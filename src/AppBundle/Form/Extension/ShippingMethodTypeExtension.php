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

use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ShippingMethodTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('taxCategory')
            ->add('taxCategory', TaxCategoryChoiceType::class, [
                'required' => true,
                'label' => 'sylius.form.shipping_method.tax_category',
            ])
            ->add('paymentMethods', PaymentMethodChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'label' => 'sylius.ui.possible_payments',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ShippingMethodType::class;
    }
}
