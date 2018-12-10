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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderAdjustmentsClearer implements OrderProcessorInterface
{
    /**
     * @var array
     */
    private static $adjustmentsToRemove = [
        AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT,
        AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
        AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT,
        AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT,
        AdjustmentInterface::TAX_ADJUSTMENT,
        AppAdjustmentInterface::PAYMENT_ADJUSTMENT,
        AppAdjustmentInterface::PAYMENT_TAX_ADJUSTMENT,
    ];

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order): void
    {
        foreach (self::$adjustmentsToRemove as $type) {
            $order->removeAdjustmentsRecursively($type);
        }
    }
}
