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

use Doctrine\ORM\OptimisticLockException;
use GuzzleHttp\Exception\RequestException;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppDispatchVariantUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:dispatch-variant-update')
            ->setDescription('Dispatch product variant update on each varint found in database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $variants = $this->getContainer()->get('sylius.repository.product_variant')->findAll();
        $variantManager = $this->getContainer()->get('sylius.manager.product_variant');
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');
        $variantsCount = count($variants);
        $progressBar = new ProgressBar($output, $variantsCount);

        $progressBar->start();
        $batchCount = 0;
        $failedCount = 0;
        foreach ($variants as $variant) {
            try {
                $eventDispatcher->dispatch(
                    'sylius.product_variant.pre_update',
                    new ResourceControllerEvent($variant)
                );

                $variantManager->persist($variant);
            } catch (RequestException $e) {
                $output->writeln($e->getResponse()->getReasonPhrase() . ' ' . $e->getResponse()->getStatusCode() . ', variant external code: ' . $variant->getExternalCode() . ', product code ' . $variant->getProduct()->getCode() . '.');
                ++$failedCount;

                continue;
            } catch (OptimisticLockException $e) {
                $output->writeln('ENTITY LOCK, variant external code: ' . $e->getEntity()->getExternalCode() . ', product code ' . $e->getEntity()->getProduct()->getCode() . '.');
                ++$failedCount;

                continue;
            }

            ++$batchCount;
            if ($batchCount > 50) {
                $variantManager->flush();
                $batchCount = 0;
            }
            $progressBar->advance();
        }
        $variantManager->flush();
        $progressBar->finish();

        $output->writeln(' ');
        $output->writeln('<options=bold,underscore>Dispatched variant_update event on ' . $variantsCount . ' variants.</>');
        if ($failedCount > 0) {
            $output->writeln('<options=bold,underscore>Failed: ' . $failedCount . '</>');
        }
    }
}
