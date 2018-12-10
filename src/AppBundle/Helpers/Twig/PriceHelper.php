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

namespace AppBundle\Helpers\Twig;

use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Templating\Helper\Helper;
use Webmozart\Assert\Assert;

class PriceHelper extends Helper
{
    /**
     * @var ProductVariantPriceCalculatorInterface
     */
    private $productVariantPriceCalculator;

    /**
     * @param ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
     */
    public function __construct(ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function getPrice(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        return $this
            ->productVariantPriceCalculator
            ->calculate($productVariant, $context)
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function getPriceWithVat(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        return $this
            ->productVariantPriceCalculator
            ->calculatePriceWithTax($productVariant, $context)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_calculate_price';
    }
}
