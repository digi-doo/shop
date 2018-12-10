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

use AppBundle\Repository\ProductAttributeValueRepository;
use AppBundle\Repository\ProductOptionValueRepository;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

class ProductsFilter implements FilterInterface
{
    /**
     * @var ProductOptionValueRepository
     */
    private $optionValueRepo;

    /**
     * @var ProductAttributeValueRepository
     */
    private $attributeValueRepo;

    /**
     * @param ProductOptionRepository $optionRepo
     * @param ProductAttributeValueRepository $attributeValueRepo
     */
    public function __construct(
        ProductOptionValueRepository $optionValueRepo,
        ProductAttributeValueRepository $attributeValueRepo
    ) {
        $this->optionValueRepo = $optionValueRepo;
        $this->attributeValueRepo = $attributeValueRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        // Product manufacturers with OR boolean
        if (!empty($data['manufacturers'])) {
            $this->resolveManufacturers($data['manufacturers'], $dataSource);
        }

        // Product option values with OR boolean separated with AND for each option
        if (!empty($data['options'])) {
            $this->resolveOptionValues($data['options'], $dataSource);
        }

        // Product attribute values with OR boolean separated with AND for each attribute
        if (!empty($data['attributes'])) {
            $this->resolveAttributeValues($data['attributes'], $dataSource);
        }
    }

    /**
     * @param  array $options
     * @param  DataSourceInterface $dataSource
     */
    private function resolveAttributeValues(array $attributeCodes, DataSourceInterface $dataSource): void
    {
        $queryBuilder = $dataSource->getQueryBuilder();

        $queries = [];
        $ia = 0;
        foreach ($attributeCodes as $attributeCode => $attributeStringValues) {
            $queryString = '';
            $attributeValues = $this->attributeValueRepo->findByAttributeCodeAndValues($attributeCode, $attributeStringValues);
            $attributeValuesCount = count($attributeValues);

            $i = 0;
            foreach ($attributeValues as $value) {
                if ($i == 0) {
                    $queryString .= '(';
                }
                $queryString .= ':attributeValue' . $ia . ' MEMBER OF o.attributes';
                if ($i != $attributeValuesCount - 1) {
                    $queryString .= ' OR ';
                }
                if ($i == $attributeValuesCount - 1) {
                    $queryString .= ')';
                }

                $queryBuilder->setParameter('attributeValue' . $ia, $value);
                ++$ia;
                ++$i;
            }

            $queries[] = $queryString;
        }

        $mainQuery = '';
        $queriesCount = count($queries);
        $qi = 0;
        foreach ($queries as $query) {
            $mainQuery .= $query;
            if ($qi != $queriesCount - 1) {
                $mainQuery .= ' AND ';
            }
            ++$qi;
        }

        $queryBuilder->andWhere($mainQuery);
    }

    /**
     * @param  array $options
     * @param  DataSourceInterface $dataSource
     */
    private function resolveOptionValues(array $optionCodes, DataSourceInterface $dataSource): void
    {
        $queryBuilder = $dataSource->getQueryBuilder();

        $queries = [];
        $iv = 0;
        foreach ($optionCodes as $optionValueCodes) {
            $queryString = '';
            $optionValues = $this->optionValueRepo->findByOptionValueCodes($optionValueCodes);
            $optionValuesCount = count($optionValues);

            $i = 0;
            foreach ($optionValues as $value) {
                if ($i == 0) {
                    $queryString .= '(';
                }
                $queryString .= ':optionValue' . $iv . ' MEMBER OF variant.optionValues';
                if ($i != $optionValuesCount - 1) {
                    $queryString .= ' OR ';
                }
                if ($i == $optionValuesCount - 1) {
                    $queryString .= ')';
                }

                $queryBuilder->setParameter('optionValue' . $iv, $value);
                ++$iv;
                ++$i;
            }

            $queries[] = $queryString;
        }

        $mainQuery = '';
        $queriesCount = count($queries);
        $qi = 0;
        foreach ($queries as $query) {
            $mainQuery .= $query;
            if ($qi != $queriesCount - 1) {
                $mainQuery .= ' AND ';
            }
            ++$qi;
        }

        $queryBuilder->andWhere($mainQuery);
    }

    /**
     * @param  array $manufacturers
     * @param  DataSourceInterface $dataSource
     */
    private function resolveManufacturers(array $manufacturers, DataSourceInterface $dataSource): void
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();
        $manExpressions = [];
        foreach ($manufacturers['manufacturers'] as $manCode) {
            $manExpressions[] = $expressionBuilder->equals('manufacturer.code', $manCode);
        }

        $dataSource->restrict($expressionBuilder->orX(...$manExpressions));
    }
}
