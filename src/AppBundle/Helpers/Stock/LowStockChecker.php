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

namespace AppBundle\Helpers\Stock;

use AppBundle\Repository\ProductRepository;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class LowStockChecker
{
    /**
     * @var ProductRepository
     */
    private $productRepo;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * Inject dependencies
     *
     * @param ProductRepository $productRepo
     * @param ChannelContextInterface $channelContext
     * @param SenderInterface $emailSender
     */
    public function __construct(ProductRepository $productRepo,
                                ChannelContextInterface $channelContext,
                                SenderInterface $emailSender)
    {
        $this->productRepo = $productRepo;
        $this->channelContext = $channelContext;
        $this->emailSender = $emailSender;
    }

    /**
     * Get all producst with onHand <= 0
     *
     * @return array
     **/
    public function check()
    {
        return $this->productRepo->findByOnHand();
    }

    /**
     * Send notification about low stock via email
     *
     * @return self
     **/
    public function sendNotification()
    {
        $products = $this->check();
        $url = $this->channelContext->getChannel()->getHostName();
        $name = $this->channelContext->getChannel()->getName();
        $emails = $this->channelContext->getChannel()->getNotificationStockEmails();

        $this->emailSender->send('low_stock_info', $emails, ['url' => $url, 'name' => $name, 'products' => $products]);

        return $this;
    }
}
