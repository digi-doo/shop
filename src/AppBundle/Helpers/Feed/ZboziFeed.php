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

use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ZboziFeed
{
    /**
     * Add product variant for heureka feed export
     *
     * @param ProductVariantInterface $prod
     * @param FeedHelper $prod
     */
    public function addProductVariant(ProductVariantInterface $prodVar, ZboziFeedHelper $feedHelper): void
    {
        // Main parent product
        $prod = $prodVar->getProduct();

        // Create base shop element
        $feedHelper->item = $feedHelper->createElement('SHOPITEM');

        // Product variant code
        $feedHelper->add('ITEM_ID', $prodVar->getCode());

        // Product
        $feedHelper->addCData('PRODUCT', $prod->getName());

        // Product name
        $feedHelper->addCData('PRODUCTNAME', $prod->getName() . ' - ' . $prodVar->getCode());

        // Product description
        $feedHelper->addCData('DESCRIPTION', $prod->getDescription() ? $prod->getDescription() : $prod->getShortDescription());

        // Product variant EAN
        if ($prodVar->getEan()) {
            $feedHelper->addCData('EAN', $prodVar->getEan());
        }

        // Product manufacturer
        if ($prod->getManufacturer()) {
            $feedHelper->addCData('MANUFACTURER', $prod->getManufacturer()->getName());
        }

        // Product URL
        $feedHelper->add('URL', $feedHelper->createLinkToProduct($prod, $prodVar));

        // Product main image
        if (!$prod->getImagesByType('main')->isEmpty()) {
            $feedHelper->add('IMGURL', $feedHelper->createLinkToMainProductImage($prod));
        }

        // Product thumbnails
        // if (!$prod->getImagesByType('thumbnail')->isEmpty()) {
        //     foreach ($prod->getImagesByType('thumbnail') as $thumbnail) {
        //         $feedHelper->add('IMGURL_ALTERNATIVE', $feedHelper->createLinkToProductImage($prod, $thumbnail));
        //     }
        // }

        // Product variant price with tax
        $feedHelper->add('PRICE_VAT', $feedHelper->getProductVariantPriceTax($prodVar));

        // Product heureka taxonomy
        if ($prod->getHeurekaTaxonomy()) {
            $feedHelper->addCData('CATEGORYTEXT', $prod->getHeurekaTaxonomy()->getName());
        }

        // Product variant options and its values
        if (!$prodVar->getOptionValues()->isEmpty()) {
            $feedHelper->addProductVariantOptions($prodVar, $feedHelper->item);
        }

        // Product Dvariant delivery date
        $feedHelper->add('DELIVERY_DATE', $feedHelper->getDeliveryDate($prodVar));

        /**
         * Heureka wants their unique delivery codes
         */
        // Add available channel deliveries methods with prices
        // $shippingMethods = $feedHelper->getShippingMethods();
        // if (!empty($shippingMethods)) $feedHelper->addShippingMethods($shippingMethods, $feedHelper->item);

        // Product CODE for variants
        if (!$prod->isSimple()) {
            $feedHelper->add('ITEMGROUP_ID', $prod->getCode());
        }

        $feedHelper->shop->appendChild($feedHelper->item);
    }
}
