<?php

/*
 * This file is part of the Digi Doo s.r.o. sshop project.
 *
 * (c) Digi Doo s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\OrderInterface;
use Autodevelo\ApiClient\Clients\Structure\Response\OrderResponse;
use FOS\RestBundle\View\View;
use Nette\Utils\ArrayHash;
use Sylius\Bundle\CoreBundle\Controller\OrderController as BaseOrderController;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Order\Model\OrderInterface as SyliusOrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class OrderController extends BaseOrderController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function bankTransferNotificationAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $order = $this->findOr404($configuration);

        // Proceed only if it is not API request
        if ($configuration->isHtmlRequest() && in_array($request->getMethod(), ['POST'], true)) {
            try {
                $this->createEmailCacheDir();
                $mailCacheDir = rtrim($this->getParameter('kernel.cache_dir'), '/') . '/_mail-cache';

                /** Send order to Develo app */
                $orderClient = $this->get('czende.develo_shop_plugin.api')->createOrder();
                $response = $orderClient->readOrderByImportId($order->getNumber());

                $attachments = [];
                if (!empty($response->getInvoices())) {
                    $invoiceClient = $this->get('czende.develo_shop_plugin.api')->createInvoice();
                    foreach ($response->getInvoices() as $invoice) {
                        if ($invoice->getType() !== 'proforma') {
                            continue;
                        }
                        // Save invoice
                        $invoiceClient->readPdfToFile($invoice->getId(), $mailCacheDir . '/faktura-' . $invoice->getNumber() . '.pdf');
                        $attachments[] = $mailCacheDir . '/faktura-' . $invoice->getNumber() . '.pdf';
                    }

                    if (!empty($attachments)) {
                        $this->get('sylius.email_sender')->send('order_bank_transfer_notification', [$order->getCustomer()->getEmail()], ['order' => $order], $attachments);
                        $this->get('session')->getFlashBag()->add('success', 'sylius.order.order_bank_transfer_notification_done');
                    } else {
                        $this->get('session')->getFlashBag()->add('error', 'sylius.order.order_bank_transfer_notification_no_proformas');
                    }
                }

                $this->removeEmailCacheDir();
            } catch (\Exception $e) {
                // Add error flashmessage
                $this->get('session')->getFlashBag()->add('error', 'sylius.order.order_bank_transfer_notification_general_error');
            }
        }

        return $this->redirectHandler->redirectToResource($configuration, $order);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function develoUpdateAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $order = $this->findOr404($configuration);

        // Proceed only if it is API request
        if (!$configuration->isHtmlRequest() && in_array($request->getMethod(), ['POST'], true)) {
            $develoOrder = json_decode($request->getContent(), true);

            Assert::isArray($develoOrder);

            $develoOrder = new OrderResponse(ArrayHash::from($develoOrder));
            $order = $this->updateOrder($order, $develoOrder);

            // Order has been changed
            if ($order !== null) {
                /** @var ResourceControllerEvent $event */
                $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $order);

                try {
                    $this->resourceUpdateHandler->handle($order, $configuration, $this->manager);
                } catch (UpdateHandlingException $exception) {
                    return $this->viewHandler->handle(
                        $configuration,
                        View::create(null, $exception->getApiResponseCode())
                    );
                }

                $postEvent = $this->eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $order);
            }

            $view = $configuration->getParameters()->get('return_content', false) ? View::create($order, Response::HTTP_OK) : View::create(null, Response::HTTP_NO_CONTENT);

            return $this->viewHandler->handle($configuration, $view);
        }

        return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_BAD_REQUEST));
    }

    /**
     * Prepare dir for email attachments
     */
    protected function createEmailCacheDir(): void
    {
        $mailCacheDir = rtrim($this->getParameter('kernel.cache_dir'), '/') . '/_mail-cache';
        $fs = new Filesystem();
        $fs->mkdir($mailCacheDir, 0700);
    }

    /**
     * Clear dir for email attachments
     */
    protected function removeEmailCacheDir(): void
    {
        $mailCacheDir = rtrim($this->getParameter('kernel.cache_dir'), '/') . '/_mail-cache';
        $fs = new Filesystem();
        $fs->remove([$mailCacheDir]);
    }

    /**
     * Check order changes
     *
     * @param  ResourceInterface|null $order
     * @param  OrderResponse     $develoOrder
     *
     * @return ResourceInterface
     */
    private function updateOrder(ResourceInterface $order, OrderResponse $develoOrder): ?ResourceInterface
    {
        $updatedOrder = false;
        $payment = $order->getPayments()->first();

        if ($develoOrder->isOrderInvoiced()) {
            $updatedOrder = true;
            $order->setPaymentState(OrderPaymentStates::STATE_PAID);
            $payment->setState(PaymentInterface::STATE_COMPLETED);

            $this->get('czende.rabbit.producer.order_label')->add($order);
        }

        if ($develoOrder->isOrderIssued() && $order->getState() !== SyliusOrderInterface::STATE_FULFILLED) {
            $updatedOrder = true;
            $order->setState(OrderInterface::STATE_ISSUED);

            $this->get('czende.rabbit.producer.order_label')->add($order);
        }

        if ($develoOrder->isInactive() && $order->getState() !== SyliusOrderInterface::STATE_CANCELLED) {
            $updatedOrder = true;
            $stateMachine = $this->get('sm.factory')->get($order, OrderTransitions::GRAPH);
            $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL);
        }

        $this->saveInvoiceDocuments($order->getNumber(), $develoOrder);
        $this->saveStockDocuments($order->getNumber(), $develoOrder);

        return $updatedOrder ? $order : null;
    }

    /**
     * Save order invoice documents if any
     *
     * @param string $orderNumber
     * @param DeveloOrderResponse $response
     */
    private function saveInvoiceDocuments(string $orderNumber, OrderResponse $response): void
    {
        if (!empty($response->getInvoices())) {
            foreach ($response->getInvoices() as $invoice) {
                if ($this->get('develo.repository.order_document')->findOneBy(['documentId' => $invoice->getId(), 'orderNumber' => $orderNumber]) !== null) {
                    continue;
                }
                $document = $this->get('develo.factory.order_document')->createNew();
                $document->setOrderNumber($orderNumber);
                $document->setDocumentType('invoice-' . $invoice->getType());
                $document->setDocumentNumber($invoice->getNumber());
                $document->setDocumentId($invoice->getId());

                $this->get('develo.manager.order_document')->persist($document);
            }

            $this->get('develo.manager.order_document')->flush();
        }
    }

    /**
     * Save order documents if any
     *
     * @param string $orderNumber
     * @param OrderResponse $response
     */
    private function saveStockDocuments(string $orderNumber, OrderResponse $response): void
    {
        if (!empty($response->getOutcomingStockDocuments())) {
            foreach ($response->getOutcomingStockDocuments() as $stockDocument) {
                if ($this->get('develo.repository.order_document')->findOneBy(['documentId' => $stockDocument->getId(), 'orderNumber' => $orderNumber]) !== null) {
                    continue;
                }
                $document = $this->get('develo.factory.order_document')->createNew();
                $document->setOrderNumber($orderNumber);
                $document->setDocumentType('stock-' . $stockDocument->getType());
                $document->setDocumentNumber($stockDocument->getNumber());
                $document->setDocumentId($stockDocument->getId());

                $this->get('develo.manager.order_document')->persist($document);
            }

            $this->get('develo.manager.order_document')->flush();
        }
    }
}
