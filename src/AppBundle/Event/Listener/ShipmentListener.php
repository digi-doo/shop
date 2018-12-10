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

namespace AppBundle\Event\Listener;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class ShipmentListener
{
    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Set email sender
     *
     * @param SenderInterface $emailSender
     * @param RequestStack $requestStack
     */
    public function __construct(SenderInterface $emailSender, RequestStack $requestStack)
    {
        $this->emailSender = $emailSender;
        $this->requestStack = $requestStack;
    }

    /**
     * @param GenericEvent $event
     */
    public function sendShipmentIssuedEmail(GenericEvent $event): void
    {
        $this->sendEmail($event, 'shipment_confirmation');
    }

    /**
     * @param GenericEvent $event
     */
    public function sendShipmentShippedEmail(GenericEvent $event): void
    {
        $this->sendEmail($event, 'shipped_order');
    }

    /**
     * @param ResourceControllerEvent $event
     * @param string $type
     */
    private function sendEmail(GenericEvent $event, string $type): void
    {
        // Check if route is with email
        if (!$this->withEmail()) {
            return;
        }
        $shipment = $event->getSubject();
        Assert::isInstanceOf($shipment, ShipmentInterface::class);

        $order = $shipment->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->emailSender->send($type, [$order->getCustomer()->getEmail()], [
            'shipment' => $shipment,
            'order' => $order,
        ]);
    }

    /**
     * @return bool
     */
    private function withEmail(): bool
    {
        $routeEmailQuery = $this->requestStack->getCurrentRequest()->query->get('withEmail');

        return $routeEmailQuery !== null && '1' === $routeEmailQuery;
    }
}
