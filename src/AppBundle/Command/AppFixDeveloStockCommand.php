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

use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppFixDeveloStockCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:fix-develo-stock')
            ->setDescription('Fix invalid stock synchronization with develo app.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $variantRepo = $this->getContainer()->get('sylius.repository.product_variant');
        $variantManager = $this->getContainer()->get('sylius.manager.product_variant');

        $notNullCriteria = new Criteria();
        $notNullCriteria->where(Criteria::expr()->neq('externalCode', null));
        $synchronizedVariants = $variantRepo->matching($notNullCriteria);
        $synchronizedVariantsCount = count($synchronizedVariants);

        $nullCriteria = new Criteria();
        $nullCriteria->where(Criteria::expr()->eq('externalCode', null));
        $unSynchronizedVariants = $variantRepo->matching($nullCriteria);
        $unSynchronizedVariantsCount = count($unSynchronizedVariants);

        if ($unSynchronizedVariantsCount > 0) {
            $codes = [];
            foreach ($unSynchronizedVariants as $variant) {
                $codes[] = $variant->getCode();
            }
            $io->note('Found ' . $unSynchronizedVariantsCount . ' unsynchronized variants. Unsynchronized variants codes: ' . implode(', ', $codes) . '.');
        }

        if ($synchronizedVariantsCount > 0) {
            $io->note('Found ' . $synchronizedVariantsCount . ' synchronized variants.');
        }

        // Prepare codes array to checks
        $io->newLine();
        $io->note('Preparing external variants codes to check...');
        $codes = [];
        $io->progressStart($synchronizedVariantsCount);
        foreach ($synchronizedVariants as $variant) {
            $codes[] = $variant->getExternalCode();
            $io->progressAdvance();
        }
        $io->progressFinish();

        // Prepare develo stock client
        $clientFactory = $this->getContainer()->get('czende.develo_shop_plugin.api');
        if ($this->getContainer()->getParameter('develo_rest_url')) {
            $clientFactory->setRestApiUrl($this->getContainer()->getParameter('develo_rest_url'));
        }
        $stockClient = $clientFactory->createStock();

        // Loop and fill invalid relations
        $chunkedCodes = array_chunk($codes, 100);
        $notOk = [];
        $i = 0;
        $invalidCount = 0;
        foreach ($chunkedCodes as $codes) {
            $stockItems = $stockClient->readItemsByCodes($codes);

            $io->note('Looping chunk number ' . $i);
            $io->progressStart(count($codes));
            $batchSize = 20;
            $iFlush = 0;
            foreach ($stockItems as $item) {
                $variant = $variantRepo->findOneByExternalCode($item->getCode());
                if (!$variant) {
                    throw new \Exception('Variant with extrenal code ' . $item->getCode() . ' not found in database!');
                }

                $stockCount = $variant->getOnHand();
                $develoCount = (int) $item->getCount();

                $stockRes = $variant->getOnHold();
                $develoRes = (int) $item->getReservedCount();

                if ($stockCount !== $develoCount || $stockRes !== $develoRes) {
                    // Fix new onHand or onHold
                    $variant->setOnHand($develoCount);
                    $variant->setOnHold($develoRes);
                    $variantManager->persist($variant);

                    if (($iFlush % $batchSize) === 0) {
                        $variantManager->flush();
                    }

                    ++$iFlush;
                    ++$invalidCount;
                    $io->progressAdvance();
                } else {
                    $io->progressAdvance();
                }
            }
            $variantManager->flush();
            ++$i;
            $io->progressFinish();
        }

        // Invalid synchronization
        if ($invalidCount > 0) {
            $io->error('Found ' . $invalidCount . ' invalid variants stock synchronizations.');
            $io->success($invalidCount . ' invalid variant stock synchronizations were successfully fixed.');
        } else {
            $io->success('Every variant has stock proper synchornized with external system.');
        }
    }
}
