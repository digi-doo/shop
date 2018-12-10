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

use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ProductOptionTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('values')
            ->add('filterable', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.filterable',
            ])
            ->add('values', CollectionType::class, [
                'entry_type' => ProductOptionValueType::class,
                'allow_add' => true,
                'by_reference' => false,
                'label' => false,
                'button_add_label' => 'sylius.form.option_value.add_value',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ProductOptionType::class;
    }
}
