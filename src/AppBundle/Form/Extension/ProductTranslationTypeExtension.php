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

use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductTranslationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ProductTranslationTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('description')
            ->add('description', FroalaEditorType::class, [
                'required' => false,
                'label' => 'sylius.form.product.description',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ProductTranslationType::class;
    }
}
