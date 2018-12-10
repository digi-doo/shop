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

use BitBag\SyliusCmsPlugin\Entity\Block as BaseBlock;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class Block extends BaseBlock
{
    use TimestampableTrait;

    /**
     * @var Tag|null
     */
    private $tag;

    /**
     * @var Taxon|null
     */
    private $taxon;

    /** @var string */
    private $tabType = 'taxon';

    /**
     * @return Tag|null
     */
    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    /**
     * @param Tag|null
     */
    public function setTag(?Tag $tag): void
    {
        $this->tag = $tag;
    }

    /**
     * @return Taxon|null
     */
    public function getTaxon(): ?Taxon
    {
        return $this->taxon;
    }

    /**
     * @param Tag|null
     */
    public function setTaxon(?Taxon $taxon): void
    {
        $this->taxon = $taxon;
    }

    /**
     * @return $tabType
     */
    public function getTabType(): ?string
    {
        return $this->tabType;
    }

    /**
     * @param string $tabType
     */
    public function setTabType(?string $tabType): void
    {
        $this->tabType = $tabType;
    }
}
