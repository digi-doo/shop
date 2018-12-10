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

use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAttributeValueRepository as BaseProductAttributeValueRepository;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;

class ProductAttributeValueRepository extends BaseProductAttributeValueRepository
{
    /**
     * Find only used product attributes values
     *
     * @param  ProductAttributeInterface $productAttribute
     * @param  TaxonInterface $taxon
     *
     * @return []|null
     */
    public function findUsedByAttributeAndTaxon(ProductAttributeInterface $productAttribute, TaxonInterface $taxon): ?array
    {
        $result = $this->createQueryBuilder('pav')
            ->leftJoin('pav.subject', 'p')
            ->leftJoin('p.productTaxons', 'pt')
            ->andWhere('pav.attribute = :productAttribute')
            ->andWhere('pt.taxon = :taxon')
            ->andWhere('p.enabled = true')
            ->setParameter('productAttribute', $productAttribute)
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
     * Find product attribute values by attribute code and values
     *
     * @param  array $attributeCode
     * @param  array $values
     *
     * @return []|null
     */
    public function findByAttributeCodeAndValues(string $attributeCodeWithDelimiter, array $values): ?array
    {
        $attributeCode = explode('___', $attributeCodeWithDelimiter)[0];
        $attributeType = explode('___', $attributeCodeWithDelimiter)[1];

        $result = $this->createQueryBuilder('pav')
            ->innerJoin('pav.attribute', 'attribute')
            ->andWhere('attribute.code = :attrCode')
            ->andWhere("pav.$attributeType IN (:values)")
            ->setParameter('attrCode', $attributeCode)
            ->setParameter('values', $values)
        ;

        return $result
             ->getQuery()
             ->useQueryCache(true)
             ->useResultCache(true, '3600')
             ->getResult()
        ;
    }
}
