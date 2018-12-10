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

use AppBundle\Event\ChannelMenuBuilderEvent;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ChannelFormMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.channel.form';

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!array_key_exists('channel', $options) || !$options['channel'] instanceof ChannelInterface) {
            return $menu;
        }

        $menu
            ->addChild('details')
            ->setAttribute('template', '@SyliusAdmin/Channel/Tab/_details.html.twig')
            ->setLabel('sylius.ui.details')
            ->setCurrent(true)
        ;

        $menu
            ->addChild('locales')
            ->setAttribute('template', '@SyliusAdmin/Channel/Tab/_locales.html.twig')
            ->setLabel('sylius.form.channel.locales')
        ;

        $menu
            ->addChild('pricing')
            ->setAttribute('template', '@SyliusAdmin/Channel/Tab/_pricing.html.twig')
            ->setLabel('sylius.ui.channel_pricing')
        ;

        $menu
            ->addChild('contact')
            ->setAttribute('template', '@SyliusAdmin/Channel/Tab/_contact.html.twig')
            ->setLabel('sylius.ui.channel_contacts')
        ;

        $menu
            ->addChild('seo_settings')
            ->setAttribute('template', '@SyliusAdmin/Channel/Tab/_seoSettings.html.twig')
            ->setLabel('sylius.ui.channel_seo_settings')
        ;

        $menu
            ->addChild('search')
            ->setAttribute('template', '@SyliusAdmin/Channel/Tab/_search.html.twig')
            ->setLabel('sylius.ui.channel_search')
        ;

        $menu
            ->addChild('gift')
            ->setAttribute('template', '@SyliusAdmin/Channel/Tab/_gift.html.twig')
            ->setLabel('sylius.ui.channel_gift')
        ;

        $menu
            ->addChild('others')
            ->setAttribute('template', '@SyliusAdmin/Channel/Tab/_others.html.twig')
            ->setLabel('sylius.ui.channel_others')
        ;

        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new ChannelMenuBuilderEvent($this->factory, $menu, $options['channel'])
        );

        return $menu;
    }
}
