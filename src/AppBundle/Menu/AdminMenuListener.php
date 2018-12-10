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

final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        // Children for catalog
        $catalog = $menu->getChild('catalog');
        $catalog
            ->addChild('suppliers', ['route' => 'app_admin_supplier_index'])
            ->setLabel('app.ui.suppliers')
            ->setLabelAttribute('icon', 'shipping');
        $catalog
            ->addChild('manufacturers', ['route' => 'app_admin_manufacturer_index'])
            ->setLabel('app.ui.manufacturers')
            ->setLabelAttribute('icon', 'recycle');
        $catalog
            ->addChild('tags', ['route' => 'app_admin_tag_index'])
            ->setLabel('app.ui.tags')
            ->setLabelAttribute('icon', 'tags');
    }
}
