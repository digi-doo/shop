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

use AppBundle\Entity\Product;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ProductDefaultType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('massPriceEnabled', CheckboxType::class, [
            'required' => false,
            'label' => 'app.ui.enabled_mass_price',
            'data' => false,
        ]);
        $builder->add('price', MoneyType::class, [
            'label' => 'sylius.ui.price',
            'currency' => $options['channel']->getBaseCurrency()->getCode(),
        ]);

        $builder->add('massDiscountEnabled', CheckboxType::class, [
            'required' => false,
            'label' => 'app.ui.enabled_mass_discount',
            'data' => false,
        ]);
        $builder->add('discount', PercentType::class, [
            'label' => 'sylius.ui.percental_discount',
        ]);

        $builder->add('massOriginalPriceEnabled', CheckboxType::class, [
            'required' => false,
            'label' => 'app.ui.enabled_mass_original_price',
            'data' => false,
        ]);
        $builder->add('originalPrice', MoneyType::class, [
            'label' => 'app.ui.original_price',
            'currency' => $options['channel']->getBaseCurrency()->getCode(),
        ]);

        $builder->add('massSupplierEnabled', CheckboxType::class, [
            'required' => false,
            'label' => 'app.ui.enabled_mass_supplier',
            'data' => false,
        ]);
        $builder->add('supplier', SupplierChoiceType::class, [
            'required' => false,
            'label' => 'app.ui.supplier',
        ]);

        $builder->add('massTaxCategoryEnabled', CheckboxType::class, [
            'required' => false,
            'label' => 'app.ui.enabled_mass_tax_category',
            'data' => false,
        ]);
        $builder->add('taxCategory', TaxCategoryChoiceType::class, [
            'required' => true,
            'label' => 'sylius.form.product_variant.tax_category',
        ]);

        $builder->add('tracked', CheckboxType::class, [
            'label' => 'sylius.form.variant.tracked',
        ]);
        $builder->add('massTrackedEnabled', CheckboxType::class, [
            'required' => false,
            'label' => 'app.ui.enabled_mass_tracked',
            'data' => false,
        ]);

        $builder->add('onHand', IntegerType::class, [
            'label' => 'sylius.form.variant.on_hand',
        ]);
        $builder->add('massOnHandEnabled', CheckboxType::class, [
            'required' => false,
            'label' => 'app.ui.enabled_mass_on_hand',
            'data' => false,
        ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($options): void {
            $productChannelPricing = $event->getData();

            if (!$productChannelPricing instanceof $this->dataClass) {
                $event->setData(null);

                return;
            }

            $productChannelPricing->setChannelCode($options['channel']->getCode());
            $productChannelPricing->setProduct($options['product']);

            $event->setData($productChannelPricing);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('channel')
            ->setAllowedTypes('channel', [ChannelInterface::class])

            ->setDefined('product')
            ->setAllowedTypes('product', ['null', Product::class])

            ->setDefaults([
                'label' => false,
                // 'label' => function (Options $options): string {
                //     return $options['channel']->getName();
                // },
                // 'label_attr' => ['style' => 'border-bottom:1px solid rgba(34,36,38,0.15);font-size:1.28571429rem;margin-bottom:10px;']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_product_default';
    }
}
