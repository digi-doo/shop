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

namespace AppBundle\Context;

use AppBundle\Repository\ProductAttributeRepository;

class ProductAttributesContext
{
    /**
     * @var TaxonContext
     */
    private $taxonContext;

    /**
     * @var ProductAttributeRepository
     */
    private $productAttributeRepository;

    /**
     * @param TaxonContext $taxonContext
     * @param ProductAttributeRepository $productAttributeRepository
     */
    public function __construct(
        TaxonContext $taxonContext,
        ProductAttributeRepository $productAttributeRepository
    ) {
        $this->taxonContext = $taxonContext;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    public function getProductAttributes(): ?array
    {
        $taxon = $this->taxonContext->getTaxon();

        if ($taxon === null) {
            return null;
        }
        $attrs = $this->productAttributeRepository->findByTaxon($taxon);

        return $attrs;
    }
}
