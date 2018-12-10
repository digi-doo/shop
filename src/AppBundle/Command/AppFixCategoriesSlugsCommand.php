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

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppFixCategoriesSlugsCommand extends ContainerAwareCommand
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:fix-categories-slugs')
            ->setDescription('Fix all categories slig according to parent category. Final slug format will be parent/parent/parent/child.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $taxonRepo = $this->getContainer()->get('sylius.repository.taxon');
        $language = 'cs_CZ';
        $taxonManager = $this->getContainer()->get('sylius.manager.taxon');
        $taxonSlugGenerator = $this->getContainer()->get('sylius.generator.taxon_slug');
        $taxons = $taxonRepo->findAll();

        $this->io->progressStart(count($taxons));
        $batchSize = 20;
        $i = 0;
        foreach ($taxons as $taxon) {
            if ($taxon->isRoot()) {
                continue;
            }
            if ($taxonRepo->findOneBySlug($taxonSlugGenerator->generate($taxon), $language)) {
                $taxon->setSlug($this->generateString($taxonSlugGenerator->generate($taxon)), 3);
            } else {
                $taxon->setSlug($taxonSlugGenerator->generate($taxon));
            }

            $taxonManager->persist($taxon);

            if (($i % $batchSize) === 0) {
                $taxonManager->flush();
            }

            ++$i;
            $this->io->progressAdvance();
        }
        $taxonManager->flush();
        $this->io->progressFinish();

        $this->io->success('All categories slugs were fixed.');
    }

    /**
     * @param  string $string
     * @param  int|int $length
     *
     * @return string
     */
    protected function generateString(string $string, int $length = 5): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $string . '-' . $randomString;
    }
}
