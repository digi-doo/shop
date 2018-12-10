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

use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ChannelPricingTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Temporary remove original price
        $builder->remove('originalPrice');
        // Hack price to default price
        $builder->remove('price');

        $productDefault = $options['product_variant']->getProduct()->getProductDefaultForChannel($options['channel']);
        $variantDefault = $options['product_variant']->getChannelPricingForChannel($options['channel']);

        if (!$productDefault && $variantDefault) {
            $builder
                ->add('defaultPrice', MoneyType::class, [
                    'label' => 'sylius.ui.price',
                    'currency' => $options['channel']->getBaseCurrency()->getCode(),
                    'label_attr' => [
                        'style' => ($options['product_variant']->isSynchronized() ? 'opacity:0.5;' : ''),
                    ],
                    'attr' => [
                        'style' => ($options['product_variant']->isSynchronized() ? 'pointer-events:none;opacity:0.5;' : ''),
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'sylius.product_variant.price.not_blank',
                            'groups' => 'sylius',
                        ]),
                    ],
                ])
                ->add('realDiscount', PercentType::class, [
                    'label' => 'sylius.ui.percental_discount',
                ]);
        } elseif ($productDefault && !$variantDefault) {
            $builder
                ->add('defaultPrice', MoneyType::class, [
                    'label' => 'sylius.ui.price',
                    'currency' => $options['channel']->getBaseCurrency()->getCode(),
                    'data' => $productDefault->getPrice(),
                    'constraints' => [
                        new NotBlank([
                            'message' => 'sylius.product_variant.price.not_blank',
                            'groups' => 'sylius',
                        ]),
                    ],
                ])
                ->add('realDiscount', PercentType::class, [
                    'label' => 'sylius.ui.percental_discount',
                    'data' => $productDefault->getDiscount(),
                ]);
        } else {
            $builder
                ->add('defaultPrice', MoneyType::class, [
                    'label' => 'sylius.ui.price',
                    'currency' => $options['channel']->getBaseCurrency()->getCode(),
                    'label_attr' => [
                        'style' => ($options['product_variant']->isSynchronized() ? 'opacity:0.5;' : ''),
                    ],
                    'attr' => [
                        'style' => ($options['product_variant']->isSynchronized() ? 'pointer-events:none;opacity:0.5;' : ''),
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'sylius.product_variant.price.not_blank',
                            'groups' => 'sylius',
                        ]),
                    ],
                ])
                ->add('realDiscount', PercentType::class, [
                    'label' => 'sylius.ui.percental_discount',
                ]);
        }

        $builder->add('discountLimitType', ChoiceType::class, [
            'label' => false,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
            'choices' => [
                'sylius.ui.discount_limit_none' => null,
                'sylius.ui.discount_limit_stock' => 'stock',
                'sylius.ui.discount_limit_datetime' => 'datetime',
            ],
        ]);

        $builder
            ->add('discountFrom', DateType::class, [
                'label' => 'sylius.ui.from',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('discountTo', DateType::class, [
                'label' => 'sylius.ui.to',
                'widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ChannelPricingType::class;
    }
}
