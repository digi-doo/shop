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

namespace AppBundle\Form\Type\Filter\ChoiceMapper;

use AppBundle\Context\TaxonContext;
use AppBundle\Helpers\StringFormatter;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

final class ProductAttributesMapper
{
    /**
     * @var ProductAttributeValueRepositoryInterface
     */
    private $productAttributeValueRepository;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var TaxonContext
     */
    private $taxonContext;

    /**
     * @var StringFormatter
     */
    private $stringFormatter;

    /**
     * @param ProductAttributeValueRepositoryInterface $productAttributeValueRepository
     * @param LocaleContextInterface $localeContext
     * @param TaxonContext $taxonContext
     * @param StringFormatter $stringFormatter
     */
    public function __construct(
        ProductAttributeValueRepositoryInterface $productAttributeValueRepository,
        LocaleContextInterface $localeContext,
        TaxonContext $taxonContext,
        StringFormatter $stringFormatter
    ) {
        $this->productAttributeValueRepository = $productAttributeValueRepository;
        $this->localeContext = $localeContext;
        $this->taxonContext = $taxonContext;
        $this->stringFormatter = $stringFormatter;
    }

    public function mapToChoices(ProductAttributeInterface $productAttribute): array
    {
        $attributeValues = $this->productAttributeValueRepository->findUsedByAttributeAndTaxon($productAttribute, $this->taxonContext->getTaxon());

        if ($attributeValues === null) {
            return [];
        }
        $choices = [];
        array_walk($attributeValues, function (ProductAttributeValueInterface $productAttributeValue) use (&$choices): void {
            $value = $productAttributeValue->getValue();
            $configuration = $productAttributeValue->getAttribute()->getConfiguration();

            if (is_array($value)
                && isset($configuration['choices'])
                && is_array($configuration['choices'])
            ) {
                foreach ($value as $singleValue) {
                    $choice = $singleValue;
                    $label = $configuration['choices'][$singleValue][$this->localeContext->getLocaleCode()];
                    $choices[$label] = $choice;
                }
            } else {
                $choice = $value;
                $choices[$value] = $choice;
            }
        });

        asort($choices);

        return count($choices) <= 1 ? [] : $choices;
    }
}
