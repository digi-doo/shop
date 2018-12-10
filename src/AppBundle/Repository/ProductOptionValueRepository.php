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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository as BaseProductOptionValueRepository;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;

class ProductOptionValueRepository extends BaseProductOptionValueRepository
{
    /**
     * Find only used product option values by given option and taxon from context
     *
     * @param  ProductOptionInterface $productOption
     * @param  TaxonInterface $taxon
     *
     * @return array|null
     */
    public function findUsedByOptionAndTaxon(ProductOptionInterface $productOption, TaxonInterface $taxon): ?array
    {
        $result = $this->createQueryBuilder('pov')
            ->leftJoin("AppBundle\Entity\ProductVariant", 'pv', Join::WITH, 'pov MEMBER OF pv.optionValues')
            ->leftJoin('pv.product', 'p')
            ->leftJoin('p.productTaxons', 'pt')
            ->andWhere('pov.option = :option')
            ->andWhere('pt.taxon = :taxon')
            // ->andWhere('p.enabled = true')
            // ->andWhere('pv.enabled = true')
            ->setParameter('option', $productOption)
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
     * Find product option values by its codes
     *
     * @param  array $codes
     *
     * @return []|null
     */
    public function findByOptionValueCodes(array $codes): ?array
    {
        $result = $this->createQueryBuilder('pov')
            ->andWhere('pov.code IN (:codes)')
            ->setParameter('codes', $codes)
        ;

        return $result
             ->getQuery()
             ->useQueryCache(true)
             ->useResultCache(true, '3600')
             ->getResult();
    }
}
