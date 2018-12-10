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

namespace AppBundle\Form\Type\Filter;

use AppBundle\Component\Order\AppOrderShippingStates;
use AppBundle\Entity\OrderInterface as AppOrderIterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderStateFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state', ChoiceType::class, [
                'label' => 'sylius.ui.order_state',
                'choices' => [
                    'sylius.ui.new' => OrderInterface::STATE_NEW,
                    'sylius.ui.processed' => AppOrderIterface::STATE_PROCESSED,
                    'sylius.ui.expedited' => AppOrderIterface::STATE_EXPEDITED,
                    'sylius.ui.issued' => AppOrderIterface::STATE_ISSUED,
                    'sylius.ui.fulfilled' => OrderInterface::STATE_FULFILLED,
                    'sylius.ui.cancelled' => OrderInterface::STATE_CANCELLED,
                    'sylius.ui.all_orders' => OrderInterface::STATE_CART,
                ],
            ])
            ->add('paymentState', ChoiceType::class, [
                'label' => 'sylius.ui.payment_state',
                'placeholder' => '---',
                'required' => false,
                'choices' => [
                    'sylius.ui.awaiting_payment' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
                    'sylius.ui.paid' => OrderPaymentStates::STATE_PAID,
                    'sylius.ui.cancelled' => OrderPaymentStates::STATE_CANCELLED,
                ],
            ])
            ->add('shippingState', ChoiceType::class, [
                'label' => 'sylius.ui.shipping_state',
                'placeholder' => '---',
                'required' => false,
                'choices' => [
                    'sylius.ui.ready_waiting_for_issue' => OrderShippingStates::STATE_READY,
                    'sylius.ui.issued_transport' => AppOrderShippingStates::STATE_ISSUED,
                    'sylius.ui.shipped' => OrderShippingStates::STATE_SHIPPED,
                    'sylius.ui.cancelled' => OrderShippingStates::STATE_CANCELLED,
                ],
            ]);
    }
}
