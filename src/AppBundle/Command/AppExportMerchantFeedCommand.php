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

use AppBundle\Repository\ProductVariantRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppExportMerchantFeedCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:export-merchant-feed')
            ->setDescription('Export taxonomy for given merchant.')
            ->addArgument('merchant', InputArgument::REQUIRED, 'Please provide required merchant name.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $merchant = $input->getArgument('merchant');
        $date = new \DateTime();
        $repo = $this->getContainer()->get('sylius.repository.product_variant');

        if ($merchant == 'heureka' && $this->getContainer()->getParameter('app.feed.heureka.enabled')) {
            $output->writeln($this->exportHeurekaFeed($repo, $date, $output));
        } elseif ($merchant == 'zbozi' && $this->getContainer()->getParameter('app.feed.zbozi.enabled')) {
            $output->writeln($this->exportZboziFeed($repo, $date, $output));
        } elseif ($merchant == 'google' && $this->getContainer()->getParameter('app.feed.google.enabled')) {
            $output->writeln($this->exportGoogleFeed($repo, $date, $output));
        } elseif ($merchant == 'facebook' && $this->getContainer()->getParameter('app.feed.facebook.enabled')) {
            $output->writeln($this->exportFacebookFeed($repo, $date, $output));
        } else {
            $output->writeln('<error>Given merchant was not found or is not enabled.</error>');
        }
    }

    /**
     * Resolve export for heureka.cz feed
     *
     * @param ProductVariantRepository $prodRepo
     * @param \DateTime $date
     *
     * @return string $message
     */
    protected function exportHeurekaFeed(ProductVariantRepository $variantRepo, \DateTime $date, OutputInterface $output)
    {
        $productVariants = $variantRepo->findByVariantAndProductEnabled();
        $feedHelper = $this->getContainer()->get('app.heureka_feed_helper');
        $heurekaHelper = $this->getContainer()->get('app.heureka_feed');

        $progress = new ProgressBar($output, count($productVariants));
        foreach ($productVariants as $variant) {
            $heurekaHelper->addProductVariant($variant, $feedHelper);
            $progress->advance();
        }
        $progress->finish();

        $feedHelper->save($this->getContainer()->get('kernel')->getProjectDir() . '/web/feed/' . 'heureka.xml');

        return [
            ' ',
            '<info>Successfully exported ' . count($productVariants) . ' product variants for Heureka.cz.</info>',
        ];
    }

    /**
     * Resolve export for zbozi.cz feed
     *
     * @param ProductVariantRepository $prodRepo
     * @param \DateTime $date
     *
     * @return string $message
     */
    protected function exportZboziFeed(ProductVariantRepository $variantRepo, \DateTime $date, OutputInterface $output)
    {
        $productVariants = $variantRepo->findByVariantAndProductEnabled();
        $feedHelper = $this->getContainer()->get('app.zbozi_feed_helper');
        $heurekaHelper = $this->getContainer()->get('app.zbozi_feed');

        $progress = new ProgressBar($output, count($productVariants));
        foreach ($productVariants as $variant) {
            $heurekaHelper->addProductVariant($variant, $feedHelper);
            $progress->advance();
        }
        $progress->finish();

        $feedHelper->save($this->getContainer()->get('kernel')->getProjectDir() . '/web/feed/' . 'zbozi.xml');

        return [
            ' ',
            '<info>Successfully exported ' . count($productVariants) . ' product variants for Zbozi.cz.</info>',
        ];
    }

    /**
     * Resolve export for facebook feed
     *
     * @param ProductVariantRepository $prodRepo
     * @param \DateTime $date
     *
     * @return string $message
     */
    protected function exportFacebookFeed(ProductVariantRepository $variantRepo, \DateTime $date, OutputInterface $output)
    {
        $productVariants = $variantRepo->findByVariantAndProductEnabled();
        $feedHelper = $this->getContainer()->get('app.google_feed_helper');
        $googleHelper = $this->getContainer()->get('app.google_feed');

        $progress = new ProgressBar($output, count($productVariants));
        foreach ($productVariants as $variant) {
            $googleHelper->addProductVariant($variant, $feedHelper);
            $progress->advance();
        }
        $progress->finish();

        $feedHelper->save($this->getContainer()->get('kernel')->getProjectDir() . '/web/feed/' . 'facebook.xml');

        return [
            ' ',
            '<info>Successfully exported ' . count($productVariants) . ' product variants for Facebook feed.</info>',
        ];
    }

    /**
     * Resolve export for Google feed
     *
     * @param ProductVariantRepository $prodRepo
     * @param \DateTime $date
     *
     * @return string $message
     */
    protected function exportGoogleFeed(ProductVariantRepository $variantRepo, \DateTime $date, OutputInterface $output)
    {
        $productVariants = $variantRepo->findByVariantAndProductEnabled();
        $feedHelper = $this->getContainer()->get('app.google_feed_helper');
        $googleHelper = $this->getContainer()->get('app.google_feed');

        $progress = new ProgressBar($output, count($productVariants));
        foreach ($productVariants as $variant) {
            $googleHelper->addProductVariant($variant, $feedHelper);
            $progress->advance();
        }
        $progress->finish();

        $feedHelper->save($this->getContainer()->get('kernel')->getProjectDir() . '/web/feed/' . 'google.xml');

        return [
            ' ',
            '<info>Successfully exported ' . count($productVariants) . ' product variants for Google feed.</info>',
        ];
    }
}
