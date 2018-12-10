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

use BitBag\SyliusCmsPlugin\Entity\SectionInterface;
use BitBag\SyliusCmsPlugin\Entity\SectionTranslationInterface;
use BitBag\SyliusCmsPlugin\Fixture\Factory\FixtureFactoryInterface;
use BitBag\SyliusCmsPlugin\Repository\SectionRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class SectionFixtureFactory implements FixtureFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $sectionFactory;

    /**
     * @var FactoryInterface
     */
    private $sectionTranslationFactory;

    /**
     * @var SectionRepositoryInterface
     */
    private $sectionRepository;

    /**
     * @param FactoryInterface $sectionFactory
     * @param FactoryInterface $sectionTranslationFactory
     * @param SectionRepositoryInterface $sectionRepository
     */
    public function __construct(
        FactoryInterface $sectionFactory,
        FactoryInterface $sectionTranslationFactory,
        SectionRepositoryInterface $sectionRepository
    ) {
        $this->sectionFactory = $sectionFactory;
        $this->sectionTranslationFactory = $sectionTranslationFactory;
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $data): void
    {
        foreach ($data as $code => $fields) {
            if (
                true === $fields['remove_existing'] &&
                null !== $section = $this->sectionRepository->findOneBy(['code' => $code])
            ) {
                $this->sectionRepository->remove($section);
            }

            /** @var SectionInterface $section */
            $section = $this->sectionFactory->createNew();

            $section->setCode($code);
            $section->setType($fields['type']);

            foreach ($fields['translations'] as $localeCode => $translation) {
                /** @var SectionTranslationInterface $sectionTranslation */
                $sectionTranslation = $this->sectionTranslationFactory->createNew();

                $sectionTranslation->setLocale($localeCode);
                $sectionTranslation->setName($translation['name']);
                $sectionTranslation->setDescription($translation['description']);

                $section->addTranslation($sectionTranslation);
            }

            $this->sectionRepository->add($section);
        }
    }
}
