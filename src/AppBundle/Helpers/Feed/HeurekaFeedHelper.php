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

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ShopBundle\Router\LocaleStrippingRouter;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\RequestBased\ChannelContext;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculator;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class HeurekaFeedHelper extends \DOMDocument
{
    /** @var string */
    public $shop;

    /** @var string */
    public $item;

    /** @var LocaleStrippingRouter */
    private $router;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    /** @var ProductVariantPriceCalculator */
    private $productVariantPriceCalculator;

    /** @var ChannelContext */
    private $channelContext;

    /** @var CurrencyContextInterface */
    private $currencyContext;

    /** @var ShippingMethodRepositoryInterface */
    private $shippingRepo;

    /**
     * Prepare feed and inject dependencies
     *
     * @param LocaleStrippingRouter $router
     * @param AvailabilityCheckerInterface $availabilityChecker
     * @param ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
     * @param ChannelContextInterface  $channelContext
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(
        LocaleStrippingRouter $router,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator,
        ChannelContextInterface  $channelContext,
        CurrencyContextInterface $currencyContext,
        ShippingMethodRepositoryInterface $shippingRepo
    ) {
        parent::__construct('1.0', 'utf-8');

        // Add comment
        $comment = $this->createComment('Generated ' . date('d.m.Y H:i:s'));
        $this->appendChild($comment);

        // Add base shop
        $this->shop = $this->createElement('SHOP');
        $this->appendChild($this->shop);

        $this->availabilityChecker = $availabilityChecker;
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
        $this->shippingRepo = $shippingRepo;

        $this->router = $router;
        $context = $this->router->getContext();
        $context->setHost($this->channelContext->getChannel()->getHostname());
    }

    /**
     * Encode item
     *
     * @param  string $value
     *
     * @return string|null
     */
    public function encode(string $value): ?string
    {
        $value = strip_tags($value);

        // $value = html_entity_decode($value, ENT_XML1, 'UTF-8');
        $value = html_entity_decode($value);
        $value = iconv(mb_detect_encoding($value), 'UTF-8//IGNORE', $value);

        // Remove bad characters
        $value = str_replace("\x01", '', $value);

        return $value;
    }

    /**
     * Add basic item
     *
     * @param string $name
     * @param string $value
     */
    public function add(string $name, string $value): void
    {
        if (trim($value) != '') {
            $item = $this->createElement($name, $value);
            $this->item->appendChild($item);
        }
    }

    /**
     * Add cdata item
     *
     * @param string $name
     * @param string $value
     */
    public function addCData(string $name, ?string $value): void
    {
        if ($value) {
            if (trim($value)) {
                $value = $this->encode($value);

                $item = $this->createElement($name);
                $cdata = $this->createCDATASection($value);
                $item->appendChild($cdata);
                $this->item->appendChild($item);
            }
        }
    }

    /**
     * Create absolute url to product.
     *
     * @param ProductInterface $product
     *
     * @return string
     */
    public function createLinkToProduct(ProductInterface $product, ProductVariantInterface $productVariant): string
    {
        $url = $this->router->generate('sylius_shop_product_show', [
            'slug' => $product->getSlug(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $url . '#' . $productVariant->getCode();
    }

    /**
     * Create absolute url to main product image.
     *
     * @param ProductInterface $product
     *
     * @return string
     */
    public function createLinkToMainProductImage(ProductInterface $product): string
    {
        return $this->router->generate('liip_imagine_filter', [
            'path' => $product->getImagesByType('main')->first()->getPath(),
            'filter' => 'sylius_shop_product_large_thumbnail',
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Create absolute url to product image.
     *
     * @param ProductInterface $product
     * @param ProductImage $productImage
     *
     * @return string
     */
    public function createLinkToProductImage(ProductInterface $product, ProductImage $productImage): string
    {
        return $this->router->generate('liip_imagine_filter', [
            'path' => $productImage->getPath(),
            'filter' => 'sylius_shop_product_large_thumbnail',
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Get product variant price
     *
     * @param ProductVariantInterface $productVariant
     *
     * @return string
     */
    public function getProductVariantPriceTax(ProductVariantInterface $productVariant): string
    {
        return (string) $productVariant->getVariantPriceVat($this->channelContext->getChannel());
    }

    /**
     * @TODO
     * Get product variant delivery date
     *
     * @param ProductVariantInterface $productVariant
     *
     * @return string
     */
    public function getDeliveryDate(ProductVariantInterface $productVariant): string
    {
        return (string) 0;
    }

    /**
     * Add all product variant options and values
     *
     * @param ProductVariantInterface $productVariant
     * @param \DOMElement $item
     */
    public function addProductVariantOptions(ProductVariantInterface $productVariant, \DOMElement $item): void
    {
        foreach ($productVariant->getOptionValues() as $value) {
            $childItem = $this->createElement('PARAM');
            $paramName = $this->createElement('PARAM_NAME', $value->getOption()->getName());
            $paramValue = $this->createElement('VAL', $value->getValue());
            $childItem->appendChild($paramName);
            $childItem->appendChild($paramValue);

            $item->appendChild($childItem);
        }
    }

    /**
     * Add all available channel shipping methods
     *
     * @param array $shippingMethods
     * @param \DOMElement $item
     */
    public function addShippingMethods(array $shippingMethods, \DOMElement $item): void
    {
        foreach ($shippingMethods as $method) {
            $price = round(($method->getConfiguration()['default']['amount'] / 100));
            $vat = $method->getTaxCategory()->getRates()->first()->getAmount();

            $childItem = $this->createElement('DELIVERY');
            $methodName = $this->createElement('DELIVERY_ID', $method->getName());
            $methodPrice = $this->createElement('DELIVERY_PRICE', (string) $price);
            $methodPriceVat = $this->createElement('DELIVERY_PRICE_COD', (string) round(($price * $vat + $price)));
            $childItem->appendChild($methodName);
            $childItem->appendChild($methodPrice);
            $childItem->appendChild($methodPriceVat);

            $item->appendChild($childItem);
        }
    }

    /**
     * Get all shipping methods
     *
     * @return Collection
     */
    public function getShippingMethods()
    {
        return $this->shippingRepo->findBy(['enabled' => true]);
    }
}
