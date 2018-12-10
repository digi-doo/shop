<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Sylius\Bundle\CoreBundle\Application\Kernel;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AppKernel extends Kernel
{   
    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        $bundles = [
            new \Sylius\Bundle\AdminBundle\SyliusAdminBundle(),
            new \Sylius\Bundle\ShopBundle\SyliusShopBundle(),

            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(), // Required by SyliusAdminApiBundle.
            new \Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle(),

            new Sentry\SentryBundle\SentryBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),

            new KMS\FroalaEditorBundle\KMSFroalaEditorBundle(),
            
            new \BitBag\SyliusCmsPlugin\BitBagSyliusCmsPlugin(),
            new \KunicMarko\ColorPickerBundle\ColorPickerBundle(),

            new Xynnn\GoogleTagManagerBundle\GoogleTagManagerBundle(),
            new GtmPlugin\GtmPlugin(),
            new GtmEnhancedEcommercePlugin\GtmEnhancedEcommercePlugin(),

            new FriendsOfSylius\SyliusImportExportPlugin\FOSSyliusImportExportPlugin(),

            // new \BitBag\ShippingExportPlugin\ShippingExportPlugin(),
            // new \Czende\BalikonosShippingExportPlugin\BalikonosShippingExportPlugin(),

            new \Czende\EcomailPlugin\EcomailPlugin(),
            new \Czende\GoPayPlugin\GoPayPlugin(),

            new \Webburza\Sylius\WishlistBundle\WebburzaSyliusWishlistBundle(),

            new SitemapPlugin\SitemapPlugin(),

            new \AppBundle\AppBundle(),
            new \Czende\DeveloShopPlugin\DeveloShopPlugin(), // Register develo plugin AFTER AppBundle to override some services

            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
            new Phobetor\RabbitMqSupervisorBundle\RabbitMqSupervisorBundle(),

            new EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
        ];

        // Only load these bundles when on dev, test and staging environment
        if (in_array($this->getEnvironment(), array('dev', 'test', 'staging'))) {
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return array_merge(parent::registerBundles(), $bundles);
    }
}
