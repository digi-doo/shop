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

use KunicMarko\ColorPickerBundle\Form\Type\ColorPickerType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class TagType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => TagTranslationType::class,
                'label' => 'sylius.ui.name',
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                // 'attr' => ['checked' => 'checked'],
                'label' => 'sylius.ui.enabled',
            ])
            ->add('color', ColorPickerType::class, [
                'required' => false,
                'attr' => ['class' => 'colorpicker'],
                'label' => 'sylius.ui.color',
            ])
            ->add('textColor', ColorPickerType::class, [
                'required' => false,
                'attr' => ['class' => 'colorpicker'],
                'label' => 'sylius.ui.text_color',
            ])
            ->add('code', TextType::class, [
                'required' => true,
                'label' => 'sylius.ui.code',
            ])
            ->add('mainTag', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.main_tag',
            ])
            ->add('stockSortingEnabled', CheckboxType::class, [
                'required' => false,
                'label' => 'app.ui.stock_sorting_enabled',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_tag';
    }
}
