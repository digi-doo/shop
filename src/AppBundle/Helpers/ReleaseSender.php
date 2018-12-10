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

namespace AppBundle\Helpers;

use Sylius\Bundle\ShopBundle\Router\LocaleStrippingRouter;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ReleaseSender
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @var RepositoryInterface
     */
    private $adminUserRepo;

    /**
     * @var LocaleStrippingRouter
     */
    private $router;

    /**
     * @param ChannelContextInterface $channelContext
     * @param SenderInterface $emailSender
     * @param RepositoryInterface $adminUserRepo
     */
    public function __construct(
        ChannelContextInterface $channelContext,
        SenderInterface $emailSender,
        RepositoryInterface $adminUserRepo,
        LocaleStrippingRouter $router
    ) {
        $this->channelContext = $channelContext;
        $this->emailSender = $emailSender;
        $this->adminUserRepo = $adminUserRepo;
        $this->router = $router;

        $context = $this->router->getContext();
        $context->setHost($this->channelContext->getChannel()->getHostname());
    }

    public function sendReleaseInfoToAdmins(): void
    {
        $shopName = $this->channelContext->getChannel()->getName();
        $admins = $this->adminUserRepo->findAll();

        foreach ($admins as $admin) {
            if ($admin->hasRole(AdminUserInterface::DEFAULT_ADMIN_ROLE)) {
                $this->emailSender->send('new_release_info', [$admin->getEmail()], ['shop_name' => $shopName, 'releases_url' => $this->router->generate('app_admin_release', [], UrlGeneratorInterface::ABSOLUTE_URL)]);
            }
        }
    }
}
