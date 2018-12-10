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
use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantGenerationType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ProductVariantGenerationTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $productVariant = $event->getData();

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

            $productDefault = $productVariant->getProduct()->getProductDefaults()->first();
            $newVariant = $productVariant->getChannelPricings()->isEmpty();

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

            // Set entities and its product defaults
            $event->getForm()->add('taxCategory', TaxCategoryChoiceType::class, array_merge($taxCategoryOptions, $defaultTaxCategory));
            $event->getForm()->add('supplier', SupplierChoiceType::class, array_merge($supplierOptions, $defaultSupplier));
            $event->getForm()->add('tracked', CheckboxType::class, array_merge($trackedOptions, $defaultTracked));
            $event->getForm()->add('onHand', IntegerType::class, array_merge($onHandOptions, $defaultOnHand));

            // Channel pricing
            $event->getForm()->add('channelPricings', ChannelCollectionType::class, [
                'entry_type' => ChannelPricingType::class,
                'entry_options' => function (ChannelInterface $channel) use ($productVariant) {
                    return [
                        'channel' => $channel,
                        'product_variant' => $productVariant,
                    ];
                },
                'label' => 'sylius.form.variant.price',
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ProductVariantGenerationType::class;
    }
}
