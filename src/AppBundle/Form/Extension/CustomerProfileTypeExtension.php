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

use Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerProfileTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('birthday')
            ->remove('gender');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return CustomerProfileType::class;
    }
}
