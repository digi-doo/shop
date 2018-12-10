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

use AppBundle\Entity\Product;
use Symfony\Component\Templating\Helper\Helper;

class ProductVariantsStockHelper extends Helper
{
    /**
     * @param Product $product
     *
     * @return int
     */
    public function countStock(Product $product): int
    {
        if ($product->getVariants()->isEmpty()) {
            return 0;
        }
        $count = 0;
        foreach ($product->getVariants() as $variant) {
            // if ($variant->isNegativeStock()) {
            //     // There is at least one variant with negative stock bool
            //     $count += 1;

            //     break;
            // }
            $count += ($variant->getOnHand() - $variant->getOnHold());
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'product_variants_stock_count';
    }
}
