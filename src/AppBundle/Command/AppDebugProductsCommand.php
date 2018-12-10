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
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppDebugProductsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:debug-products')
            ->setDescription('Debug and discover badly configured products - missing variants, missing options etc.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $prodRepo = $this->getContainer()->get('sylius.repository.product');
        $prods = $prodRepo->findAll();
        $outputStyle = new OutputFormatterStyle('white', 'red', ['bold', 'blink']);
        $output->getFormatter()->setStyle('error', $outputStyle);

        $errors = 0;
        foreach ($prods as $prod) {
            if (!$prod->isSimple()) {
                $variants = $prod->getVariants();
                $options = $prod->getOptions();

                if ($variants->isEmpty()) {
                    $output->writeln('<error>Configurable product with code ' . $prod->getCode() . ' doesn\'t have any variants!</>');
                    ++$errors;
                }

                if ($options->isEmpty()) {
                    $output->writeln('<error>Configurable product with code ' . $prod->getCode() . ' doesn\'t have any options!</>');
                    ++$errors;
                }

                foreach ($variants as $variant) {
                    if ($variant->getOptionValues()->isEmpty()) {
                        $output->writeln('<error>Configurable product with code ' . $prod->getCode() . ' doesn\'t have any option values!</>');
                        ++$errors;
                    }
                }
            } else {
                $variants = $prod->getVariants();

                if ($prod->hasOptions()) {
                    $output->writeln('<error>Simple product with code ' . $prod->getCode() . ' has options? Wtf?</>');
                    ++$errors;
                }

                if ($variants->count() > 1) {
                    $output->writeln('<error>Simple product with code ' . $prod->getCode() . ' has more than 1 variant? Wtf?</>');
                    ++$errors;
                }

                if ($variants->count() < 1) {
                    $output->writeln('<error>Simple product with code ' . $prod->getCode() . ' has no variants at all? Wtf?</>');
                    ++$errors;
                }
            }
        }

        if (!$errors > 0) {
            $output->writeln('Nothing to see here, move along people.');
        }
    }
}
