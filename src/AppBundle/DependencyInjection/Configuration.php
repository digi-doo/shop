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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');

        $rootNode
            ->children()
                ->arrayNode('feed')
                    ->children()
                        ->arrayNode('heureka')
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('taxonomy_url')->defaultNull()->end()
                              ->end()
                        ->end()
                        ->arrayNode('google')
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('taxonomy_url')->defaultNull()->end()
                            ->end()
                        ->end()
                        ->arrayNode('facebook')
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                            ->end()
                        ->end()
                        ->arrayNode('zbozi')
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                              ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
