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

use BitBag\SyliusCmsPlugin\Form\Type\Translation\TextBlockTranslationType;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class TextBlockTranslationTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->remove('content');
        $builder
            ->add('name', TextType::class, [
                'label' => 'bitbag_sylius_cms_plugin.ui.name',
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('content', FroalaEditorType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
                'label' => 'bitbag_sylius_cms_plugin.ui.content',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return TextBlockTranslationType::class;
    }
}
