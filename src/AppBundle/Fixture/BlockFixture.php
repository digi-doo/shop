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

use BitBag\SyliusCmsPlugin\Fixture\Factory\FixtureFactoryInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class BlockFixture extends AbstractFixture implements FixtureInterface
{
    /**
     * @var FixtureFactoryInterface
     */
    private $blockFixtureFactory;

    /**
     * @param FixtureFactoryInterface $blockFixtureFactory
     */
    public function __construct(FixtureFactoryInterface $blockFixtureFactory)
    {
        $this->blockFixtureFactory = $blockFixtureFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $this->blockFixtureFactory->load($options['custom']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'block';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->arrayNode('custom')
                    ->prototype('array')
                        ->children()
                            ->booleanNode('remove_existing')->defaultTrue()->end()
                            ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('tab_tybe')->defaultNull()->end()
                            ->scalarNode('tag')->defaultNull()->end()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->integerNode('products')->defaultNull()->end()
                            ->arrayNode('sections')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('translations')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('name')->defaultNull()->end()
                                        ->scalarNode('content')->defaultNull()->end()
                                        ->scalarNode('link')->defaultNull()->end()
                                        ->scalarNode('image_path')->defaultNull()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
