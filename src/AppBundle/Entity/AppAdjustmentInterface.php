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

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\AdjustmentInterface as BaseAdjustmentInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
interface AppAdjustmentInterface extends BaseAdjustmentInterface
{
    public const PAYMENT_ADJUSTMENT = 'payment';
    public const PAYMENT_TAX_ADJUSTMENT = 'payment_tax';
    public const ORDER_PAYMENT_PROMOTION_ADJUSTMENT = 'order_payment_promotion';
    public const ORDER_GIFT_PROMOTION_ADJUSTMENT = 'order_gift_promotion';
}
