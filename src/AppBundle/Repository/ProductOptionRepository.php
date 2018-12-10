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

namespace AppBundle\Repository;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductOptionRepository as BaseProductOptionRepository;
use Sylius\Component\Core\Model\TaxonInterface;

class ProductOptionRepository extends BaseProductOptionRepository
{
    /**
     * Find product options by taxon related to products
     *
     * @param  TaxonInterface $taxon
     *
     * @return []|null
     */
    public function findByTaxon(TaxonInterface $taxon): ?array
    {
        $result = $this->createQueryBuilder('po')
            ->leftJoin('AppBundle\Entity\Product', 'p', 'WITH', 'po MEMBER OF p.options')
            ->leftJoin('p.productTaxons', 'pt')
            ->andWhere('pt.taxon = :taxon')
            ->andWhere('p.enabled = true')
            ->setParameter('taxon', $taxon)
            ->distinct()
        ;

        return $result
             ->getQuery()
             ->useQueryCache(true)
             ->useResultCache(true, '3600')
             ->getResult();

        // $result = $this->createQueryBuilder('po')
        //     ->leftJoin("AppBundle\Entity\Product", "sp", Join::WITH, "po MEMBER OF sp.options")
        //     ->leftJoin("Sylius\Component\Core\Model\ProductTaxon", "spt", Join::WITH, "spt MEMBER OF sp.productTaxons")
        //     ->andWhere("spt.taxon = :taxon")
        //     ->andWhere('sp.enabled = true')
        //     ->setParameter("taxon", $taxon);

        // return $result->getQuery()->getResult();
    }

    /**
     * Find product options by its codes
     *
     * @param  array $codes
     *
     * @return []|null
     */
    public function findByOptionCodes(array $codes): ?array
    {
        $result = $this->createQueryBuilder('po')
            ->andWhere('po.code IN (:codes)')
            ->setParameter('codes', $codes)
        ;

        return $result
             ->getQuery()
             ->useQueryCache(true)
             ->useResultCache(true, '3600')
             ->getResult();
    }
}
