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

use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ProductAttributeTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filterable', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.filterable',
            ])
            ->add('rangeable', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.rangeable',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ProductAttributeType::class;
    }
}
