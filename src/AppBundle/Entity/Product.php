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
use Sylius\Component\Core\Model\Product as BaseProduct;

/**
 * Extended Sylius product entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class Product extends BaseProduct
{
    use TagAssociationTrait, ProductDefaultTrait;

    /**
     * @var string
     */
    public const SHOPPABLE_ASSOCIATION_CODE = 'colors';

    /**
     * @var ArrayCollection|Tag[]
     */
    protected $tags;

    /**
     * @var Manufacturer|null
     */
    private $manufacturer;

    /**
     * @var HeurekaTaxonomy|null
     */
    private $heurekaTaxonomy;

    /**
     * @var GoogleTaxonomy|null
     */
    private $googleTaxonomy;

    public function __construct()
    {
        parent::__construct();

        $this->initializeTagsCollection();
        $this->initializeProductDefaultCollection();
    }

    /**
     * @return Manufacturer|null
     */
    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    /**
     * @param Manufacturer|null
     */
    public function setManufacturer(?Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return HeurekaTaxonomy|null
     */
    public function getHeurekaTaxonomy(): ?HeurekaTaxonomy
    {
        return $this->heurekaTaxonomy;
    }

    /**
     * @param HeurekaTaxonomy|null
     */
    public function setHeurekaTaxonomy(?HeurekaTaxonomy $heurekaTaxonomy): void
    {
        $this->heurekaTaxonomy = $heurekaTaxonomy;
    }

    /**
     * @return GoogleTaxonomy|null
     */
    public function getGoogleTaxonomy(): ?GoogleTaxonomy
    {
        return $this->googleTaxonomy;
    }

    /**
     * @param GoogleTaxonomy|null
     */
    public function setGoogleTaxonomy(?GoogleTaxonomy $googleTaxonomy): void
    {
        $this->googleTaxonomy = $googleTaxonomy;
    }

    /**
     * Check if product has at least one variant tracked in inventory
     *
     * @return bool|null
     **/
    public function hasAtLeastOneVariantTracked(): ?bool
    {
        if (!$this->hasVariants()) {
            return false;
        }
        // Check if at least one variant is tracked
        foreach ($this->variants as $variant) {
            if ($variant->isTracked()) {
                return true;
            }
        }

        // Product doesn't have at least one tracked variant
        return null;
    }

    /**
     * Get first tracked variant for Product entity
     *
     * @return ProductVariant|null
     **/
    public function getFirstTrackedVariant(): ?ProductVariant
    {
        // Get first tracked variant
        foreach ($this->variants as $variant) {
            if ($variant->isTracked()) {
                return $variant;
            }
        }

        // Product doesn't have any tracked variant
        return null;
    }

    /**
     * Temporary remove options matching as a available option
     */
    public static function getVariantSelectionMethodLabels(): array
    {
        return [
            // self::VARIANT_SELECTION_CHOICE => 'sylius.ui.variant_choice',
            self::VARIANT_SELECTION_MATCH => 'sylius.ui.options_matching',
        ];
    }

    /**
     * Check if product has association to be printed above price (color)
     *
     * @return bool
     **/
    public function hasShoppableAssociation(): ?bool
    {
        if ($this->getAssociations()) {
            foreach ($this->getAssociations() as $assoc) {
                if ($assoc->getType()->getCode() == self::SHOPPABLE_ASSOCIATION_CODE) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get shoppable association
     *
     * @return bool
     **/
    public function getShoppableAssociationCode(): ?string
    {
        return self::SHOPPABLE_ASSOCIATION_CODE;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariants(): bool
    {
        if (!$this->getVariants()->isEmpty()) {
            $disabled = 0;
            foreach ($this->getVariants() as $variant) {
                if (!$variant->isEnabled()) {
                    ++$disabled;
                }
            }

            return $disabled !== $this->getVariants()->count();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariants(): Collection
    {
        return $this->variants->filter(function (ProductVariant $variant) {
            return $variant->isEnabled();
        });
    }
}
