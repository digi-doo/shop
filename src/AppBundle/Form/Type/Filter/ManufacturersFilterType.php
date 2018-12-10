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

use AppBundle\Context\ManufacturersContext;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ManufacturersFilterType extends AbstractFilterType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ManufacturersContext
     */
    private $manufacturersContext;

    /**
     * @param ManufacturersContext $manufacturersContext
     * @param RequestStack $requestStack
     */
    public function __construct(
        ManufacturersContext $manufacturersContext,
        RequestStack $requestStack
    ) {
        $this->manufacturersContext = $manufacturersContext;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Check if taxon has enabled filters && taxon has some related manufacturers
        $manufacturers = $this->manufacturersContext->getManufacturers();
        if ($manufacturers !== null) {
            $choices = $this->mapToChoices($manufacturers);
            if (empty($choices)) {
                return;
            }
            $expanded = false;
            $requestCriteria = $this->requestStack->getCurrentRequest()->query->get('criteria');
            if ($requestCriteria !== null && isset($requestCriteria['product']['manufacturers'])) {
                $expanded = true;
            }

            $builder->add('manufacturers', ChoiceType::class, [
                'label' => 'app.ui.manufacturers',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $choices,
                'attr' => [
                    'class' => ('collapse' . ($expanded ? ' show' : '')),
                ],
                'label_attr' => [
                    'data-toggle' => 'collapse',
                    'data-target' => '#criteria_product_manufacturers_manufacturers',
                    'aria-expanded' => $expanded ? 'true' : 'false',
                    'aria-controls' => 'criteria_product_manufacturers_manufacturers',
                ],
            ]);
        }
    }

    /**
     * Map manufacturers to array
     *
     * @param  array  $manufacturers
     *
     * @return array
     */
    private function mapToChoices(array $manufacturers): array
    {
        $choices = [];
        foreach ($manufacturers as $manufacturer) {
            // Check if manufacturer is enabled
            if (!$manufacturer->isEnabled()) {
                continue;
            }
            // Check if manufacturer is filterable
            if (!$manufacturer->isFilterable()) {
                continue;
            }
            $choices[$manufacturer->getName()] = $manufacturer->getCode();
        }

        return $choices;
    }
}
