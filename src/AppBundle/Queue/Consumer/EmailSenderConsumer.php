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
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class EmailSenderConsumer implements ConsumerInterface
{
    /**
     * @var SentrySymfonyClient
     */
    private $sentry;

    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @var RepositoryInterface
     */
    private $orderRepo;

    /**
     * @param SentrySymfonyClient $sentry
     * @param SenderInterface $emailSender
     * @param RepositoryInterface $orderRepo
     */
    public function __construct(
        SentrySymfonyClient $sentry,
        SenderInterface $emailSender,
        RepositoryInterface $orderRepo
    ) {
        $this->sentry = $sentry;
        $this->emailSender = $emailSender;
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
            if ($body['mail_type'] === 'order') {
                $this->sendOrderEmail($body);
            } else {
                $this->sendEmail($body);
            }

            // Message is ok - drop it
            return ConsumerInterface::MSG_ACK;
        } catch (\Exception $e) {
            // Send exception to sentry
            $this->sentry->captureException($e);

            // Message is not ok - reject and drop it
            return ConsumerInterface::MSG_REJECT_REQUEUE;
        }
    }

    /**
     * @param  array  $body
     */
    private function sendEmail(array $body): void
    {
        $this->emailSender->send(
            $body['mail_code'],
            $body['mail_recipients'],
            $body['mail_data'],
            $body['mail_attachments']
        );
    }

    /**
     * @param  array  $body
     */
    private function sendOrderEmail(array $body): void
    {
        $this->emailSender->send(
            $body['mail_code'],
            $body['mail_recipients'],
            ['order' => $this->orderRepo->findOneById($body['mail_data'])],
            $body['mail_attachments']
        );
    }
}
