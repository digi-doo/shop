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

namespace AppBundle\Event\Listener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class AdminCreateListener
{
    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * Set email sender
     *
     * @param SenderInterface $emailSender
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(SenderInterface $emailSender, ChannelContextInterface $channelContext)
    {
        $this->emailSender = $emailSender;
        $this->channelContext = $channelContext;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function beforeAdminCreate(ResourceControllerEvent $event): void
    {
        /** @var AdminUserInterface $admin */
        $admin = $event->getSubject();

        /** @var AdminUserInterface $admin */
        Assert::isInstanceOf($admin, AdminUserInterface::class);

        // Send to superadmin about new admin user
        $this->sendSuperAdminEmail($admin);

        // Send email to created admin user
        $this->sendAdminEmail($admin);
    }

    /**
     * @param AdminUserInterface $order
     */
    public function sendSuperAdminEmail(AdminUserInterface $admin): void
    {
        $superAdminEmail = $this->channelContext->getChannel()->getSuperAdminEmail();
        $channel = $this->channelContext->getChannel()->getName();

        $data['admin'] = $admin;
        $data['channel'] = $channel;
        $this->emailSender->send('superadmin_new_admin', [$superAdminEmail], $data);
    }

    /**
     * @param AdminUserInterface $admin
     */
    public function sendAdminEmail(AdminUserInterface $admin): void
    {
        $channel = $this->channelContext->getChannel()->getName();

        $data['admin'] = $admin;
        $data['channel'] = $channel;
        $this->emailSender->send('new_admin', [$admin->getEmail()], $data);
    }
}
