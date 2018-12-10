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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
trait ProductDefaultTrait
{
    /**
     * @var ArrayCollection
     */
    protected $productDefaults;

    /**
     * Initialize product default collection
     *
     * @return ArrayColletion
     */
    public function initializeProductDefaultCollection()
    {
        $this->productDefaults = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductDefaults(): ?Collection
    {
        return $this->productDefaults;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductDefaultForChannel(ChannelInterface $channel): ?ProductDefault
    {
        if ($this->productDefaults->containsKey($channel->getCode())) {
            return $this->productDefaults->get($channel->getCode());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductDefaultForChannel(ChannelInterface $channel): bool
    {
        return null !== $this->getProductDefaultForChannel($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductDefault(ProductDefault $productDefault): bool
    {
        return $this->productDefaults->contains($productDefault);
    }

    /**
     * {@inheritdoc}
     */
    public function addProductDefault(ProductDefault $productDefault): void
    {
        if (!$this->hasProductDefault($productDefault)) {
            $productDefault->setProduct($this);
            $this->productDefaults->set($productDefault->getChannelCode(), $productDefault);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductDefault(ProductDefault $productDefault): void
    {
        if ($this->hasProductDefault($productDefault)) {
            $productDefault->setProduct(null);
            $this->productDefaults->remove($productDefault->getChannelCode());
        }
    }
}
