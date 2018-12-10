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

namespace AppBundle\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

class FulltextSearchFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data) || $data['value'] === '') {
            return;
        }

        $parts = preg_split("/[\s,;]+/", $data['value']);
        $searchTerm = implode(' ', $parts);

        $queryBuilder = $dataSource->getQueryBuilder();
        $queryBuilder->setParameter('searchterm', $searchTerm);
        $queryBuilder->addOrderBy('score', 'DESC');
    }
}
