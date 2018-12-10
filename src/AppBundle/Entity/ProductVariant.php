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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Resource\Model\ToggleableTrait;

/**
 * Extended Sylius product variant entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ProductVariant extends BaseProductVariant
{
    use ToggleableTrait;

    /**
     * @var int
     */
    protected $onHand = 0;

    /**
     * @var int
     */
    protected $tracked = true;

    /**
     * @var string|null
     */
    private $ean;

    /**
     * @var Supplier|null
     */
    private $supplier;

    /**
     * @var string|null
     */
    private $externalCode;

    /**
     * @var bool
     */
    private $negativeStock = false;

    /**
     * @return string|null
     */
    public function getEan(): ?string
    {
        return $this->ean;
    }

    /**
     * @param string|null $ean
     */
    public function setEan(?string $ean): void
    {
        $this->ean = $ean;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHand(): ?int
    {
        return $this->onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHand(?int $onHand): void
    {
        $this->onHand = $onHand;
    }

    /**
     * @return int
     */
    public function getAvailableStockCount(): ?int
    {
        return $this->onHand - $this->onHold;
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
     * @return string
     */
    public function getSupplierName(): string
    {
        return $this->supplier ? $this->supplier->getName() : '---';
    }

    /**
     * @return int
     */
    public function getVariantPriceVat(ChannelInterface $channel): int
    {
        $priceNoVat = $this->getChannelPricingForChannel($channel)->getPrice() / 100;
        $taxRate = $this->getTaxCategory()->getRates()->first()->getAmount();

        return (int) round(($priceNoVat * $taxRate) + $priceNoVat);
    }

    /**
     * @return int
     */
    public function getVariantDefaultPriceVat(ChannelInterface $channel): int
    {
        $defaultPriceNoVat = $this->getChannelPricingForChannel($channel)->getDefaultPrice() / 100;
        $taxRate = $this->getTaxCategory()->getRates()->first()->getAmount();

        return (int) round(($defaultPriceNoVat * $taxRate) + $defaultPriceNoVat);
    }

    /**
     * @return string|null
     */
    public function getExternalCode(): ?string
    {
        return $this->externalCode;
    }

    /**
     * @param string|null $externalCode
     */
    public function setExternalCode(?string $externalCode): void
    {
        $this->externalCode = $externalCode;
    }

    /**
     * @return bool
     */
    public function isSynchronized(): bool
    {
        return $this->getExternalCode() ? true : false;
    }

    /**
     * @return bool
     */
    public function isNegativeStock(): ?bool
    {
        return $this->negativeStock;
    }

    /**
     * @param bool $negativeStock
     */
    public function setNegativeStock(?bool $negativeStock): void
    {
        $this->negativeStock = $negativeStock;
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock(): bool
    {
        if ($this->isNegativeStock()) {
            return true;
        }

        return 0 < $this->onHand;
    }

    /**
     * @param bool $negativeStock
     */
    public function hasDiscountInFirstChannel(): ?bool
    {
        return $this->getChannelPricings()->first()->hasDiscount();
    }
}
