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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class AdminProductMenuListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminProductMenuItems(MenuBuilderEvent $event): void
    {
        $heureka = $this->container->getParameter('app.feed.heureka.enabled');
        $google = $this->container->getParameter('app.feed.google.enabled');

        $menu = $event->getMenu();
        $product = $event->getProduct();

        $menu->removeChild('details');
        $menu->removeChild('taxonomy');
        $menu->removeChild('attributes');
        $menu->removeChild('associations');
        $menu->removeChild('media');

        $menu
            ->addChild('details')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_details.html.twig')
            ->setLabel('sylius.ui.details')
            ->setCurrent(true);

        if ($product->isSimple()) {
            $menu
                ->addChild('pricing')
                ->setAttribute('template', '@SyliusAdmin/Product/Tab/_pricing.html.twig')
                ->setLabel('sylius.ui.pricing');
        }

        $menu
            ->addChild('taxonomy')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_taxonomy.html.twig')
            ->setLabel('sylius.ui.taxonomy');

        $menu
            ->addChild('attributes')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_attributes.html.twig')
            ->setLabel('sylius.ui.attributes');

        $menu
            ->addChild('associations')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_associations.html.twig')
            ->setLabel('sylius.ui.associations');

        $menu
            ->addChild('media')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_media.html.twig')
            ->setLabel('sylius.ui.media');

        $menu
            ->addChild('shipping')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_shipment.html.twig')
            ->setLabel('sylius.ui.shipping');

        $menu
            ->addChild('tagsTab')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_tag.html.twig')
            ->setLabel('app.ui.tags');

        $menu
            ->addChild('feedsTab')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_feed.html.twig')
            ->setLabel('app.ui.feeds');

        $menu
            ->addChild('mansup')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_mansup.html.twig')
            ->setLabel('sylius.ui.mansup');

        $menu
            ->addChild('massEdit')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_massEdit.html.twig')
            ->setLabel('app.ui.mass_product_edit');
    }
}
