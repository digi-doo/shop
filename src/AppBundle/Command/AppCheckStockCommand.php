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

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class AppCheckStockCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:check-stock')
            ->setDescription('Check low stock and send alert to authorities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stockChecker = $this->getContainer()->get('app.low_stock_checker');
        $products = $stockChecker->check();

        if (count($products) > 0) {
            $output->writeln('<error>Found ' . count($products) . ' products that have negative or low stock.</error>');
            $output->writeln('<error>Notification was sent to the authorities.</error>');
            $stockChecker->sendNotification();
        } else {
            $output->writeln('<info>All products have positive stock.</info>');
        }
    }
}
