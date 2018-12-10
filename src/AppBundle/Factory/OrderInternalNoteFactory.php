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

namespace AppBundle\Factory;

use AppBundle\Entity\OrderInternalNote;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class OrderInternalNoteFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return OrderInternalNote
     */
    public function createNew(): OrderInternalNote
    {
        return $this->factory->createNew();
    }

    /**
     * @param AdminUserInterface $admin
     * @param OrderInterface $order
     *
     * @return OrderInternalNote
     */
    public function createWithAdminAndOrder(AdminUserInterface $admin, OrderInterface $order): OrderInternalNote
    {
        $note = $this->createNew();
        $note->setCreatedBy($admin);
        $note->setCreatedAt(new \DateTime());
        $note->setOrder($order);

        return $note;
    }
}
