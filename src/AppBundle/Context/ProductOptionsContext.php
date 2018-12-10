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

use AppBundle\Repository\ProductOptionRepository;

class ProductOptionsContext
{
    /**
     * @var TaxonContext
     */
    private $taxonContext;

    /**
     * @var ProductOptionRepository
     */
    private $productOptionRepository;

    /**
     * @param TaxonContext $taxonContext
     * @param ProductOptionRepository $productOptionRepository
     */
    public function __construct(
        TaxonContext $taxonContext,
        ProductOptionRepository $productOptionRepository
    ) {
        $this->taxonContext = $taxonContext;
        $this->productOptionRepository = $productOptionRepository;
    }

    public function getProductOptions(): ?array
    {
        $taxon = $this->taxonContext->getTaxon();

        if ($taxon === null) {
            return null;
        }
        $options = $this->productOptionRepository->findByTaxon($taxon);

        return $options;
    }
}
