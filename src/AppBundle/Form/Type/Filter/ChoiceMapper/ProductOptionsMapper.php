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
use AppBundle\Repository\ProductOptionValueRepository;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductOptionsMapper
{
    /**
     * @var ProductOptionValueRepository
     */
    private $optionValueRepo;

    /**
     * @var TaxonContext
     */
    private $taxonContext;

    /**
     * @param ProductOptionValueRepository $optionValueRepo
     * @param TaxonContext $taxonContext
     */
    public function __construct(
        ProductOptionValueRepository $optionValueRepo,
        TaxonContext $taxonContext
    ) {
        $this->optionValueRepo = $optionValueRepo;
        $this->taxonContext = $taxonContext;
    }

    public function mapToChoices(ProductOptionInterface $productOption): array
    {
        // $productOptionValues = $productOption->getValues()->toArray();
        $productOptionValues = $this->optionValueRepo->findUsedByOptionAndTaxon($productOption, $this->taxonContext->getTaxon());

        if ($productOptionValues === null
            || empty($productOptionValues)
            || count($productOptionValues) < 1) {
            return [];
        }
        $choices = [];
        array_walk($productOptionValues, function (ProductOptionValueInterface $productOptionValue) use (&$choices): void {
            $value = $productOptionValue->getValue();
            $choices[$value] = $productOptionValue->getCode();
        });

        asort($choices);

        return count($choices) <= 1 ? [] : $choices;
    }
}
