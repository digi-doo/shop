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

use AppBundle\Validator\PhoneNumber;
use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Sylius\Bundle\AddressingBundle\Form\Type\CountryCodeChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class AddressTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('phoneNumber');
        $builder->remove('countryCode');

        $builder
            ->add('ic', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.address.ic',
            ])
            ->add('dic', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.address.dic',
            ])
            ->add('streetNumber', TextType::class, [
                'required' => true,
                'label' => 'sylius.form.address.street_number',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.address.street_number.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.address.title',
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => true,
                'label' => 'sylius.form.address.phone_number',
                'attr' => [
                     'placeholder' => '+420 123 456 789',
                     'data-mask-phone' => '',
                 ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.address.phone_number.not_blank',
                        'groups' => 'sylius',
                    ]),
                    new PhoneNumber([
                        'message' => 'sylius.address.phone_number.invalid',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('countryCode', CountryCodeChoiceType::class, [
                'label' => 'sylius.form.address.country',
                'enabled' => true,
                'required' => true,
                'placeholder' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return AddressType::class;
    }
}
