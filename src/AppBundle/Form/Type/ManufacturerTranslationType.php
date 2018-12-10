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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ManufacturerTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'sylius.ui.name',
                'required' => true,
            ])
            ->add('slug', TextType::class, [
                'label' => 'sylius.ui.slug',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'sylius.ui.description',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_manufacturer_translation';
    }
}
