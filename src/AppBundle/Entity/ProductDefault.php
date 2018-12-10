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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ProductDefault implements ResourceInterface
{
    /**
     * @var bool
     */
    protected $massDiscountEnabled = false;

    /**
     * @var bool
     */
    protected $massPriceEnabled = false;

    /**
     * @var bool
     */
    protected $massOriginalPriceEnabled = false;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Supplier
     */
    protected $supplier;

    /**
     * @var bool
     */
    protected $massSupplierEnabled = false;

    /**
     * @var TaxCategoryInterface
     */
    protected $taxCategory;

    /**
     * @var bool
     */
    protected $massTaxCategoryEnabled = false;

    /**
     * @var bool
     */
    protected $tracked = false;

    /**
     * @var bool
     */
    protected $massTrackedEnabled = false;

    /**
     * @var int
     */
    protected $onHand = 0;

    /**
     * @var bool
     */
    protected $massOnHandEnabled = false;
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $price;

    /**
     * @var float
     */
    private $discount = 0.00;

    /**
     * @var int
     */
    private $originalPrice;

    /**
     * @var string
     */
    private $channelCode;

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return (string) $this->getPrice();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set price
     *
     * @param int $price
     */
    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * Enable/disable mass price change
     *
     * @param bool $massPriceEnabled
     */
    public function setMassPriceEnabled(bool $massPriceEnabled): void
    {
        $this->massPriceEnabled = $massPriceEnabled;
    }

    /**
     * Get enabled/disabled massPriceEnabled
     *
     * @return bool
     */
    public function isMassPriceEnabled(): bool
    {
        return $this->massPriceEnabled;
    }

    /**
     * @return float percent
     */
    public function getDiscount(): ?float
    {
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
     * Enable/disable mass price change
     *
     * @param bool $massDiscountEnabled
     */
    public function setMassDiscountEnabled(bool $massDiscountEnabled): void
    {
        $this->massDiscountEnabled = $massDiscountEnabled;
    }

    /**
     * Get enabled/disabled massDiscountEnabled
     *
     * @return bool
     */
    public function isMassDiscountEnabled(): bool
    {
        return $this->massDiscountEnabled;
    }

    /**
     * Set originalPrice
     *
     * @param int $originalPrice
     */
    public function setOriginalPrice(?int $originalPrice): void
    {
        $this->originalPrice = $originalPrice;
    }

    /**
     * Get originalPrice
     *
     * @return int
     */
    public function getOriginalPrice(): ?int
    {
        return $this->originalPrice;
    }

    /**
     * Enable/disable mass OriginalPrice change
     *
     * @param bool $massPriceEnabled
     */
    public function setMassOriginalPriceEnabled(bool $massOriginalPriceEnabled): void
    {
        $this->massOriginalPriceEnabled = $massOriginalPriceEnabled;
    }

    /**
     * Get enabled/disabled mass OriginalPrice
     *
     * @return bool
     */
    public function isMassOriginalPriceEnabled(): bool
    {
        return $this->massOriginalPriceEnabled;
    }

    /**
     * Set channelCode
     *
     * @param string $channelCode
     */
    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }

    /**
     * Get channelCode
     *
     * @return string
     */
    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCategory(): ?TaxCategoryInterface
    {
        return $this->taxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCategory(?TaxCategoryInterface $category): void
    {
        $this->taxCategory = $category;
    }

    /**
     * Enable/disable mass TaxCategory change
     *
     * @param bool $massTaxCategoryEnabled
     */
    public function setMassTaxCategoryEnabled(bool $massTaxCategoryEnabled): void
    {
        $this->massTaxCategoryEnabled = $massTaxCategoryEnabled;
    }

    /**
     * Get enabled/disabled mass TaxCategory
     *
     * @return bool
     */
    public function isMassTaxCategoryEnabled(): bool
    {
        return $this->massTaxCategoryEnabled;
    }

    /**
     * @return Supplier|null
     */
    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    /**
     * @param Supplier|null
     */
    public function setSupplier(?Supplier $supplier): void
    {
        $this->supplier = $supplier;
    }

    /**
     * Enable/disable mass Supplier change
     *
     * @param bool $massSupplierEnabled
     */
    public function setMassSupplierEnabled(bool $massSupplierEnabled): void
    {
        $this->massSupplierEnabled = $massSupplierEnabled;
    }

    /**
     * Get enabled/disabled mass Supplier
     *
     * @return bool
     */
    public function isMassSupplierEnabled(): bool
    {
        return $this->massSupplierEnabled;
    }

    /**
     * Set tracked inventory
     *
     * @param bool
     */
    public function setTracked(bool $tracked): void
    {
        $this->tracked = $tracked;
    }

    /**
     * Get tracked inventory
     *
     * @return bool
     */
    public function getTracked(): bool
    {
        return $this->tracked;
    }

    /**
     * Enable/disable mass tracked change
     *
     * @param bool $massTrackedEnabled
     */
    public function setMassTrackedEnabled(bool $massTrackedEnabled): void
    {
        $this->massTrackedEnabled = $massTrackedEnabled;
    }

    /**
     * Get enabled/disabled mass tracked
     *
     * @return bool
     */
    public function isMassTrackedEnabled(): bool
    {
        return $this->massTrackedEnabled;
    }

    /**
     * Set onHand default
     *
     * @param int
     */
    public function setOnHand(?int $onHand): void
    {
        $this->onHand = $onHand;
    }

    /**
     * Get onHand default
     *
     * @return int
     */
    public function getOnHand(): ?int
    {
        return $this->onHand;
    }

    /**
     * Inventory on hand enabled/disabled
     *
     * @param bool $massOnHandEnabled
     */
    public function setMassOnHandEnabled(bool $massOnHandEnabled): void
    {
        $this->massOnHandEnabled = $massOnHandEnabled;
    }

    /**
     * Get enabled/disabled inventory on hand
     *
     * @return bool
     */
    public function isMassOnHandEnabled(): bool
    {
        return $this->massOnHandEnabled;
    }
}
