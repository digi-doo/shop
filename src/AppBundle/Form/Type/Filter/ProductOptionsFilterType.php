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

use AppBundle\Context\ProductOptionsContext;
use AppBundle\Form\Type\Filter\ChoiceMapper\ProductOptionsMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductOptionsFilterType extends AbstractFilterType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ProductOptionsContext
     */
    private $productOptionsContext;

    /**
     * @var ProductOptionsMapper
     */
    private $productOptionsMapper;

    /**
     * @param RequestStack $requestStack
     * @param ProductOptionsContext $productOptionsContext
     * @param ProductOptionsMapper $productOptionsMapper
     */
    public function __construct(
        RequestStack $requestStack,
        ProductOptionsContext $productOptionsContext,
        ProductOptionsMapper $productOptionsMapper
    ) {
        $this->requestStack = $requestStack;
        $this->productOptionsContext = $productOptionsContext;
        $this->productOptionsMapper = $productOptionsMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $requestOptionCodes = [];
        $requestCriteria = $this->requestStack->getCurrentRequest()->query->get('criteria');
        if ($requestCriteria !== null && isset($requestCriteria['product']['options'])) {
            $requestOptionCodes = $requestCriteria['product']['options'];
        }

        // Check if taxon has enabled filters && taxon has some related manufacturers
        if ($this->productOptionsContext->getProductOptions() !== null) {
            foreach ($this->productOptionsContext->getProductOptions() as $productOption) {
                // check if product option is filterable
                if (!$productOption->isFilterable()) {
                    continue;
                }
                $choices = $this->productOptionsMapper->mapToChoices($productOption);

                if (empty($choices)) {
                    continue;
                }
                if (!empty($requestOptionCodes) && array_key_exists($productOption->getCode(), $requestOptionCodes)) {
                    $expanded = true;
                } else {
                    $expanded = false;
                }

                $builder->add($productOption->getCode(), ChoiceType::class, [
                    'label' => $productOption->getName(),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => $choices,
                    'attr' => [
                        'class' => ('collapse' . ($expanded ? ' show' : '')),
                    ],
                    'label_attr' => [
                        'data-toggle' => 'collapse',
                        'data-target' => '#criteria_product_options_' . $productOption->getCode(),
                        'aria-expanded' => $expanded ? 'true' : 'false',
                        'aria-controls' => 'criteria_product_options_' . $productOption->getCode(),
                    ],
                ]);
            }
        }
    }
}
