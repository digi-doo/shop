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
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class TextBlockTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder->remove('content');
        $builder
            ->add('name', TextType::class, [
                'label' => 'bitbag_sylius_cms_plugin.ui.name',
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('content', TextAreaType::class, [
                'required' => false,
                'label' => 'bitbag_sylius_cms_plugin.ui.content',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'bitbag_sylius_cms_plugin_text_block_translation';
    }
}
