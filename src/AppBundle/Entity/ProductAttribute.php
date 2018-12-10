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

use Sylius\Component\Product\Model\ProductAttribute as BaseProductAttribute;

class ProductAttribute extends BaseProductAttribute
{
    /**
     * @var bool
     */
    protected $filterable = true;

    /**
     * @var bool
     */
    protected $rangeable = false;

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

    /**
     * @return bool
     */
    public function isRangeable(): bool
    {
        return $this->rangeable;
    }

    /**
     * @param bool $rangeable
     */
    public function setRangeable(?bool $rangeable): void
    {
        $this->rangeable = (bool) $rangeable;
    }
}
