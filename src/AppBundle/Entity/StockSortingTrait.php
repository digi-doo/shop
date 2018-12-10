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

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
trait StockSortingTrait
{
    /**
     * @var bool
     */
    private $stockSortingEnabled = true;

    /**
     * @param bool $stockSortingEnabled
     */
    public function setStockSortingEnabled(?bool $stockSortingEnabled): void
    {
        $this->stockSortingEnabled = $stockSortingEnabled;
    }

    /**
     * @return bool|null
     */
    public function isStockSortingEnabled(): ?bool
    {
        return $this->stockSortingEnabled;
    }
}
