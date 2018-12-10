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

namespace AppBundle\Form\Type;

use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

final class OrderInternalNoteApproveType extends AbstractType
{
    /**
     * @var AdminUserInterface
     */
    private $admin;

    /**
     * @param TokenStorage $tokenStogare
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->admin = $tokenStorage->getToken()->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $note = $event->getData();

                $approvedBy = null;
                if (null === $note->getApprovedBy()) {
                    $approvedBy = $this->admin;
                }
                $note->setApprovedBy($approvedBy);

                $approvedAt = null;
                if (null === $note->getApprovedAt()) {
                    $approvedAt = new \DateTime();
                }
                $note->setApprovedAt($approvedAt);

                $event->setData($note);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_order_internal_comment_approve';
    }
}
