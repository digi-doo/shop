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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FloatType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new NumberToLocalizedStringTransformer(null, $options['type']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'scale' => null,
            'type' => 'fractional',
            'compound' => false,
        ]);

        $resolver->setAllowedValues('type', [
            'fractional',
            'integer',
        ]);

        $resolver->setDefined('locale_code');

        // $resolver->setAllowedTypes('scale', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'float';
    }
}
