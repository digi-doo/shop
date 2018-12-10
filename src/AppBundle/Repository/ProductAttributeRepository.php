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

use Doctrine\ORM\Query\Expr\Join;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository as BaseProductAttributeRepository;
use Sylius\Component\Core\Model\TaxonInterface;

class ProductAttributeRepository extends BaseProductAttributeRepository
{
    /**
     * Find product attributes by taxon related to products
     *
     * @param  TaxonInterface $taxon
     *
     * @return []|null
     */
    public function findByTaxon(TaxonInterface $taxon): ?array
    {
        // $result = $this->createQueryBuilder('pa')
        //     ->leftJoin("AppBundle\Entity\Product", "sp", Join::WITH,
        //         "
        //         (EXISTS (SELECT 1 FROM Sylius\Component\Product\Model\ProductAttributeValue pva
        //         WHERE pva.subject = sp
        //         AND pva.attribute = pa))
        //         "
        //     )
        //     ->leftJoin("Sylius\Component\Core\Model\ProductTaxon", "spt", Join::WITH, "spt MEMBER OF sp.productTaxons")
        //     ->andWhere("spt.taxon = :taxon")
        //     ->andWhere('sp.enabled = true')
        //     ->setParameter("taxon", $taxon);

        $result = $this->createQueryBuilder('pa')
            ->leftJoin('Sylius\Component\Product\Model\ProductAttributeValue', 'pav', 'WITH', 'pav.attribute = pa')
            ->leftJoin('pav.subject', 'p')
            ->leftJoin('p.productTaxons', 'pt')
            ->andWhere('pt.taxon = :taxon')
            ->andWhere('p.enabled = true')
            ->addOrderBy('pa.position', 'ASC')
            ->setParameter('taxon', $taxon)
            ->distinct()
        ;

        return $result
             ->getQuery()
             ->useQueryCache(true)
             ->useResultCache(true, '3600')
             ->getResult();
    }

    /**
     * Find product attributes by its codes
     *
     * @param  array $codes
     *
     * @return []|null
     */
    public function findByAttributeCodes(array $codes): ?array
    {
        $result = $this->createQueryBuilder('pa')
            ->andWhere('pa.code IN (:codes)')
            ->setParameter('codes', $codes)
        ;

        return $result
             ->getQuery()
             ->useQueryCache(true)
             ->useResultCache(true, '3600')
             ->getResult();
    }
}
