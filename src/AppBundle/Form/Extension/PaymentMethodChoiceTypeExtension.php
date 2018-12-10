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

use Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentMethodRepository;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class PaymentMethodChoiceTypeExtension extends AbstractTypeExtension
{
    /**
     * @var PaymentMethodRepository
     */
    private $paymentMethodRepository;

    /**
     * @param PaymentMethodRepository $paymentMethodRepository
     */
    public function __construct(PaymentMethodRepository $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    if (isset($options['subject'])) {
                        return $options['subject']->getOrder()->getShipments()->first()->getMethod()->getPaymentMethods();
                    }

                    return $this->paymentMethodRepository->findAll();
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
            ])
            ->setDefined([
                'subject',
            ])
            ->setAllowedTypes('subject', PaymentInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return PaymentMethodChoiceType::class;
    }
}
