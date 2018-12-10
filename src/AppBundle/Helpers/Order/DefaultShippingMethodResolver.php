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
use Sylius\Component\Core\Model\ShipmentInterface as CoreShipmentInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Webmozart\Assert\Assert;

class DefaultShippingMethodResolver implements DefaultShippingMethodResolverInterface
{
    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     */
    public function __construct(ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface
    {
        /** @var CoreShipmentInterface $shipment */
        Assert::isInstanceOf($shipment, CoreShipmentInterface::class);

        // Don't select first available shipping method in cart
        if ($shipment->getOrder()->getCheckoutState() == OrderCheckoutStates::STATE_CART) {
            throw new UnresolvedDefaultShippingMethodException();
        }

        /** @var ChannelInterface $channel */
        $channel = $shipment->getOrder()->getChannel();

        $shippingMethods = $this->shippingMethodRepository->findEnabledForChannel($channel);
        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }

        return $shippingMethods[0];
    }
}
