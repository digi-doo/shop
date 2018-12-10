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

namespace AppBundle\Helpers\Feed;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class GoogleFeed
{
    /**
     * Add product variant for heureka feed export
     *
     * @param ProductVariantInterface $prod
     * @param FeedHelper $prod
     */
    public function addProductVariant(ProductVariantInterface $prodVar, GoogleFeedHelper $feedHelper): void
    {
        // Main parent product
        $prod = $prodVar->getProduct();
        $defaultText = 'Procamping - vše pro caravan, camping a volný čas';

        // Create base shop element
        $feedHelper->item = $feedHelper->createElement('item');

        // Product variant code
        $feedHelper->add('g:id', $prodVar->getCode());

        // Product variant EAN
        if ($prodVar->getEan()) {
            $feedHelper->addCData('g:gtin', $prodVar->getEan());
        }

        // Product name
        $feedHelper->addCData('title', $prod->getName());

        // Product description
        $feedHelper->addCData('g:description', $prod->getDescription() ? $prod->getDescription() : $defaultText);

        // Product URL
        $feedHelper->add('link', $feedHelper->createLinkToProduct($prod, $prodVar));

        // Product main image
        if (!$prod->getImagesByType('main')->isEmpty()) {
            $feedHelper->add('g:image_link', $feedHelper->createLinkToMainProductImage($prod));
        }

        // Product thumbnails
        if (!$prod->getImagesByType('thumbnail')->isEmpty()) {
            foreach ($prod->getImagesByType('thumbnail') as $thumbnail) {
                $feedHelper->add('g:additional_image_link', $feedHelper->createLinkToProductImage($prod, $thumbnail));
            }
        }

        // Product stock availability
        $feedHelper->add('g:availability', ($prodVar->getAvailableStockCount() > 0 ? 'in stock' : 'out of stock'));

        // Product variant price with tax
        $feedHelper->add('g:price', $feedHelper->getProductVariantDefaultPriceTax($prodVar) . ' CZK');

        // Product variant price with tax AFTER DISCOUNT
        if ($prodVar->hasDiscountInFirstChannel()) {
            $feedHelper->add('g:sale_price', $feedHelper->getProductVariantPriceTaxAfterDiscount($prodVar) . ' CZK');
        }

        // Product manufacturer
        if ($prod->getManufacturer()) {
            $feedHelper->addCData('g:brand', $prod->getManufacturer()->getName());
        }

        // Product google taxonomy
        if ($prod->getGoogleTaxonomy()) {
            $feedHelper->add('g:google_product_category', (string) $prod->getGoogleTaxonomy()->getName());
        }

        $feedHelper->add('g:product_type', $this->getSecondLevelTaxonSlugInFirstTree($prod));

        // Product condition
        $feedHelper->add('g:condition', 'new');

        // // Product variant options and its values
        // if (!$prodVar->getOptionValues()->isEmpty()) $feedHelper->addProductVariantOptions($prodVar, $feedHelper->item);

        // // Product variant delivery date
        // $feedHelper->add('DELIVERY_DATE', $feedHelper->getDeliveryDate($prodVar));

        // Add available channel deliveries methods with prices
        $shippingMethods = $feedHelper->getShippingMethods();
        if (!empty($shippingMethods)) {
            $feedHelper->addShippingMethods($shippingMethods, $feedHelper->item);
        }

        // Product CODE for variants
        if (!$prod->isSimple()) {
            $feedHelper->add('g:item_group_id', $prod->getCode());
        }

        // We don't have MPN or GTIN
        $feedHelper->add('g:identifier_exists', $prodVar->getEan() ? 'yes' : 'no');

        // Custom discount label
        $feedHelper->add('g:custom_label_0', $prodVar->hasDiscountInFirstChannel() ? 'akce' : ' ', true);

        // Custom stock label
        $stockPhrase = 'skladem';
        if ($prodVar->getAvailableStockCount() <= 0) {
            $stockPhrase = $prodVar->getSupplier() ? $prodVar->getSupplier()->getDelivery() : 'na dotaz';
        }
        $feedHelper->add('g:custom_label_1', $stockPhrase);

        $feedHelper->shop->appendChild($feedHelper->item);
    }

    /**
     * Get second level taxon slug in first returned tree. Others tree will be ignored.
     *
     * @param  ProductInterface $product
     *
     * @return string
     */
    private function getSecondLevelTaxonSlugInFirstTree(ProductInterface $product): string
    {
        $taxons = $product->getTaxons();

        foreach ($taxons as $taxon) {
            if ($taxon->getLevel() === 1 && $taxon->isEnabled()) {
                $slug = $taxon->getSlug();
                $pos = strrpos($slug, '/');
                $cleanSlug = $pos === false ? $slug : substr($slug, $pos + 1);

                return $cleanSlug;
            }
        }

        return '';
    }
}
