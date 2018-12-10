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

namespace AppBundle\Form\Type\Filter;

use Symfony\Component\Form\FormBuilderInterface;

class ProductsFilterType extends AbstractFilterType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('manufacturers', ManufacturersFilterType::class, ['required' => false, 'label' => false])
            ->add('options', ProductOptionsFilterType::class, ['required' => false, 'label' => false])
            ->add('attributes', ProductAttributesFilterType::class, ['required' => false, 'label' => false])
            // ->add('price', PriceFilterType::class, ['required' => false, 'label' => false])
        ;
    }
}
