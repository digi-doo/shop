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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

class OrderStateFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (!empty($data['state'])) {
            if (!$data['state'] === OrderInterface::STATE_CART) {
                $dataSource->restrict($dataSource->getExpressionBuilder()->equals('state', $data['state']));
            }
        }

        if (!empty($data['shippingState'])) {
            $dataSource->restrict($dataSource->getExpressionBuilder()->equals('shippingState', $data['shippingState']));
        }

        if (!empty($data['paymentState'])) {
            $dataSource->restrict($dataSource->getExpressionBuilder()->equals('paymentState', $data['paymentState']));
        }
    }
}
