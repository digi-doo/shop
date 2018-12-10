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

use AppBundle\Entity\AppAdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Calculator\UndefinedShippingMethodException;
use Webmozart\Assert\Assert;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class PaymentChargesProcessor implements OrderProcessorInterface
{
    /**
     * @var FactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @param FactoryInterface $adjustmentFactory
     */
    public function __construct(FactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        // Remove all payment adjustments, we recalculate everything from scratch.
        $order->removeAdjustments(AppAdjustmentInterface::PAYMENT_ADJUSTMENT);

        foreach ($order->getPayments() as $payment) {
            try {
                $paymentCharge = $payment->getMethod()->getPrice();

                $adjustment = $this->adjustmentFactory->createNew();
                $adjustment->setType(AppAdjustmentInterface::PAYMENT_ADJUSTMENT);
                $adjustment->setAmount($paymentCharge);
                $adjustment->setLabel($payment->getMethod()->getName());

                $order->addAdjustment($adjustment);
            } catch (UndefinedShippingMethodException $exception) {
            }
        }
    }
}
