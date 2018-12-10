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

use AppBundle\Entity\ProductVariant;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as BaseProductVariantRepository;

class ProductVariantRepository extends BaseProductVariantRepository
{
    /**
     * Find all variants with enabled parent product
     *
     * @return array
     */
    public function findByVariantAndProductEnabled(): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.product', 'product')
            ->andWhere('product.enabled = true')
            ->andWhere('o.enabled = true')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find one Product Variant by given external code
     *
     * @param  string $externalCode
     *
     * @return ProductVariantInterface|null
     */
    public function findOneByExternalCode(string $externalCode): ?ProductVariant
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.externalCode = :externalCode')
            ->setParameter('externalCode', $externalCode)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
