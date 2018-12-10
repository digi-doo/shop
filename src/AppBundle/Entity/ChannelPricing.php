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

use Sylius\Component\Core\Model\ChannelPricing as BaseChannelPricing;

/**
 * AppBundle extended sylius channel pricing entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ChannelPricing extends BaseChannelPricing
{
    private const LIMIT_STOCK = 'stock';
    private const LIMIT_DATETIME = 'datetime';

    /**
     * @var float
     */
    private $discount = 0.00;

    /**
     * @var string|null
     */
    private $discountLimitType;

    /**
     * @var \DateTimeInterface|null
     */
    private $discountFrom;

    /**
     * @var \DateTimeInterface|null
     */
    private $discountTo;

    /**
     * @return float|null
     */
    public function getRealDiscount(): ?float
    {
        return (float) $this->discount;
    }

    /**
     * @param float $discount percent
     */
    public function setRealDiscount(?float $discount): void
    {
        $this->discount = $discount;
    }

    /**
     * @return float|null
     */
    public function getDiscount(): ?float
    {
        if ($this->getDiscountLimitType() && $this->discount !== null) {
            return $this->resolveDiscount();
        }

        return (float) $this->discount;
    }

    /**
     * @param float $discount percent
     */
    public function setDiscount(?float $discount): void
    {
        $this->discount = $discount;
    }

    /**
     * @return int
     */
    public function getDiscountedPrice(): ?int
    {
        if (!$this->hasDiscount()) {
            return $this->getPrice();
        }

        return (int) round($this->price - ($this->price * ($this->getDiscount())));
    }

    /**
     * @return bool
     */
    public function hasDiscount(): bool
    {
        return $this->getDiscount() ? true : false;
    }

    /**
     * Override default getPrice method to use discounted price
     *
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->hasDiscount() ? $this->getDiscountedPrice() : $this->price;
    }

    /**
     * Get default price
     *
     * @return int|null
     */
    public function getDefaultPrice(): ?int
    {
        return $this->price;
    }

    /**
     * Set default price
     *
     * @param int $defaultPrice
     */
    public function setDefaultPrice(?int $defaultPrice): void
    {
        $this->price = $defaultPrice;
    }

    /**
     * Get discount limit type
     *
     * @return string|null
     */
    public function getDiscountLimitType(): ?string
    {
        return $this->discountLimitType;
    }

    /**
     * Set discount limit type
     *
     * @param string|null
     */
    public function setDiscountLimitType(?string $discountLimitType): void
    {
        $this->discountLimitType = $discountLimitType;
    }

    /**
     * Get discount from
     *
     * @return \DateTimeInterface|null
     */
    public function getDiscountFrom(): ?\DateTimeInterface
    {
        return $this->discountFrom;
    }

    /**
     * Set discount from
     *
     * @param \DateTimeInterface|null
     */
    public function setDiscountFrom(?\DateTimeInterface $discountFrom): void
    {
        $this->discountFrom = $discountFrom;
    }

    /**
     * Get discount to
     *
     * @return \DateTimeInterface|null
     */
    public function getDiscountTo(): ?\DateTimeInterface
    {
        return $this->discountTo;
    }

    /**
     * Set discount to
     *
     * @param \DateTimeInterface|null
     */
    public function setDiscountTo(?\DateTimeInterface $discountTo): void
    {
        $this->discountTo = $discountTo;
    }

    /**
     * @return float|null
     */
    private function resolveDiscount(): ?float
    {
        $variant = $this->getProductVariant();
        $discountLimitType = $this->getDiscountLimitType();

        if ($discountLimitType === self::LIMIT_STOCK) {
            return $variant->getAvailableStockCount() > 0 ? (float) $this->discount : null;
        }

        if ($discountLimitType === self::LIMIT_DATETIME && ($this->getDiscountFrom() || $this->getDiscountTo())) {
            $date = new \DateTime();
            if ($this->getDiscountFrom() && $this->getDiscountTo()) {
                return $date >= $this->getDiscountFrom() && $date <= $this->getDiscountTo() ? (float) $this->discount : null;
            }
            if ($this->getDiscountFrom()) {
                return $date >= $this->getDiscountFrom() ? (float) $this->discount : null;
            }
            if ($this->getDiscountTo()) {
                return $date <= $this->getDiscountTo() ? (float) $this->discount : null;
            }
        }

        return (float) $this->discount;
    }
}
