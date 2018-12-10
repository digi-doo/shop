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

use AppBundle\Component\Order\AppOrderShippingStates;
use AppBundle\Component\Order\AppOrderShippingTransitions;
use AppBundle\Entity\AppShipmentInterface;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\OrderShippingTransitions;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class OrderShippingStateResolver implements StateResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(FactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(BaseOrderInterface $order): void
    {
        if (OrderShippingStates::STATE_SHIPPED === $order->getShippingState()) {
            return;
        }
        /** @var StateMachineInterface $stateMachine */
        $stateMachine = $this->stateMachineFactory->get($order, OrderShippingTransitions::GRAPH);

        if ($this->allShipmentsInStateButOrderStateNotUpdated($order, AppShipmentInterface::STATE_ISSUED, AppOrderShippingStates::STATE_ISSUED)) {
            $stateMachine->apply(AppOrderShippingTransitions::TRANSITION_ISSUE);
        }

        if ($this->allShipmentsInStateButOrderStateNotUpdated($order, ShipmentInterface::STATE_SHIPPED, OrderShippingStates::STATE_SHIPPED)) {
            $stateMachine->apply(OrderShippingTransitions::TRANSITION_SHIP);
        }

        if ($this->isPartiallyShippedButOrderStateNotUpdated($order)) {
            $stateMachine->apply(OrderShippingTransitions::TRANSITION_PARTIALLY_SHIP);
        }
    }

    /**
     * @param OrderInterface $order
     * @param string $shipmentState
     *
     * @return int
     */
    private function countOrderShipmentsInState(OrderInterface $order, string $shipmentState): int
    {
        $shipments = $order->getShipments();

        return $shipments
            ->filter(function (ShipmentInterface $shipment) use ($shipmentState) {
                return $shipment->getState() === $shipmentState;
            })
            ->count()
        ;
    }

    /**
     * @param OrderInterface $order
     * @param string $shipmentState
     * @param string $orderShippingState
     *
     * @return bool
     */
    private function allShipmentsInStateButOrderStateNotUpdated(
        OrderInterface $order,
        string $shipmentState,
        string $orderShippingState
    ): bool {
        $shipmentInStateAmount = $this->countOrderShipmentsInState($order, $shipmentState);
        $shipmentAmount = $order->getShipments()->count();

        return $shipmentAmount === $shipmentInStateAmount && $orderShippingState !== $order->getShippingState();
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function isPartiallyShippedButOrderStateNotUpdated(OrderInterface $order): bool
    {
        $shipmentInShippedStateAmount = $this->countOrderShipmentsInState($order, ShipmentInterface::STATE_SHIPPED);
        $shipmentAmount = $order->getShipments()->count();

        return
            1 <= $shipmentInShippedStateAmount &&
            $shipmentInShippedStateAmount < $shipmentAmount &&
            OrderShippingStates::STATE_PARTIALLY_SHIPPED !== $order->getShippingState()
        ;
    }
}
