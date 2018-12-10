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

namespace AppBundle\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class AdminProductVariantMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminProductVariantMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $menu->removeChild('taxes');
        $menu
            ->addChild('pricing')
            ->setAttribute('template', '@SyliusAdmin/ProductVariant/Tab/_pricing.html.twig')
            ->setLabel('sylius.ui.pricing');
        $menu
            ->addChild('inventory')
            ->setAttribute('template', '@SyliusAdmin/ProductVariant/Tab/_inventory.html.twig')
            ->setLabel('sylius.ui.inventory');
        $menu
            ->addChild('shipping')
            ->setAttribute('template', '@SyliusAdmin/ProductVariant/Tab/_shipment.html.twig')
            ->setLabel('sylius.ui.shipping');
        $menu
            ->addChild('supplier')
            ->setAttribute('template', '@SyliusAdmin/ProductVariant/Tab/_supplier.html.twig')
            ->setLabel('app.ui.supplier');
    }
}
