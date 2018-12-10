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

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class CompleteTypeExtenstion extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('terms', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'sylius.checkout.terms.not_null',
                        'groups' => 'sylius_checkout_complete',
                    ]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CompleteType::class;
    }
}
