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

use AppBundle\Entity\Product;
use AppBundle\Event\Subscriber\ProductOptionFieldSubscriber;
use AppBundle\Form\Type\GoogleAutocompleteChoiceType;
use AppBundle\Form\Type\HeurekaAutocompleteChoiceType;
use AppBundle\Form\Type\ManufacturerChoiceType;
use AppBundle\Form\Type\ProductDefaultType;
use AppBundle\Form\Type\TagAutocompleteChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ProductTypeExtension extends AbstractTypeExtension
{
    /**
     * @var ProductVariantResolverInterface
     */
    private $variantResolver;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ProductVariantResolverInterface $variantResolver
     * @param ContainerInterface $container
     */
    public function __construct(ProductVariantResolverInterface $variantResolver, ContainerInterface $container)
    {
        $this->variantResolver = $variantResolver;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $heurekaEnabled = $this->container->getParameter('app.feed.heureka.enabled');
        $googleEnabled = $this->container->getParameter('app.feed.google.enabled');
        $facebookEnabled = $this->container->getParameter('app.feed.facebook.enabled');

        $builder->add('manufacturer', ManufacturerChoiceType::class, [
            'required' => false,
            'label' => 'app.ui.manufacturer',
        ]);

        $builder->add('tags', TagAutocompleteChoiceType::class, [
            'label' => false,
            'multiple' => true,
        ]);

        if ($heurekaEnabled) {
            $builder->add('heurekaTaxonomy', HeurekaAutocompleteChoiceType::class, [
                'label' => false,
                'multiple' => false,
            ]);
        }

        if ($googleEnabled && $facebookEnabled) {
            $builder->add('googleTaxonomy', GoogleAutocompleteChoiceType::class, [
                'label' => false,
                'multiple' => false,
            ]);
            $builder->add('facebookTaxonomy', TextType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
            ]);
        }

        if (!$googleEnabled && $facebookEnabled) {
            $builder->add('googleDisabled', TextType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
            ]);
            $builder->add('googleTaxonomy', GoogleAutocompleteChoiceType::class, [
                'label' => false,
                'multiple' => false,
            ]);
        }

        if ($googleEnabled && !$facebookEnabled) {
            $builder->add('googleEnabled', TextType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
            ]);
            $builder->add('googleTaxonomy', GoogleAutocompleteChoiceType::class, [
                'label' => false,
                'multiple' => false,
            ]);
        }

        $builder->remove('variantSelectionMethod');
        $builder->add('variantSelectionMethod', ChoiceType::class, [
            'choices' => array_flip(Product::getVariantSelectionMethodLabels()),
            'label' => 'sylius.form.product.variant_selection_method',
        ]);

        $builder->addEventSubscriber(new ProductOptionFieldSubscriber($this->variantResolver));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $product = $event->getData();

            $event->getForm()->add('productDefaults', ChannelCollectionType::class, [
                'entry_type' => ProductDefaultType::class,
                'entry_options' => function (ChannelInterface $channel) use ($product) {
                    return [
                        'channel' => $channel,
                        'product' => $product,
                        'required' => false,
                    ];
                },
                'label' => false,
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ProductType::class;
    }
}
