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

namespace AppBundle\Helpers\Product;

use AppBundle\Repository\ProductRepository;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ProductsChecker
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
     * Check if there is added products for given month
     *
     * @param string $email
     * @param DateTime $date
     *
     * @return self
     **/
    public function check($email, $month, $year)
    {
        // Find all products added in given month and year
        $allProducts = $this->productRepo->findByMonthAndYear($month, $year);

        // Send notification to given authorities
        $this->sendNotification($this->resolveProductsCounting($allProducts), $email, $month, $year);

        return $this;
    }

    /**
     * Resolve products counting
     *
     * @param ProductInterface
     *
     * @return []
     **/
    public function resolveProductsCounting($allProducts)
    {
        // Zero counting
        $sp = 0;
        $cp = 0;
        $cv = 0;

        foreach ($allProducts as $product) {
            if ($product->isSimple()) {
                ++$sp;
            } else {
                ++$cp;
                $cv += $product->getVariants()->count();
            }
        }

        $data = [];
        $data['simpleProducts'] = $sp;
        $data['confProducts'] = $cp;
        $data['confVariants'] = $cv;

        return $data;
    }

    /**
     * Send notification about added products via email
     *
     * @param string $email
     * @param DateTime $date
     *
     * @return self
     **/
    public function sendNotification($data, $email, $month, $year)
    {
        $data['name'] = $this->channelContext->getChannel()->getName();
        $data['month'] = $month;
        $data['year'] = $year;

        $this->emailSender->send('month_products_overview', [$email], $data);

        return $this;
    }
}
