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

namespace AppBundle\Helpers\Stock;

use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

final class AvailabilityChecker implements AvailabilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isStockAvailable(StockableInterface $stockable): bool
    {
        return $this->isStockSufficient($stockable, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function isStockSufficient(StockableInterface $stockable, int $quantity): bool
    {
        if (!$stockable->isNegativeStock()) {
            return $quantity <= ($stockable->getOnHand() - $stockable->getOnHold());
        }

        return true;
    }
}
