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

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class OrderCompleteListener
{
    /**
     * @var ProducerInterface
     */
    private $emailSenderProducer;

    /**
     * @var ProducerInterface
     */
    private $heurekaOverenoProducer;

    /**
     * @var string|null
     */
    private $heurekaOverenoKey;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param ProducerInterface $emailSenderProducer
     * @param ProducerInterface $heurekaOverenoProducer
     * @param string|null $heurekaOverenoKey
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(
        ProducerInterface $emailSenderProducer,
        ProducerInterface $heurekaOverenoProducer,
        ?string $heurekaOverenoKey,
        ChannelContextInterface $channelContext
    ) {
        $this->emailSenderProducer = $emailSenderProducer;
        $this->heurekaOverenoProducer = $heurekaOverenoProducer;
        $this->heurekaOverenoKey = $heurekaOverenoKey;
        $this->channelContext = $channelContext;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function sendAdminEmails(ResourceControllerEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->emailSenderProducer->setContentType('application/json');
        $this->emailSenderProducer->publish(json_encode([
                'mail_type' => 'order',
                'mail_code' => 'admins_order_info',
                'mail_recipients' => $this->channelContext->getChannel()->getNotificationAdminOrderEmails(),
                'mail_data' => $order->getId(),
                'mail_attachments' => [],
            ]
        ));
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function sendHeurekaOvereno(ResourceControllerEvent $event): void
    {
        if (!$this->heurekaOverenoKey) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $event->getSubject();

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->heurekaOverenoProducer->setContentType('application/json');
        $this->heurekaOverenoProducer->publish(json_encode([
                'order_id' => $order->getId(),
                'heureka_overeno_key' => $this->heurekaOverenoKey,
            ]
        ));
    }
}
