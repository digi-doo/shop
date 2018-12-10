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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentInterface as CorePaymentInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Webmozart\Assert\Assert;

class DefaultPaymentMethodResolver implements DefaultPaymentMethodResolverInterface
{
    /**
     * @var PaymentMethodRepositoryInterface
     */
    protected $paymentMethodRepository;

    /**
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     */
    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnresolvedDefaultPaymentMethodException
     */
    public function getDefaultPaymentMethod(PaymentInterface $payment): PaymentMethodInterface
    {
        /** @var CorePaymentInterface $payment */
        Assert::isInstanceOf($payment, CorePaymentInterface::class);

        // Don't select first available payment method in cart
        // if ($payment->getOrder()->getCheckoutState() == OrderCheckoutStates::STATE_CART) {
        //     throw new UnresolvedDefaultPaymentMethodException();
        // }

        /** @var ChannelInterface $channel */
        $channel = $payment->getOrder()->getChannel();

        $paymentMethods = $this->paymentMethodRepository->findEnabledForChannel($channel);
        if (empty($paymentMethods)) {
            throw new UnresolvedDefaultPaymentMethodException();
        }

        return $paymentMethods[0];
    }
}
