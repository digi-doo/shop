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

use AppBundle\Repository\ManufacturerRepository;

class ManufacturersContext
{
    /**
     * @var TaxonContext
     */
    private $taxonContext;

    /**
     * @var ManufacturerRepository
     */
    private $manufacturerRepository;

    /**
     * @param TaxonContext $taxonContext
     * @param ManufacturerRepository $manufacturerRepository
     */
    public function __construct(
        TaxonContext $taxonContext,
        ManufacturerRepository $manufacturerRepository
    ) {
        $this->taxonContext = $taxonContext;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    public function getManufacturers(): ?array
    {
        $taxon = $this->taxonContext->getTaxon();

        if ($taxon === null) {
            return null;
        }
        $manufacturers = $this->manufacturerRepository->findByTaxon($taxon);

        return $manufacturers;
    }
}
