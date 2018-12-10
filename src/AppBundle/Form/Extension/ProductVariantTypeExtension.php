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

use AppBundle\Form\Type\SupplierChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('taxCategory');
        $builder->remove('tracked');
        $builder->remove('onHand');

        // Tax Category options
        $taxCategoryOptions = [
            'required' => true,
            'label' => 'sylius.form.product_variant.tax_category',
        ];

        // Supplier options
        $supplierOptions = [
            'required' => true,
            'label' => 'app.ui.choose_supplier',
        ];

        // Tracked options
        $trackedOptions = [
            'label' => 'sylius.form.variant.tracked',
        ];

        // OnHand options
        $onHandOptions = [
            'label' => 'sylius.form.variant.on_hand',
        ];

        if (array_key_exists('data', $options)) {
            // Product default options
            $productDefault = $options['data']->getProduct()->getProductDefaults()->first();
            $newVariant = $options['data']->getChannelPricings()->isEmpty();

            $defaultTaxCategory = [];
            if ($productDefault && $productDefault->getTaxCategory() != null && $newVariant) {
                $defaultTaxCategory = ['data' => $productDefault->getTaxCategory()];
            }

            $defaultSupplier = [];
            if ($productDefault && $productDefault->getSupplier() != null && $newVariant) {
                $defaultSupplier = ['data' => $productDefault->getSupplier()];
            }

            $defaultTracked = [];
            if ($productDefault && $productDefault->getTracked() != null && $newVariant) {
                $defaultTracked = ['data' => $productDefault->getTracked()];
            }

            $defaultOnHand = [];
            if ($productDefault && $productDefault->getOnHand() != null && $newVariant) {
                $defaultOnHand = ['data' => $productDefault->getOnHand()];
            }

            // There are some default product options
            $builder->add('taxCategory', TaxCategoryChoiceType::class, array_merge($taxCategoryOptions, $defaultTaxCategory));
            $builder->add('supplier', SupplierChoiceType::class, array_merge($supplierOptions, $defaultSupplier));
            $builder->add('tracked', CheckboxType::class, array_merge($trackedOptions, $defaultTracked));
            $builder->add('onHand', IntegerType::class, array_merge($onHandOptions, $defaultOnHand));

            // Extra fields
            $builder->add('negativeStock', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.negative_stock',
            ]);
            $builder->add('externalCode', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.external_code',
            ]);
        } else {
            // There are no product default options
            $builder->add('taxCategory', TaxCategoryChoiceType::class, $taxCategoryOptions);
            $builder->add('supplier', SupplierChoiceType::class, $supplierOptions);
            $builder->add('tracked', CheckboxType::class, $trackedOptions);
            $builder->add('onHand', IntegerType::class, $onHandOptions);

            // Extra fields
            $builder->add('negativeStock', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.negative_stock',
            ]);
            $builder->add('externalCode', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.external_code',
            ]);
        }

        $builder->add('ean', TextType::class, [
            'required' => false,
            'label' => 'sylius.ui.ean',
        ]);

        // Hidden reserved count for API calls
        $builder->add('onHold', HiddenType::class);

        $builder->add('enabled', CheckboxType::class, [
            'label' => 'sylius.form.country.enabled',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ProductVariantType::class;
    }
}
