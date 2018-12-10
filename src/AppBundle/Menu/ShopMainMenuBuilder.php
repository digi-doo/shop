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

use AppBundle\Repository\TagRepository;
use AppBundle\Repository\TaxonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ShopMainMenuBuilder
{
    public const EVENT_NAME = 'app.menu.shop.main';

    /**
     * @var ProductInterface|null
     */
    private $product;

    /**
     * @var MenuFactory
     */
    private $factory;

    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var TaxonRepository
     */
    private $taxonRepo;

    /**
     * @var TagRepository
     */
    private $tagRepo;

    /**
     * Temp hardcoded tags to append
     *
     * @var array
     */
    private $tags = ['vyprodej'];

    /**
     * Temp hardcoded custom pages to append
     *
     * @var array
     */
    private $pages = [
        [
            'label' => 'Výrobci',
            'route' => 'app_shop_manufacturer_index',
        ],
    ];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->factory = $container->get('knp_menu.factory');
        $this->matcher = $container->get('knp_menu.matcher');
        $this->eventDispatcher = $container->get('event_dispatcher');
        $this->taxonRepo = $container->get('sylius.repository.taxon');
        $this->tagRepo = $container->get('app.repository.tag');

        $currentPage = $container->get('request_stack')->getCurrentRequest()->attributes;
        if ($currentPage->get('_route') === 'sylius_shop_product_show') {
            $currentLocale = $container->get('sylius.context.locale')->getLocaleCode();
            $this->product = $container->get('sylius.repository.product')->findOneByLocaleSlug(
                    $container->get('sylius.context.locale')->getLocaleCode(),
                    $currentPage->get('slug')
                );
        }
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createMenu(array $options): ItemInterface
    {
        // Get all active categories with just one query
        $rootCats = $this->taxonRepo->findActiveRootNodes();
        $cats = new ArrayCollection($rootCats);

        // Create menu wrapper
        $menu = $this->factory->createItem('root');

        // Set all active categories
        $this->addCategories($menu, $cats);

        // Add custom tags
        $this->addTags($menu, $this->tags);

        // Add custom pages
        $this->addPages($menu, $this->pages);

        // Dispatch shop main menu event
        $this->eventDispatcher->dispatch(self::EVENT_NAME, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     * @param Collection $cats
     */
    private function addCategories(ItemInterface $menu, Collection $cats): void
    {
        if (!empty($cats)) {
            foreach ($cats as $cat) {
                if (!$cat->isEnabled()) {
                    continue;
                }
                $catLevel = $cat->getLevel();
                $slug = $cat->getSlug();
                $hasChildren = $cat->hasChildren();

                $ulAttrs = ['class' => 'nav navbar-nav'];

                $prod = false;
                if ($this->product) {
                    if ($this->product->getTaxons()->contains($cat)) {
                        $prod = true;
                        $menu->setExtra('product', $prod);
                    }
                }

                $menu->setExtra('level', $cat->getLevel() + 1);

                $menu->setChildrenAttributes($ulAttrs);
                $menu->addChild($slug, [
                        'route' => 'sylius_shop_product_index',
                        'routeParameters' => ['slug' => $slug],
                    ])
                    ->setAttributes([
                        'class' => 'nav-item' . ($hasChildren ? ' dropdown menu-large' : ''),
                    ])
                    ->setExtra('product', $prod)
                    ->setExtra('code', $cat->getCode())
                    ->setLabel($cat->getName());

                if ($hasChildren) {
                    $this->addCategories($menu[$slug], $cat->getChildren());
                }
            }
        }
    }

    /**
     * @param ItemInterface $menu
     * @param array $tags
     */
    private function addTags(ItemInterface $menu, array $tags): void
    {
        if (!empty($tags)) {
            foreach ($tags as $tagSlug) {
                $tag = $this->tagRepo->findOneBySlug($tagSlug, 'cs_CZ');
                if ($tag === null) {
                    continue;
                }
                if (!$tag->isEnabled()) {
                    continue;
                }

                $slug = $tag->getSlug();
                $menu->addChild($slug, [
                        'route' => 'app_shop_tag_index',
                        'routeParameters' => ['slug' => $slug],
                    ])
                    ->setAttributes([
                        'class' => 'nav-item',
                    ])
                    ->setLabel($tag->getName());
            }
        }
    }

    /**
     * @param ItemInterface $menu
     * @param array $pages
     */
    private function addPages(ItemInterface $menu, array $pages): void
    {
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $menu->addChild($page['route'], [
                        'route' => $page['route'],
                    ])
                    ->setAttributes([
                        'class' => 'nav-item',
                    ])
                    ->setLabel($page['label'])
                ;
            }
        }
    }
}
