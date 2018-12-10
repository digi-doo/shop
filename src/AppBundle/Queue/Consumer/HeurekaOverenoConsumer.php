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

namespace AppBundle\Queue\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Sentry\SentryBundle\SentrySymfonyClient;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class HeurekaOverenoConsumer implements ConsumerInterface
{
    /**
     * @var SentrySymfonyClient
     */
    private $sentry;

    /**
     * @var RepositoryInterface
     */
    private $orderRepo;

    /**
     * @param SentrySymfonyClient $sentry
     * @param RepositoryInterface $orderRepo
     */
    public function __construct(
        SentrySymfonyClient $sentry,
        RepositoryInterface $orderRepo
    ) {
        $this->sentry = $sentry;
        $this->orderRepo = $orderRepo;
    }

    /**
     * @param   AMQPMessage $message
     *
     * @return  int
     *
     * @throws  \Exception
     */
    public function execute(AMQPMessage $message)
    {
        $body = json_decode($message->body, true);

        try {
            $this->sendHeurekaOvereno($body);

            // Message is ok - drop it
            return ConsumerInterface::MSG_ACK;
        } catch (\Exception $e) {
            // Send exception to sentry
            $this->sentry->captureException($e);

            // Message is not ok - reject and drop it
            return ConsumerInterface::MSG_REJECT;
        }
    }

    /**
     * @param array $body
     */
    public function sendHeurekaOvereno(array $body): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepo->findOneById($body['order_id']);

        // Required
        $shopCertification = new \Heureka\ShopCertification($body['heureka_overeno_key']);
        $shopCertification->setEmail($order->getCustomer()->getEmail());

        // Optional order ID
        $shopCertification->setOrderId((int) $order->getNumber());

        // Optional ordered items ID's
        foreach ($order->getItems() as $item) {
            $shopCertification->addProductItemId($item->getVariant()->getCode());
        }

        $shopCertification->logOrder();
    }
}
