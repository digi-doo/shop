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

namespace AppBundle\Helpers\Product;

use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductVariantPriceCalculator implements ProductVariantPriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        $channelPricing = $productVariant->getChannelPricingForChannel($context['channel']);

        if (null === $channelPricing) {
            throw new MissingChannelConfigurationException(sprintf(
                'Channel %s has no price defined for product variant %s',
                $context['channel']->getName(),
                $productVariant->getName()
            ));
        }

        return $channelPricing->hasDiscount() ? $channelPricing->getDiscountedPrice() : $channelPricing->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function calculateDiscountedPrice(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        $channelPricing = $productVariant->getChannelPricingForChannel($context['channel']);

        if (null === $channelPricing) {
            throw new MissingChannelConfigurationException(sprintf(
                'Channel %s has no price defined for product variant %s',
                $context['channel']->getName(),
                $productVariant->getName()
            ));
        }

        return $channelPricing->hasDiscount() ? $channelPricing->getDiscountedPrice() : $channelPricing->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePriceWithTax(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        $channelPricing = $productVariant->getChannelPricingForChannel($context['channel']);

        if (null === $channelPricing) {
            throw new MissingChannelConfigurationException(sprintf(
                'Channel %s has no price defined for product variant %s',
                $context['channel']->getName(),
                $productVariant->getName()
            ));
        }

        $taxRate = $productVariant->getTaxCategory()->getRates()->first()->getAmount() + 1;
        $price = $channelPricing->getPrice();

        return (int) round($price * $taxRate);
    }
}
