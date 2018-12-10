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

namespace AppBundle\Helpers\Twig;

use AppBundle\Entity\Taxon;
use AppBundle\Repository\ProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductsByTaxonHelper extends Helper
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ChannelInterface
     */
    private $channel;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param ProductVariantResolverInterface $productRepository
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(ProductRepository $productRepository, ChannelInterface $channel, string $locale)
    {
        $this->productRepository = $productRepository;
        $this->channel = $channel;
        $this->locale = $locale;
    }

    /**
     * @param Taxon $taxon
     *
     * @return int
     */
    public function countProducts(Taxon $taxon): int
    {
        $products = $this->productRepository->findByTaxonByChannelWithVariant($this->channel, $taxon, $this->locale, null);

        return $products ? count($products) : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'products_by_taxon_counter';
    }
}
