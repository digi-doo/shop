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

use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\ChannelFormSubscriber;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ChannelTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('stockEmails', TextType::class, [
                'label' => 'sylius.form.channel.stock_emails',
                'required' => false,
            ])
            ->add('orderEmails', TextType::class, [
                'label' => 'sylius.form.channel.order_emails',
                'required' => false,
            ])
            ->add('bankAccount', TextType::class, [
                'label' => 'sylius.form.channel.bank_account',
                'required' => false,
            ])
            ->add('supportNumber', TextType::class, [
                'label' => 'sylius.form.channel.support_number',
                'required' => false,
            ])
            ->add('supportEmail', TextType::class, [
                'label' => 'sylius.form.channel.support_email',
                'required' => false,
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'sylius.form.channel.meta_title',
                'required' => false,
            ])
            ->add('metaRobots', CheckboxType::class, [
                'label' => 'sylius.form.channel.meta_robots',
                'required' => false,
            ])
            ->add('metaAuthor', TextType::class, [
                'label' => 'sylius.form.channel.meta_author',
                'required' => false,
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'sylius.form.channel.meta_description',
                'required' => false,
            ])
            ->add('metaKeywords', TextType::class, [
                'label' => 'sylius.form.channel.meta_keywords',
                'required' => false,
            ])
            ->add('address', FroalaEditorType::class, [
                'label' => 'sylius.form.channel.address',
                'required' => false,
                'height' => 100,
                'pluginsEnabled' => ['link'],
            ])
            ->add('opening', FroalaEditorType::class, [
                'label' => 'sylius.form.channel.opening',
                'required' => false,
                'height' => 100,
                'pluginsEnabled' => ['link', 'table'],
            ])
            ->add('freeShippingFrom', MoneyType::class, [
                'label' => 'app.ui.free_shipping_from',
                'currency' => 'CZK',
                'required' => false,
            ])
            ->add('freeGiftFrom', MoneyType::class, [
                'label' => 'app.ui.free_gift_from',
                'currency' => 'CZK',
                'required' => false,
            ])
            ->add('freeGiftVariantCode', TextType::class, [
                'label' => 'app.ui.free_gift_variant_code',
                'required' => false,
            ])
            ->addEventSubscriber(new ChannelFormSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ChannelType::class;
    }
}
