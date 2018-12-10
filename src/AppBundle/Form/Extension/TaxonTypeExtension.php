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

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class TaxonTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.enabled',
            ])
            ->add('filterEnabled', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.filter_enabled',
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
    public function getExtendedType()
    {
        return TaxonType::class;
    }
}
