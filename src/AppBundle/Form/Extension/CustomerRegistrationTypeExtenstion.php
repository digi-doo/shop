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

namespace AppBundle\Form\Extension;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerRegistrationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerRegistrationTypeExtenstion extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('recaptcha', EWZRecaptchaType::class, [
            'attr' => [
                'options' => [
                    'theme' => 'light',
                    'type' => 'image',
                    'size' => 'normal',
                ],
            ],
            'mapped' => false,
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
    public function getExtendedType()
    {
        return CustomerRegistrationType::class;
    }
}
