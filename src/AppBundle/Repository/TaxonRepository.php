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

use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository as BaseTaxonRepository;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class TaxonRepository extends BaseTaxonRepository
{
    /**
     * Find only active root taxons
     *
     * @return []
     */
    public function findActiveRootNodes(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.parent IS NULL')
            ->andWhere('o.enabled = true')
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult();
    }
}
