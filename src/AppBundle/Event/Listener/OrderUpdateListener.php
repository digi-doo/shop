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

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class OrderUpdateListener
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
     * @param ResourceControllerEvent $event
     */
    public function sendStornoEmail(ResourceControllerEvent $event): void
    {
        $this->sendEmail($event, 'cancelled_order');
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function sendProcessEmail(ResourceControllerEvent $event): void
    {
        $this->sendEmail($event, 'processed_order');
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function sendPaidEmail(ResourceControllerEvent $event): void
    {
        $this->sendEmail($event, 'paid_order');
    }

    /**
     * @param ResourceControllerEvent $event
     * @param string $type
     */
    private function sendEmail(ResourceControllerEvent $event, string $type): void
    {
        // Check if route is with email
        if (!$this->withEmail()) {
            return;
        }
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $data['order'] = $order;
        $this->emailSender->send(
            $type,
            [$order->getCustomer()->getEmail()],
            $data
        );
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
