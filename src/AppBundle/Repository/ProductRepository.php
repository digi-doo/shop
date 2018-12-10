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

use AppBundle\Entity\Manufacturer;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Taxon;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

class ProductRepository extends BaseProductRepository
{
    /**
     * Find all products with low stock
     *
     * @return []
     */
    public function findByOnHand(): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('variant')
            ->addSelect('translation')
            ->leftJoin('o.variants', 'variant')
            ->leftJoin('o.translations', 'translation')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere('variant.onHand <= 0')
            ->addOrderBy('o.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all products with at leat one variant
     *
     * @return []
     */
    public function findAllWithVariants(): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByNamePart(string $phrase, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere('translation.name LIKE :name OR o.code LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createShopListQueryBuilder(
        ChannelInterface $channel,
        TaxonInterface $taxon,
        string $locale,
        array $sorting = []
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.productTaxons', 'productTaxon')
            ->innerJoin('o.variants', 'variant')
            ->leftJoin('o.manufacturer', 'manufacturer')
            // ->leftJoin('o.options', 'option')
            // ->leftJoin('variant.optionValues', 'optionValue')
            ->andWhere('o.enabled = true')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere('variant.enabled = true')
            ->andWhere('productTaxon.taxon = :taxon')
            ->andWhere(':channel MEMBER OF o.channels')
            ->addGroupBy('o.id')
            ->setParameter('locale', $locale)
            ->setParameter('taxon', $taxon)
            ->setParameter('channel', $channel)
        ;

        // Sort list by real to two groups - in stock and out of stock. In each group sort by position.
        // Sort only if there is not additional sorting by price, name etc.
        if (empty($sorting) && $taxon !== null && $taxon->isStockSortingEnabled()) {
            $queryBuilder->addSelect('CASE WHEN (variant.onHand - variant.onHold) > 0 THEN 1 ELSE 0 END AS HIDDEN stockCondition');
            $queryBuilder->addOrderBy('stockCondition', 'DESC');
        }

        // Grid hack, we do not need to join these if we don't sort by price
        if (isset($sorting['price'])) {
            $queryBuilder
                ->innerJoin('variant.channelPricings', 'channelPricing')
                ->andWhere('channelPricing.channelCode = :channelCode')
                ->setParameter('channelCode', $channel->getCode())
            ;
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createShopSearchListQueryBuilder(
        ChannelInterface $channel,
        string $locale,
        array $sorting = []
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->addSelect('
                        (MATCH_AGAINST(translation.name, :searchterm) * 5)
                        +
                        MATCH_AGAINST(translation.description, translation.shortDescription, :searchterm) 
                        + 
                        (MATCH_AGAINST(o.code, :searchterm) * 10) 
                        AS HIDDEN score
                        ')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.variants', 'variant')
            ->where('
                    (
                    MATCH_AGAINST(translation.name, :searchterm) > 0 
                    OR
                    MATCH_AGAINST(translation.description, translation.shortDescription, :searchterm) > 0.8 
                    OR 
                    MATCH_AGAINST(o.code, :searchterm) > 0
                    )
                    ')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->setParameter('locale', $locale)
            ->setParameter('channel', $channel)
            ->addGroupBy('o.id');

        if (isset($sorting['stock'])) {
            $queryBuilder->addSelect('CASE WHEN (variant.onHand - variant.onHold) > 0 THEN 1 ELSE 0 END AS HIDDEN stockCondition');
            $queryBuilder->addOrderBy('stockCondition', 'DESC');
        }

        // Grid hack, we do not need to join these if we don't sort by price
        if (isset($sorting['price'])) {
            $queryBuilder
                ->innerJoin('variant.channelPricings', 'channelPricing')
                ->andWhere('channelPricing.channelCode = :channelCode')
                ->setParameter('channelCode', $channel->getCode())
            ;
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createAdminListQueryBuilder(string $locale, $taxonId = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere('o.enabled = true')
            ->setParameter('locale', $locale);

        if (null !== $taxonId) {
            $queryBuilder
                ->innerJoin('o.productTaxons', 'productTaxon')
                ->andWhere('productTaxon.taxon = :taxonId')
                ->setParameter('taxonId', $taxonId)
            ;
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createShopManufacturerProductsQueryBuilder(
        ChannelInterface $channel,
        Manufacturer $manufacturer,
        string $locale,
        array $sorting = []
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.manufacturer', 'manufacturer')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere('manufacturer = :manufacturer')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->addGroupBy('o.id')
            ->setParameter('locale', $locale)
            ->setParameter('manufacturer', $manufacturer)
            ->setParameter('channel', $channel)
        ;

        // Sort list by real to two groups - in stock and out of stock.
        // Sort only if there is not additional sorting by price, name etc.
        if (empty($sorting) && $manufacturer !== null && $manufacturer->isStockSortingEnabled()) {
            $queryBuilder->addSelect('CASE WHEN (variant.onHand - variant.onHold) > 0 THEN 1 ELSE 0 END AS HIDDEN stockCondition');
            $queryBuilder->addOrderBy('stockCondition', 'DESC');
        }

        // Grid hack, we do not need to join these if we don't sort by price
        if (isset($sorting['price'])) {
            $queryBuilder
                ->innerJoin('variant.channelPricings', 'channelPricing')
                ->andWhere('channelPricing.channelCode = :channelCode')
                ->setParameter('channelCode', $channel->getCode())
            ;
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createShopTagQueryBuilder(
        ChannelInterface $channel,
        Tag $tag,
        string $locale,
        array $sorting = []
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere(':tag MEMBER OF o.tags')
            ->andWhere('o.enabled = true')
            ->addGroupBy('o.id')
            ->setParameter('locale', $locale)
            ->setParameter('tag', $tag)
            ->setParameter('channel', $channel)
        ;

        // Sort list by real to two groups - in stock and out of stock.
        // Sort only if there is not additional sorting by price, name etc.
        if (empty($sorting) && $tag !== null && $tag->isStockSortingEnabled()) {
            $queryBuilder->addSelect('CASE WHEN (variant.onHand - variant.onHold) > 0 THEN 1 ELSE 0 END AS HIDDEN stockCondition');
            $queryBuilder->addOrderBy('stockCondition', 'DESC');
        }

        // Grid hack, we do not need to join these if we don't sort by price
        if (isset($sorting['price'])) {
            $queryBuilder
                ->innerJoin('variant.channelPricings', 'channelPricing')
                ->andWhere('channelPricing.channelCode = :channelCode')
                ->setParameter('channelCode', $channel->getCode())
            ;
        }

        return $queryBuilder;
    }

    /**
     * Find all products added in given month
     *
     * @param string $month
     * @param string $year
     *
     * @return []
     */
    public function findByMonthAndYear(string $month, string $year): array
    {
        // For the future not required param $month and $year in month overview command
        // $query->setParameter('date', new \DateTime('-1 month'));
        $fromTime = new \DateTime($year . '-' . $month . '-01');
        $toTime = new \DateTime($fromTime->format('Y-m-d') . ' first day of next month');

        return $this->createQueryBuilder('o')
            ->where('o.createdAt >= :fromTime')
            ->andWhere('o.createdAt < :toTime')
            ->setParameter('fromTime', $fromTime)
            ->setParameter('toTime', $toTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find latest products WITH !$variants->isEmpty() based on channel, locale and count.
     *
     * @param  ChannelInterface $channel
     * @param  string           $locale
     * @param  int           $count
     *
     * @return []
     */
    public function findLatestByChannelWithVariant(ChannelInterface $channel, string $locale, string $count): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * Fing one product by given channel, locale and slug
     *
     * @param   string           $locale
     * @param   string           $slug
     */
    public function findOneByLocaleSlug(string $locale, string $slug): ?ProductInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere('translation.slug = :slug')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find products by tag WITH !$variants->isEmpty() based on channel, locale and count.
     *
     * @param  ChannelInterface $channel
     * @param  string           $locale
     * @param  Tag              $tag
     * @param  int|null              $count
     *
     * @return []|null
     */
    public function findByTagByChannelWithVariant(ChannelInterface $channel, string $locale, Tag $tag, ?int $count): ?array
    {
        $result = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->andWhere(':tag MEMBER OF o.tags')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setParameter('tag', $tag);

        if ($count) {
            $result->setMaxResults($count);
        }

        return $result->getQuery()->getResult();
    }

    /**
     * Find random products by tag WITH !$variants->isEmpty() based on channel, locale and count.
     *
     * @param  ChannelInterface $channel
     * @param  string           $locale
     * @param  Tag              $tag
     * @param  int|null         $count
     *
     * @return []|null
     */
    public function findRandomByTagByChannelWithVariant(ChannelInterface $channel, string $locale, Tag $tag, ?int $count): ?array
    {
        $result = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->addSelect('RAND() as HIDDEN rand')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->andWhere(':tag MEMBER OF o.tags')
            ->addOrderBy('rand')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setParameter('tag', $tag);

        if ($count) {
            $result->setMaxResults($count);
        }

        return $result->getQuery()->getResult();
    }

    /**
     * Find products by taxon WITH !$variants->isEmpty() based on channel, locale and count.
     *
     * @param  ChannelInterface $channel
     * @param  string           $locale
     * @param  Taxon            $taxon
     * @param  int|null         $count
     *
     * @return []|null
     */
    public function findByTaxonByChannelWithVariant(ChannelInterface $channel, TaxonInterface $taxon, string $locale, ?int $count): ?array
    {
        $result = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.productTaxons', 'productTaxon')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere('productTaxon.taxon = :taxon')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->setParameter('locale', $locale)
            ->setParameter('taxon', $taxon)
            ->setParameter('channel', $channel);

        if ($count) {
            $result->setMaxResults($count);
        }

        return $result->getQuery()->getResult();
    }

    /**
     * Find random products by taxon WITH !$variants->isEmpty() based on channel, locale and count.
     *
     * @param  ChannelInterface $channel
     * @param  string           $locale
     * @param  Taxon            $taxon
     * @param  int|null         $count
     *
     * @return []|null
     */
    public function findRandomByTaxonByChannelWithVariant(ChannelInterface $channel, TaxonInterface $taxon, string $locale, ?int $count): ?array
    {
        $result = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->addSelect('RAND() as HIDDEN rand')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.productTaxons', 'productTaxon')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere('productTaxon.taxon = :taxon')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->addOrderBy('rand')
            ->setParameter('locale', $locale)
            ->setParameter('taxon', $taxon)
            ->setParameter('channel', $channel);

        if ($count) {
            $result->setMaxResults($count);
        }

        return $result->getQuery()->getResult();
    }

    /**
     * Find concept product for front show.
     * Get only product with some variant.
     * Show only if admin is logged.
     *
     * @param ChannelInterface $channel
     * @param string $locale
     * @param string $slug
     * @param string $slug
     *
     * @return ProductInterface|null
     */
    public function findOneConceptByChannelAndSlug(ChannelInterface $channel, string $locale, string $slug, ?string $adminLogged = null): ?ProductInterface
    {
        if (!$adminLogged) {
            return null;
        }

        $product = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.variants', 'variant')
            ->andWhere('variant.enabled = 1')
            ->andWhere('o.variants IS NOT EMPTY')
            ->andWhere('translation.slug = :slug')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = false')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByChannelAndSlug(ChannelInterface $channel, string $locale, string $slug): ?ProductInterface
    {
        $product = parent::findOneByChannelAndSlug($channel, $locale, $slug);

        return $product && $product->hasVariants() ? $product : null;
    }
}
