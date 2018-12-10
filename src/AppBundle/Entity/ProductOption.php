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

use Sylius\Component\Product\Model\ProductOption as BaseProductOption;

/**
 * Extended Sylius product option entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ProductOption extends BaseProductOption
{
    /**
     * @var bool
     */
    protected $filterable = true;

    /**
     * @return bool
     */
    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    /**
     * @param bool $filterable
     */
    public function setFilterable(?bool $filterable): void
    {
        $this->filterable = (bool) $filterable;
    }
}
