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

use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;

interface AppShipmentInterface extends BaseShipmentInterface
{
    public const STATE_ISSUED = 'issued_transport';
}
