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

use Sylius\Bundle\CoreBundle\Form\Type\User\AdminUserType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class AdminUserTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('enabled');

        $builder->add('enabled', CheckboxType::class, [
            'label' => 'sylius.form.user.enabled',
            'attr' => ['checked' => 'checked'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return AdminUserType::class;
    }
}
