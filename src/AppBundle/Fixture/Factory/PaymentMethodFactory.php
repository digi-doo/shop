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

namespace AppBundle\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class PaymentMethodFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    public const DEFAULT_LOCALE = 'cs_CZ';

    /**
     * @var PaymentMethodFactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var RepositoryInterface
     */
    private $taxCategoryRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param PaymentMethodFactoryInterface $paymentMethodFactory
     * @param RepositoryInterface $localeRepository
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        PaymentMethodFactoryInterface $paymentMethodFactory,
        RepositoryInterface $localeRepository,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $taxCategoryRepository
    ) {
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->localeRepository = $localeRepository;
        $this->channelRepository = $channelRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): PaymentMethodInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodFactory->createWithGateway($options['gatewayFactory']);
        $paymentMethod->getGatewayConfig()->setGatewayName($options['gatewayName']);
        $paymentMethod->getGatewayConfig()->setConfig($options['gatewayConfig']);

        $paymentMethod->setCode($options['code']);
        $paymentMethod->setEnabled($options['enabled']);
        $paymentMethod->setPrice($options['price']);
        $paymentMethod->setExternalCode($options['external_code']);
        $paymentMethod->setTaxCategory($options['tax_category']);

        foreach ($this->getLocales() as $localeCode) {
            $paymentMethod->setCurrentLocale($localeCode);
            $paymentMethod->setFallbackLocale($localeCode);

            $paymentMethod->setName($options['name']);
            $paymentMethod->setDescription($options['description']);
        }

        foreach ($options['channels'] as $channel) {
            $paymentMethod->addChannel($channel);
        }

        return $paymentMethod;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (Options $options): string {
                return $this->faker->words(3, true);
            })
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('description', function (Options $options): string {
                return $this->faker->sentence();
            })
            ->setDefault('gatewayName', 'Offline')
            ->setDefault('gatewayFactory', 'offline')
            ->setDefault('gatewayConfig', [])
            ->setDefault('enabled', true)
            ->setDefault('price', 124)
            ->setDefault('external_code', 'transfer')
            ->setDefault('tax_category', LazyOption::all($this->taxCategoryRepository))
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
            ->setAllowedTypes('enabled', 'bool')
            ->setAllowedTypes('external_code', 'string')
            ->setNormalizer('tax_category', LazyOption::findOneBy($this->taxCategoryRepository, 'code'))
        ;
    }

    /**
     * @return iterable
     */
    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        if (empty($locales)) {
            yield self::DEFAULT_LOCALE;
        }

        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
