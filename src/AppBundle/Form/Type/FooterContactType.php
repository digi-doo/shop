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

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class FooterContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'procamping.form.full_name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.contact.email.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'procamping.form.email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.contact.email.not_blank',
                        'groups' => 'sylius',
                    ]),
                    new Email([
                        'message' => 'sylius.contact.email.invalid',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'procamping.form.message',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.contact.message.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('recaptcha', EWZRecaptchaType::class, [
                'attr' => [
                    'options' => [
                        'theme' => 'light',
                        'type' => 'image',
                        'size' => 'normal',
                    ],
                ],
                'constraints' => [
                    new RecaptchaTrue([
                        'message' => 'sylius.recaptcha.approve',
                        'groups' => 'sylius',
                    ]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_contact';
    }
}
