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
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonTranslationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class TaxonTranslationTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('description')
            ->add('metaKeywords', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.product.meta_keywords',
            ])
            ->add('metaDescription', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.product.meta_description',
            ])
            ->add('description', FroalaEditorType::class, [
                'required' => false,
                'label' => 'sylius.form.taxon.description',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return TaxonTranslationType::class;
    }
}
