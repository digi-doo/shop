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

namespace AppBundle\Fixture\Factory;

use AppBundle\Repository\TagRepository;
use BitBag\SyliusCmsPlugin\Entity\BlockInterface;
use BitBag\SyliusCmsPlugin\Entity\BlockTranslationInterface;
use BitBag\SyliusCmsPlugin\Entity\SectionInterface;
use BitBag\SyliusCmsPlugin\Factory\BlockFactoryInterface;
use BitBag\SyliusCmsPlugin\Fixture\Factory\FixtureFactoryInterface;
use BitBag\SyliusCmsPlugin\Repository\BlockRepositoryInterface;
use BitBag\SyliusCmsPlugin\Repository\SectionRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class BlockFixtureFactory implements FixtureFactoryInterface
{
    /**
     * @var BlockFactoryInterface
     */
    private $blockFactory;

    /**
     * @var FactoryInterface
     */
    private $blockTranslationFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var SectionRepositoryInterface
     */
    private $sectionRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @param BlockFactoryInterface $blockFactory
     * @param FactoryInterface $blockTranslationFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param SectionRepositoryInterface $sectionRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ChannelContextInterface $channelContext
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        BlockFactoryInterface $blockFactory,
        FactoryInterface $blockTranslationFactory,
        BlockRepositoryInterface $blockRepository,
        SectionRepositoryInterface $sectionRepository,
        ProductRepositoryInterface $productRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        TagRepository $tagRepository
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockTranslationFactory = $blockTranslationFactory;
        $this->blockRepository = $blockRepository;
        $this->sectionRepository = $sectionRepository;
        $this->productRepository = $productRepository;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->tagRepository = $tagRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $data): void
    {
        foreach ($data as $code => $fields) {
            if (
                true === $fields['remove_existing'] &&
                null !== $block = $this->blockRepository->findOneBy(['code' => $code])
            ) {
                $this->blockRepository->remove($block);
            }

            $this->createBlock($code, $fields);
        }
    }

    private function createBlock(string $code, array $blockData): void
    {
        $type = $blockData['type'];
        $tabTybe = $blockData['tab_tybe'];

        $block = $this->blockFactory->createWithType($type);
        $products = $blockData['products'];
        $tagID = $blockData['tag'];

        if (null !== $tagID) {
            $this->resolveTag($block, $tagID);
        }

        if (null !== $products) {
            $this->resolveProducts($block, $products);
        }

        $this->resolveSections($block, $blockData['sections']);

        $block->setCode($code);
        $block->setTabType($tabTybe);
        $block->setEnabled($blockData['enabled']);

        foreach ($blockData['translations'] as $localeCode => $translation) {
            /** @var BlockTranslationInterface $blockTranslation */
            $blockTranslation = $this->blockTranslationFactory->createNew();

            $blockTranslation->setLocale($localeCode);
            $blockTranslation->setName($translation['name']);
            $blockTranslation->setContent($translation['content']);
            $blockTranslation->setLink($translation['link']);

            $block->addTranslation($blockTranslation);
        }

        $this->blockRepository->add($block);
    }

    /**
     * @param BlockInterface $block
     * @param int $limit
     */
    private function resolveProducts(BlockInterface $block, int $limit): void
    {
        $products = $this->productRepository->findLatestByChannel(
            $this->channelContext->getChannel(),
            $this->localeContext->getLocaleCode(),
            $limit
        );

        foreach ($products as $product) {
            $block->addProduct($product);
        }
    }

    /**
     * @param BlockInterface $block
     * @param array $sections
     */
    private function resolveSections(BlockInterface $block, array $sections): void
    {
        foreach ($sections as $sectionCode) {
            /** @var SectionInterface $section */
            $section = $this->sectionRepository->findOneBy(['code' => $sectionCode]);

            $block->addSection($section);
        }
    }

    /**
     * @param BlockInterface $block
     * @param int $tag
     */
    private function resolveTag(BlockInterface $block, int $tagID): void
    {
        $tag = $this->tagRepository->findOneBy(['id' => $tagID]);
        $block->setTag($tag);
    }
}
