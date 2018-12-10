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

class AppCheckProductAttributesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:check-product-attributes')
            ->setDescription('Check duplicity of product attributes.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $products = $this->getContainer()->get('sylius.repository.product')->findAll();

        $io->progressStart(count($products));
        $duplicities = [];
        foreach ($products as $product) {
            $values = $product->getAttributes();
            $attrIds = [];
            foreach ($values as $value) {
                $attrIds[] = $value->getAttribute()->getId();
            }

            if (count($attrIds) !== count(array_unique($attrIds))) {
                if (!in_array($product->getId(), $duplicities)) {
                    $duplicities[] = $product->getId();
                }
            }

            $io->progressAdvance();
        }
        $io->progressFinish();

        if (!empty($duplicities)) {
            $io->error('Products with id\'s ' . (implode(', ', $duplicities)) . ' hasn\'t unique attributes!');
        } else {
            $io->success('Products hasn unique attribtues!');
        }
    }
}
