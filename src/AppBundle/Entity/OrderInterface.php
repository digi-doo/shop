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

use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

interface OrderInterface extends BaseOrderInterface
{
    public const STATE_PROCESSED = 'processed';
    public const STATE_EXPEDITED = 'expedited';
    public const STATE_ISSUED = 'issued';
}
