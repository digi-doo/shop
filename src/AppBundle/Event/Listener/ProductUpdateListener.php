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

namespace AppBundle\Event\Listener;

use Doctrine\ORM\EntityManager;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ProductUpdateListener
{
    /**
     * @var EntityManager
     */
    private $productVariantManager;

    /**
     * @param EntityManager $productVariantManager
     */
    public function __construct(EntityManager $productVariantManager)
    {
        $this->productVariantManager = $productVariantManager;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function updateVariants(ResourceControllerEvent $event): void
    {
        /** @var ProductInterface $product */
        $product = $event->getSubject();

        /** Be sure that class is ProductInterface */
        Assert::isInstanceOf($product, ProductInterface::class);

        if ($product->hasVariants()) {
            /** Not valuable for simple product */
            if ($product->isSimple()) {
                return;
            }
            /** DIRTY!!! Get defaults for first channel */
            $defaults = $product->getProductDefaults()->first();
            foreach ($product->getVariants() as $variant) {
                if ($variant->getExternalCode() === null) {
                    if ($defaults->isMassPriceEnabled() && $defaults->getPrice() != null) {
                        $variant->getChannelPricings()->first()->setPrice($defaults->getPrice());
                    }
                    if ($defaults->isMassDiscountEnabled() && $defaults->getDiscount() != null) {
                        $variant->getChannelPricings()->first()->setDiscount($defaults->getDiscount());
                    }
                    if ($defaults->isMassOriginalPriceEnabled() && $defaults->getOriginalPrice() != null) {
                        $variant->getChannelPricings()->first()->setOriginalPrice($defaults->getOriginalPrice());
                    }
                    if ($defaults->isMassSupplierEnabled()) {
                        $variant->setSupplier($defaults->getSupplier());
                    }
                    if ($defaults->isMassTaxCategoryEnabled() && $defaults->getTaxCategory() != null) {
                        $variant->setTaxCategory($defaults->getTaxCategory());
                    }
                    if ($defaults->isMassTrackedEnabled()) {
                        $variant->setTracked($defaults->getTracked());
                    }
                    if ($defaults->isMassOnHandEnabled() && null !== $defaults->getOnHand()) {
                        $variant->setOnHand($defaults->getOnHand());
                    }

                    $this->productVariantManager->persist($variant);
                    $this->productVariantManager->flush();
                }
            }
        }
    }
}
