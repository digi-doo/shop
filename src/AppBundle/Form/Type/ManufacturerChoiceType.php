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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ManufacturerChoiceType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $manufacturerRepository;

    /**
     * @param \Doctrine\ORM\EntityRepository $manufacturerRepository
     */
    public function __construct(\Doctrine\ORM\EntityRepository $manufacturerRepository)
    {
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => function (Options $options) {
                return $this->manufacturerRepository->findBy(['enabled' => true]);
            },
            'choice_value' => 'id',
            'choice_label' => 'name',
            'placeholder' => '----------',
            'choice_translation_domain' => false,
            'label' => 'sylius.ui.manufacturer',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_manufacturer_choice';
    }
}
