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

namespace AppBundle\Helpers\Product;

use AppBundle\Repository\ProductRepository;
use Sylius\Component\Resource\Storage\StorageInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class LastViewedProductsHelper
{
    /**
     * @var StorageInterface
     */
    private $storageSession;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(StorageInterface $storageSession, ProductRepository $productRepository)
    {
        $this->storageSession = $storageSession;
        $this->productRepository = $productRepository;
    }

    /**
     * @param  string $locale
     *
     * @return array
     */
    public function getProducts(?string $locale): array
    {
        $viewedProducts = $this->storageSession->get('viewed_products');
        $products = [];

        if ($viewedProducts) {
            foreach ($viewedProducts as $slug) {
                $product = $this->productRepository->findOneByLocaleSlug($locale, $slug);
                if ($product) {
                    $products[] = $product;
                }
            }

            return $products;
        }

        // If there is no session return empty array
        return $products;
    }
}
