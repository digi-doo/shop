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

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class OrderRepository extends BaseOrderRepository
{
    /**
     * @param array $criteria
     *
     * @return QueryBuilder
     */
    public function createListQueryBuilder(array $criteria = []): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('channel')
            ->addSelect('customer')
            ->addSelect('shippingAddress')
            ->addSelect('billingAddress')
            ->innerJoin('o.channel', 'channel')
            ->leftJoin('o.customer', 'customer')
            ->leftJoin('o.shippingAddress', 'shippingAddress')
            ->leftJoin('o.billingAddress', 'billingAddress');

        // Set state if there is order state filter, else show only new orders
        if (isset($criteria['state'], $criteria['state']['state'])) {
            if ($criteria['state']['state'] === OrderInterface::STATE_CART) {
                $queryBuilder->andWhere('o.state != :state')
                             ->setParameter('state', OrderInterface::STATE_CART);
            } else {
                $queryBuilder->andWhere('o.state = :state')
                             ->setParameter('state', $criteria['state']['state']);
            }
        } else {
            $queryBuilder
                ->andWhere('o.state = :state')
                ->setParameter('state', OrderInterface::STATE_NEW);
        }

        return $queryBuilder;
    }

    /**
     * @param mixed $customerId
     * @param array $criteria
     *
     * @return QueryBuilder
     */
    public function createByCustomerIdAndFilterQueryBuilder($customerId, array $criteria = []): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customerId')
            ->setParameter('customerId', $customerId);

        // Set state if there is order state filter, else show only new orders
        if (isset($criteria['state'], $criteria['state']['state'])) {
            if ($criteria['state']['state'] === OrderInterface::STATE_CART) {
                $queryBuilder->andWhere('o.state != :state')
                             ->setParameter('state', OrderInterface::STATE_CART);
            } else {
                $queryBuilder->andWhere('o.state = :state')
                             ->setParameter('state', $criteria['state']['state']);
            }
        } else {
            $queryBuilder
                ->andWhere('o.state = :state')
                ->setParameter('state', OrderInterface::STATE_NEW);
        }

        return $queryBuilder;
    }
}
