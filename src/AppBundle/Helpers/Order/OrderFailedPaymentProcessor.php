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

namespace AppBundle\Helpers\Order;

use Sylius\Bundle\ResourceBundle\Controller\EventDispatcher;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class OrderFailedPaymentProcessor implements OrderProcessorInterface
{
    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Set email sender
     *
     * @param SenderInterface $emailSender
     */
    public function __construct(SenderInterface $emailSender, $eventDispatcher, RequestStack $requestStack)
    {
        $this->emailSender = $emailSender;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
    }

    /**
     * Process storno payment after redirect from failed payment gateway.
     *
     * @param OrderInterface $order
     */
    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        try {
            // Cancel all order states
            $order->setState(OrderInterface::STATE_CANCELLED);
            $order->setPaymentState(PaymentInterface::STATE_CANCELLED);
            $order->setShippingState(ShipmentInterface::STATE_CANCELLED);
            $order->getShipments()->first()->setState(ShipmentInterface::STATE_CANCELLED);

            // Manually trigger order update event only if we are returning from the payment gateway
            if ($this->requestStack->getCurrentRequest() !== null && $this->requestStack->getCurrentRequest()->get('_route') === 'sylius_shop_order_after_pay') {
                $this->eventDispatcher->dispatch(
                    'sylius.order.pre_storno',
                    new ResourceControllerEvent($order)
                );
            }
        } catch (NotProvidedOrderPaymentException $exception) {
            return;
        }
    }
}
