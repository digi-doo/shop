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

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class PaymentMethodTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('price', MoneyType::class, [
            'label' => 'sylius.ui.price',
            // 'required' => false,
            // DIRTY hardcoded - on startup we dont have channel yet
            'currency' => 'CZK',
        ]);

        $builder->add('taxCategory', TaxCategoryChoiceType::class, [
            // 'required' => false,
            'label' => 'sylius.form.product_variant.tax_category',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return PaymentMethodType::class;
    }
}
