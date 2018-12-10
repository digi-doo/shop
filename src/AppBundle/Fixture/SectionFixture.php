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

final class SectionFixture extends AbstractFixture implements FixtureInterface
{
    /**
     * @var FixtureFactoryInterface
     */
    private $sectionFixtureFactory;

    /**
     * @param FixtureFactoryInterface $sectionFixtureFactory
     */
    public function __construct(FixtureFactoryInterface $sectionFixtureFactory)
    {
        $this->sectionFixtureFactory = $sectionFixtureFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $this->sectionFixtureFactory->load($options['custom']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'section';
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
                            ->arrayNode('translations')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('name')->defaultNull()->end()
                                        ->scalarNode('description')->defaultNull()->end()
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
