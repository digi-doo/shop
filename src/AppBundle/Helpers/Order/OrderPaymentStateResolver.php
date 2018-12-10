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

use Doctrine\Common\Collections\Collection;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;

final class OrderPaymentStateResolver implements StateResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(
        FactoryInterface $stateMachineFactory,
        $eventDispatcher,
        RequestStack $requestStack
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order): void
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
        $targetTransition = $this->getTargetTransition($order);

        if (null !== $targetTransition) {
            $this->applyTransition($stateMachine, $targetTransition);
        }

        // Manually trigger payment event only if we are returning from payment gateway
        if ($this->requestStack->getCurrentRequest() !== null && $this->requestStack->getCurrentRequest()->get('_route') === 'sylius_shop_order_after_pay') {
            $this->eventDispatcher->dispatch(
                'sylius.payment.post_complete',
                new ResourceControllerEvent($order->getPayments()->first())
            );
        }
    }

    /**
     * @param StateMachineInterface $stateMachine
     * @param string $transition
     */
    private function applyTransition(StateMachineInterface $stateMachine, string $transition): void
    {
        if ($stateMachine->can($transition)) {
            $stateMachine->apply($transition);
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return string|null
     */
    private function getTargetTransition(OrderInterface $order): ?string
    {
        $refundedPaymentTotal = 0;
        $refundedPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_REFUNDED);

        foreach ($refundedPayments as $payment) {
            $refundedPaymentTotal += $payment->getAmount();
        }

        if (0 < $refundedPayments->count() && $refundedPaymentTotal >= $order->getTotal()) {
            return OrderPaymentTransitions::TRANSITION_REFUND;
        }

        if ($refundedPaymentTotal < $order->getTotal() && 0 < $refundedPaymentTotal) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND;
        }

        $completedPaymentTotal = 0;
        $completedPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_COMPLETED);

        foreach ($completedPayments as $payment) {
            $completedPaymentTotal += $payment->getAmount();
        }

        if (
            (0 < $completedPayments->count() && $completedPaymentTotal >= $order->getTotal()) ||
            $order->getPayments()->isEmpty()
        ) {
            return OrderPaymentTransitions::TRANSITION_PAY;
        }

        if ($completedPaymentTotal < $order->getTotal() && 0 < $completedPaymentTotal) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY;
        }

        return null;
    }

    /**
     * @param OrderInterface $order
     * @param string $state
     *
     * @return Collection|PaymentInterface[]
     */
    private function getPaymentsWithState(OrderInterface $order, string $state): Collection
    {
        return $order->getPayments()->filter(function (PaymentInterface $payment) use ($state) {
            return $state === $payment->getState();
        });
    }
}
