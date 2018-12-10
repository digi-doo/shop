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

namespace AppBundle\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ShippingMethodFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'shipping_method';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('description')->cannotBeEmpty()->end()
                ->scalarNode('zone')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('tax_category')->end()
                ->arrayNode('channels')->prototype('scalar')->end()->end()
                ->arrayNode('payment_methods')->prototype('scalar')->end()->end()
                ->arrayNode('calculator')
                    ->children()
                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                        ->variableNode('configuration')->end()
                    ->end()
                ->end()
        ;
    }
}
