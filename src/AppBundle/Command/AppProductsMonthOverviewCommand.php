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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppProductsMonthOverviewCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:products:monthOverview')
            ->setDescription('Send month overview about added products.')
            ->addArgument('month', InputArgument::REQUIRED, 'Please provide desired month in 01 format.')
            ->addArgument('year', InputArgument::REQUIRED, 'Please provide desired year 2000 format.')
            ->addArgument('email', InputArgument::REQUIRED, 'Please provide a valid e-mail address.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Input arguments
        $month = $input->getArgument('month');
        $year = $input->getArgument('year');
        $email = $input->getArgument('email');

        // Check if email is in correct format
        $date = $month . '-' . $year;
        if (!$this->validateDate($date)) {
            throw new \InvalidArgumentException('Argument ' . $month . ' or ' . $year . ' is invalid date format.');
        }

        // Check if date is in correct format
        if (!$this->validateEmail($email)) {
            throw new \InvalidArgumentException('Argument ' . $email . ' is invalid e-mail format.');
        }

        // Set checker and products
        $this->getContainer()->get('app.products_checker')->check($email, $month, $year);

        // Output
        $output->writeln('<info>Month overview about added products was sent to ' . $email . '</info>');
    }

    /**
     * Validate e-mail
     *
     * @param string $email
     *
     * @return bool
     **/
    protected function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    /**
     * Validate date in month and year format
     *
     * @param string $date
     *
     * @return bool
     **/
    protected function validateDate($date)
    {
        $d = \DateTime::createFromFormat('m-Y', $date);

        if ($d && $d->format('m-Y') == $date) {
            return true;
        }

        return false;
    }
}
