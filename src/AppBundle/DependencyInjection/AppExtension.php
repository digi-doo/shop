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

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AppExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Set heureka feed options
        $container->setParameter('app.feed.heureka.enabled', $config['feed']['heureka']['enabled']);
        $container->setParameter('app.feed.heureka.taxonomy_url', $config['feed']['heureka']['taxonomy_url']);

        // Set google feed options
        $container->setParameter('app.feed.google.enabled', $config['feed']['google']['enabled']);
        $container->setParameter('app.feed.google.taxonomy_url', $config['feed']['google']['taxonomy_url']);

        // Set facebook feed options
        $container->setParameter('app.feed.facebook.enabled', $config['feed']['facebook']['enabled']);

        // Set zbozi feed options
        $container->setParameter('app.feed.zbozi.enabled', $config['feed']['zbozi']['enabled']);
    }
}
