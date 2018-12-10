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

use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Webmozart\Assert\Assert;

final class OrderInventoryOperator implements OrderInventoryOperatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function cancel(OrderInterface $order): void
    {
        if (in_array(
            $order->getPaymentState(),
            [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_REFUNDED],
            true
        )) {
            $this->giveBack($order);

            return;
        }

        $this->release($order);
    }

    /**
     * {@inheritdoc}
     */
    public function hold(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            // Continue if variant is not tracked or is synchronized with external system
            if (!$variant->isTracked() || $variant->getExternalCode() !== null) {
                continue;
            }

            $variant->setOnHold($variant->getOnHold() + $orderItem->getQuantity());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sell(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            // Continue if variant is not tracked or is synchronized with external system
            if (!$variant->isTracked() || $variant->getExternalCode() !== null) {
                continue;
            }

            // Assert::greaterThanEq(
            //     ($variant->getOnHold() - $orderItem->getQuantity()),
            //     -1000000,
            //     sprintf(
            //         'Not enough units to decrease on hold quantity from the inventory of a variant "%s".',
            //         $variant->getName()
            //     )
            // );

            // Assert::greaterThanEq(
            //     ($variant->getOnHand() - $orderItem->getQuantity()),
            //     -1000000,
            //     sprintf(
            //         'Not enough units to decrease on hand quantity from the inventory of a variant "%s".',
            //         $variant->getName()
            //     )
            // );

            $variant->setOnHold($variant->getOnHold() - $orderItem->getQuantity());
            $variant->setOnHand($variant->getOnHand() - $orderItem->getQuantity());
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @throws \InvalidArgumentException
     */
    private function release(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            // Continue if variant is not tracked or is synchronized with external system
            if (!$variant->isTracked() || $variant->getExternalCode() !== null) {
                continue;
            }

            // Assert::greaterThanEq(
            //     ($variant->getOnHold() - $orderItem->getQuantity()),
            //     -1000000,
            //     sprintf(
            //         'Not enough units to decrease on hold quantity from the inventory of a variant "%s".',
            //         $variant->getName()
            //     )
            // );

            $variant->setOnHold($variant->getOnHold() - $orderItem->getQuantity());
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function giveBack(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            // Continue if variant is not tracked or is synchronized with external system
            if (!$variant->isTracked() || $variant->getExternalCode() !== null) {
                continue;
            }

            $variant->setOnHand($variant->getOnHand() + $orderItem->getQuantity());
        }
    }
}