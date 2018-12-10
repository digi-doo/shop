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
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Repository\TaxCategoryRepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ShippingMethodFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $shippingMethodFactory;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var RepositoryInterface
     */
    private $shippingCategoryRepository;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var RepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var TaxCategoryRepositoryInterface
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
     * @param FactoryInterface $shippingMethodFactory
     * @param RepositoryInterface $zoneRepository
     * @param RepositoryInterface $shippingCategoryRepository
     * @param RepositoryInterface $localeRepository
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        FactoryInterface $shippingMethodFactory,
        RepositoryInterface $zoneRepository,
        RepositoryInterface $shippingCategoryRepository,
        RepositoryInterface $localeRepository,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $paymentMethodRepository,
        RepositoryInterface $taxCategoryRepository
    ) {
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->zoneRepository = $zoneRepository;
        $this->shippingCategoryRepository = $shippingCategoryRepository;
        $this->localeRepository = $localeRepository;
        $this->channelRepository = $channelRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): ShippingMethodInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setCode($options['code']);
        $shippingMethod->setEnabled($options['enabled']);
        $shippingMethod->setZone($options['zone']);
        $shippingMethod->setCalculator($options['calculator']['type']);
        $shippingMethod->setConfiguration($options['calculator']['configuration']);
        $shippingMethod->setArchivedAt($options['archived_at']);
        $shippingMethod->setTaxCategory($options['tax_category']);

        if (array_key_exists('shipping_category', $options)) {
            $shippingMethod->setCategory($options['shipping_category']);
        }

        foreach ($this->getLocales() as $localeCode) {
            $shippingMethod->setCurrentLocale($localeCode);
            $shippingMethod->setFallbackLocale($localeCode);

            $shippingMethod->setName($options['name']);
            $shippingMethod->setDescription($options['description']);
        }

        foreach ($options['channels'] as $channel) {
            $shippingMethod->addChannel($channel);
        }

        foreach ($options['payment_methods'] as $paymentMethod) {
            $shippingMethod->addPaymentMethod($paymentMethod);
        }

        return $shippingMethod;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('name', function (Options $options): string {
                return $this->faker->words(3, true);
            })
            ->setDefault('description', function (Options $options): string {
                return $this->faker->sentence();
            })
            ->setDefault('enabled', function (Options $options): bool {
                return $this->faker->boolean(90);
            })
            ->setAllowedTypes('enabled', 'bool')

            ->setDefault('zone', LazyOption::randomOne($this->zoneRepository))
            ->setAllowedTypes('zone', ['null', 'string', ZoneInterface::class])
            ->setNormalizer('zone', LazyOption::findOneBy($this->zoneRepository, 'code'))

            ->setDefined('shipping_category')
            ->setAllowedTypes('shipping_category', ['null', 'string', ShippingCategoryInterface::class])
            ->setNormalizer('shipping_category', LazyOption::findOneBy($this->shippingCategoryRepository, 'code'))

            ->setDefault('tax_category', LazyOption::all($this->taxCategoryRepository))
            ->setNormalizer('tax_category', LazyOption::findOneBy($this->taxCategoryRepository, 'code'))

            ->setDefault('calculator', function (Options $options): array {
                $configuration = [];
                /** @var ChannelInterface $channel */
                foreach ($options['channels'] as $channel) {
                    $configuration[$channel->getCode()] = ['amount' => $this->faker->randomNumber(4)];
                }

                return [
                    'type' => DefaultCalculators::FLAT_RATE,
                    'configuration' => $configuration,
                ];
            })
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))

            ->setDefault('payment_methods', LazyOption::all($this->paymentMethodRepository))
            ->setAllowedTypes('payment_methods', 'array')
            ->setNormalizer('payment_methods', LazyOption::findBy($this->paymentMethodRepository, 'code'))

            ->setDefault('archived_at', null)
            ->setAllowedTypes('archived_at', ['null', \DateTimeInterface::class])
        ;
    }

    /**
     * @return iterable
     */
    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
