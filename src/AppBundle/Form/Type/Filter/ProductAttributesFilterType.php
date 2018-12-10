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

use AppBundle\Context\ProductAttributesContext;
use AppBundle\Form\Type\Filter\ChoiceMapper\ProductAttributesMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductAttributesFilterType extends AbstractFilterType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ProductAttributesContext
     */
    private $productAttributesContext;

    /**
     * @var ProductAttributesMapper
     */
    private $productAttributesMapper;

    /**
     * @param RequestStack $requestStack
     * @param ProductAttributesContext $productAttributesContext
     * @param ProductAttributesMapper $productOptionsMapper
     */
    public function __construct(
        RequestStack $requestStack,
        ProductAttributesContext $productAttributesContext,
        ProductAttributesMapper $productAttributesMapper
    ) {
        $this->requestStack = $requestStack;
        $this->productAttributesContext = $productAttributesContext;
        $this->productAttributesMapper = $productAttributesMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $requestAttributeCodes = [];
        $requestCriteria = $this->requestStack->getCurrentRequest()->query->get('criteria');
        if ($requestCriteria !== null && isset($requestCriteria['product']['attributes'])) {
            $requestAttributeCodes = $requestCriteria['product']['attributes'];
        }

        // Check if taxon has enabled filters && taxon has some related attributes
        if ($this->productAttributesContext->getProductAttributes() !== null) {
            foreach ($this->productAttributesContext->getProductAttributes() as $productAttribute) {
                // Check if attribute is filterable
                if (!$productAttribute->isFilterable()) {
                    continue;
                }
                $choices = $this->productAttributesMapper->mapToChoices($productAttribute);

                if (empty($choices)) {
                    continue;
                }
                if (!empty($requestAttributeCodes) && array_key_exists($productAttribute->getCode() . '___' . $productAttribute->getType(), $requestAttributeCodes)) {
                    $expanded = true;
                } else {
                    $expanded = false;
                }

                $builder->add($productAttribute->getCode() . '___' . $productAttribute->getType(), ChoiceType::class, [
                    'label' => $productAttribute->getName(),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => $choices,
                    'attr' => [
                        'class' => ('collapse' . ($expanded ? ' show' : '')),
                    ],
                    'label_attr' => [
                        'data-toggle' => 'collapse',
                        'data-target' => '#criteria_product_attributes_' . $productAttribute->getCode() . '___' . $productAttribute->getType(),
                        'aria-expanded' => $expanded ? 'true' : 'false',
                        'aria-controls' => 'criteria_product_attributes_' . $productAttribute->getCode() . '___' . $productAttribute->getType(),
                    ],
                ]);
            }
        }
    }
}
