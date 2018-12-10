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

use FOS\RestBundle\Decoder\XmlDecoder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class AppImportMerchantTaxonomyCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:import-merchant-taxonomy')
            ->setDescription('Import taxonomy from given merchant.')
            ->addArgument('merchant', InputArgument::REQUIRED, 'Please provide required merchant name.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $merchant = $input->getArgument('merchant');
        $date = new \DateTime();

        if ($merchant == 'heureka' && $this->getContainer()->getParameter('app.feed.heureka.enabled')) {
            $output->writeln($this->resolveHeurekaTaxonomy($date));
        } elseif ($merchant == 'google' && $this->getContainer()->getParameter('app.feed.google.enabled')) {
            $output->writeln($this->resolveGoogleTaxonomy($date));
        } else {
            $output->writeln('<error>Given merchant was not found or is not enabled.</error>');
        }
    }

    /**
     * Resolve google taxonomy file
     *
     * @param \DateTime $date
     *
     * @return string $message
     */
    protected function resolveGoogleTaxonomy($date)
    {
        $googleUrl = $this->getContainer()->getParameter('app.feed.google.taxonomy_url');
        $taxons = file($googleUrl, FILE_IGNORE_NEW_LINES);

        // Delete first line comment
        array_shift($taxons);

        $taxonsArray = [];
        foreach ($taxons as $taxon) {
            $taxonsArray[] = explode('- ', $taxon);
        }

        $taxonsToPersist = [];
        $i = 0;
        foreach ($taxonsArray as $taxon) {
            foreach ($taxon as $element) {
                if (is_numeric(str_replace(' ', '', $element))) {
                    $taxonsToPersist[$i]['code'] = str_replace(' ', '', $element);
                } else {
                    $taxonsToPersist[$i]['name'] = $element;
                    $taxonsToPersist[$i]['date'] = $date;
                }

                if (!is_numeric(str_replace(' ', '', $element))) {
                    ++$i;
                }
            }
        }

        return $this->presistGoogleTaxonomy($taxonsToPersist);
    }

    /**
     * Resolve heureka taxonomy file
     *
     * @param \DateTime $date
     *
     * @return string $message
     */
    protected function resolveHeurekaTaxonomy($date)
    {
        $heurekaUrl = $this->getContainer()->getParameter('app.feed.heureka.taxonomy_url');

        $encoder = new XmlDecoder();
        $data = $encoder->decode(file_get_contents($heurekaUrl));
        $flat = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data)), 0);

        $taxons = [];
        $i = 0;
        foreach ($flat as $element) {
            if (is_numeric($element)) {
                $taxons[$i]['code'] = str_replace(' ', '', $element);
            } elseif (preg_match('/\|/', $element)) {
                $names = explode('|', $element);
                array_shift($names);
                $taxons[$i]['name'] = implode('| ', $names);
                $taxons[$i]['date'] = $date;
            }

            if (!is_numeric($element) && preg_match('/\|/', $element)) {
                ++$i;
            }
        }

        return $this->presistHeurekaTaxonomy($taxons);
    }

    /**
     * Persist heureka taxonomy
     *
     * @param array $data
     *
     * @return string $message
     */
    protected function presistHeurekaTaxonomy($data)
    {
        $heurekaFactory = $this->getContainer()->get('app.factory.heureka');
        $heurekaManager = $this->getContainer()->get('app.manager.heureka');
        $heurekaRepo = $this->getContainer()->get('app.repository.heureka');

        $count = 0;
        $nonChanged = 0;
        $updated = 0;
        $new = 0;
        foreach ($data as $taxon) {
            if (!isset($taxon['name'])) {
                continue;
            } // Heureka fail - last category doesn't have name
            // Nothing to change
            if ($heurekaRepo->findBy(['code' => $taxon['code'], 'name' => $taxon['name']])) {
                ++$nonChanged;

                continue;
            }

            // Update taxonomy name
            if ($heurekaRepo->findOneBy(['code' => $taxon['code']]) && !$heurekaRepo->findOneBy(['name' => $taxon['name']])) {
                $taxonToUpdate = $heurekaRepo->findOneBy(['code' => $taxon['code']]);
                $taxonToUpdate->setName($taxon['name']);

                $heurekaManager->persist($taxonToUpdate);

                ++$updated;
                if (0 === $updated % 100) {
                    $heurekaManager->flush();
                }

                continue;
            }

            // New taxonomies
            $heureka = $heurekaFactory->createNew();
            $heureka->setCode($taxon['code']);
            $heureka->setName($taxon['name']);
            $heureka->setDateImported($taxon['date']);

            $heurekaManager->persist($heureka);

            ++$count;
            if (0 === $count % 100) {
                $heurekaManager->flush();
            }

            ++$new;
        }

        $heurekaManager->flush();

        return '<info>None changed Heureka taxonomies: ' . $nonChanged . ', updated: ' . $updated . ' and new: ' . $new . '</info>';
    }

    /**
     * Persist google taxonomy
     *
     * @param array $data
     *
     * @return string $message
     */
    protected function presistGoogleTaxonomy($data)
    {
        $googleFactory = $this->getContainer()->get('app.factory.google');
        $googleManager = $this->getContainer()->get('app.manager.google');
        $googleRepo = $this->getContainer()->get('app.repository.google');

        $count = 0;
        $nonChanged = 0;
        $updated = 0;
        $new = 0;
        foreach ($data as $taxon) {
            // Nothing to change
            if ($googleRepo->findBy(['code' => $taxon['code'], 'name' => $taxon['name']])) {
                ++$nonChanged;

                continue;
            }

            // Update taxonomy name
            if ($googleRepo->findOneBy(['code' => $taxon['code']]) && !$googleRepo->findOneBy(['name' => $taxon['name']])) {
                $taxonToUpdate = $googleRepo->findOneBy(['code' => $taxon['code']]);
                $taxonToUpdate->setName($taxon['name']);

                $googleManager->persist($taxonToUpdate);

                ++$updated;
                if (0 === $updated % 100) {
                    $googleManager->flush();
                }

                continue;
            }

            // New taxonomies
            $google = $googleFactory->createNew();
            $google->setCode($taxon['code']);
            $google->setName($taxon['name']);
            $google->setDateImported($taxon['date']);

            $googleManager->persist($google);

            ++$count;
            if (0 === $count % 100) {
                $googleManager->flush();
            }

            ++$new;
        }

        $googleManager->flush();

        return '<info>None changed Google taxonomies: ' . $nonChanged . ', updated: ' . $updated . ' and new: ' . $new . '</info>';
    }
}
