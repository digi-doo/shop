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
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;
use Webmozart\Assert\Assert;

class OrderPaymentTaxesApplicator implements OrderTaxesApplicatorInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @var TaxRateResolverInterface
     */
    private $taxRateResolver;

    /**
     * @param CalculatorInterface $calculator
     * @param AdjustmentFactoryInterface $adjustmentFactory
     * @param TaxRateResolverInterface $taxRateResolver
     */
    public function __construct(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        TaxRateResolverInterface $taxRateResolver
    ) {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->taxRateResolver = $taxRateResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        $paymentTotal = $order->getPaymentTotal();
        if (0 === $paymentTotal) {
            return;
        }

        $taxRate = $this->taxRateResolver->resolve($this->getPaymentMethod($order), ['zone' => $zone]);
        if (null === $taxRate) {
            return;
        }

        $taxAmount = $this->calculator->calculate($paymentTotal, $taxRate);
        if (0.00 === $taxAmount) {
            return;
        }

        $this->addAdjustment($order, (int) $taxAmount, $taxRate->getLabel(), $taxRate->isIncludedInPrice());
    }

    /**
     * @param OrderInterface $order
     * @param int $taxAmount
     * @param string $label
     * @param bool $included
     */
    private function addAdjustment(OrderInterface $order, int $taxAmount, string $label, bool $included): void
    {
        /** @var AppAdjustmentInterface $paymentTaxAdjustment */
        $paymentTaxAdjustment = $this->adjustmentFactory
            ->createWithData(AppAdjustmentInterface::PAYMENT_TAX_ADJUSTMENT, $label, $taxAmount, $included);

        $order->addAdjustment($paymentTaxAdjustment);
    }

    /**
     * @param OrderInterface $order
     *
     * @return PaymentMethodInterface
     *
     * @throws \LogicException
     */
    private function getPaymentMethod(OrderInterface $order): PaymentMethodInterface
    {
        /** @var bool|PaymentMethodInterface $payment */
        $payment = $order->getPayments()->first();
        if (false === $payment) {
            throw new \LogicException('Order should have at least one payment.');
        }

        $method = $payment->getMethod();

        /** @var PaymentMethodInterface $method */
        Assert::isInstanceOf($method, PaymentMethodInterface::class);

        return $method;
    }
}
