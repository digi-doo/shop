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

use BitBag\SyliusCmsPlugin\Form\Type\Translation\PageTranslationType;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class PageTranslationTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->remove('metaKeywords');
        $builder->remove('metaDescription');
        $builder->remove('content');

        $builder->add('metaKeywords', TextType::class, [
                'label' => 'bitbag_sylius_cms_plugin.ui.meta_keywords',
                'required' => false,
            ])
            ->add('metaDescription', TextType::class, [
                'label' => 'bitbag_sylius_cms_plugin.ui.meta_description',
                'required' => false,
            ])
            ->add('content', FroalaEditorType::class, [
                'label' => 'bitbag_sylius_cms_plugin.ui.content',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return PageTranslationType::class;
    }
}
