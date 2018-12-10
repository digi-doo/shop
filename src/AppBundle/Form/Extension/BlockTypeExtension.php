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

use AppBundle\Form\Type\TagAutocompleteChoiceType;
use AppBundle\Form\Type\TextBlockTranslationType;
use BitBag\SyliusCmsPlugin\Form\Type\BlockType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class BlockTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ('collection' === $builder->getData()->getType()) {
            $builder->add('tabType', ChoiceType::class, [
                'label' => false,
                'multiple' => false,
                'expanded' => true,
                'required' => true,
                'choices' => [
                    'Výběr kategorie' => 'taxon',
                    'Výběr štítku' => 'tag',
                ],
            ]);
            $builder->add('tag', TagAutocompleteChoiceType::class, [
                'label' => 'app.ui.tag',
                'multiple' => false,
                'required' => false,
            ]);
            $builder->add('taxon', TaxonAutocompleteChoiceType::class, [
                'label' => 'app.ui.taxon',
                'multiple' => false,
                'required' => false,
            ]);

            // Add taxon autocomplete
            $builder->add('translations', ResourceTranslationsType::class, [
                'label' => 'bitbag_sylius_cms_plugin.ui.contents',
                'entry_type' => TextBlockTranslationType::class,
                'constraints' => [
                    new Valid(),
                ],
            ]);

            return;
        }

        if ('products' === $builder->getData()->getType()) {
            // Products with discount
            $builder->add('translations', ResourceTranslationsType::class, [
                'label' => 'bitbag_sylius_cms_plugin.ui.contents',
                'entry_type' => TextBlockTranslationType::class,
                'constraints' => [
                    new Valid(),
                ],
            ]);

            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return BlockType::class;
    }
}
