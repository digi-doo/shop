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

class AppReleaseInfoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:release-info')
            ->setDescription('Notify admins about new SShop release!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Send release info to all admins
        $this->getContainer()->get('app.release_sender')->sendReleaseInfoToAdmins();

        // Output
        $output->writeln('<info>Info about new release was sent to all admins!</info>');
    }
}
