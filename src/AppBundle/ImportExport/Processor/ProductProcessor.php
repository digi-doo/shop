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

namespace AppBundle\ImportExport\Processor;

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Main product processor
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ProductProcessor
{
    /** @var ContainerInterface */
    private $container;

    /** @var int */
    private $loop;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        /** @var ContainerInterface */
        $this->container = $container;
    }

    public function process(array $data, int $loop): void
    {
        if ($data['Product-code'] === null) {
            return;
        }
        $this->loop = $loop;
        $productCode = $data['Product-code'];
        $variantCode = $data['Variant-code'];
        $price = $data['Price'];

        if ($productCode !== null && $variantCode === null && $price !== null) {
            // Simple product
            $this->resolveProduct($data, 'simple');
        } elseif ($productCode !== null && $variantCode === null && $price === null) {
            // Configurable product
            $this->resolveProduct($data, 'configurable');
        } elseif ($productCode !== null && $variantCode !== null && $price !== null) {
            // Variant
            $this->resolveVariant($data, 'variant');
        }
    }

    /**
     * Resolve general product
     *
     * @param array $data
     * @param string $type
     */
    private function resolveProduct(array $data, string $type = ''): void
    {
        $productManager = $this->container->get('sylius.manager.product');

        /** @var ResourceInterface $product */
        $product = $this->getProduct((string) $data['Product-code']);

        $product->setEnabled(true);
        $product->setVariantSelectionMethod('match');
        $product->setName($data['Name']);
        $product->setSlug($data['Slug']);
        $product->setMetaKeywords($data['Meta-keywords']);
        $product->setMetaDescription($data['Meta-description']);
        $product->setShortDescription($data['Short-description']);
        $product->setDescription($data['Description']);
        $product->setManufacturer($this->getManufacturer((string) $data['Manufacturer-code']));
        $product->setHeurekaTaxonomy($this->getHeurekaTaxonomy((string) (int) $data['Heureka-id']));
        $product->addChannel($this->getChannel('default'));

        if ($type === 'simple') {
            $variant = $this->resolveVariant($data, $type);
            $product->addVariant($variant);
        }

        if ($type === 'configurable') {
            foreach ($this->getProductOptions($data['Option-codes']) as $option) {
                $product->addOption($option);
            }
        }

        $productManager->persist($product);
    }

    /**
     * Resolve general variant
     *
     * @param array $data
     * @param string $type
     *
     * @return ResourceInterface
     *
     * @throws \Exception
     */
    private function resolveVariant(array $data, string $type = ''): ResourceInterface
    {
        if ($type === 'variant') {
            $variant = $this->getVariant((string) $data['Variant-code']);
        } else {
            $variant = $this->getVariant((string) $data['Product-code']);
        }

        $variant->setName(null);
        $variant->setTracked(true);
        // $variant->setOnHand((int) filter_var($data['Stock-count'], FILTER_SANITIZE_NUMBER_INT));
        $variant->setOnHand(1);
        $variant->setExternalCode(((string) $data['Import-id']) === '' ? null : (string) $data['Import-id']);
        $variant->setSupplier($this->getSupplier((string) $data['Supplier-code']));
        $variant->setTaxCategory($this->getTaxCategory('default'));
        $variant->addChannelPricing($this->resolveChannelPricingForChannel($variant, $data));

        if ($type === 'variant') {
            $productManager = $this->container->get('sylius.manager.product');

            if (!$data['Option-values-codes']) {
                throw new \Exception('Missing Product Option Values codes on row ' . $this->loop . ' !');
            }

            foreach ($this->getVariantOptionValues($data['Option-values-codes']) as $optionValue) {
                $variant->addOptionValue($optionValue);
            }

            $product = $this->getProduct((string) $data['Product-code']);
            $product->addVariant($variant);

            $productManager->persist($product);
        }

        return $variant;
    }

    /**
     * Get or create product entity
     *
     * @param string $productCode
     *
     * @return ResourceInterface
     */
    private function getProduct(?string $productCode): ResourceInterface
    {
        $productRepository = $this->container->get('sylius.repository.product');
        $productFactory = $this->container->get('sylius.factory.product');

        /** @var ResourceInterface $product */
        $product = $productRepository->findOneBy(['code' => $productCode]);

        if (null === $product) {
            $product = $productFactory->createNew();
            $product->setCode($productCode);
        }

        return $product;
    }

    /**
     * Get or create variant entity
     *
     * @param string $variantCode
     *
     * @return ResourceInterface
     */
    private function getVariant(string $variantCode): ResourceInterface
    {
        $variantRepository = $this->container->get('sylius.repository.product_variant');
        $variantFactory = $this->container->get('sylius.factory.product_variant');

        /** @var ResourceInterface $product */
        $variant = $variantRepository->findOneBy(['code' => $variantCode]);

        if (null === $variant) {
            $variant = $variantFactory->createNew();
            $variant->setCode($variantCode);
        }

        return $variant;
    }

    /**
     * Get supplier entity
     *
     * @param string $supplierCode
     *
     * @return ResourceInterface
     */
    private function getSupplier(?string $supplierCode): ?ResourceInterface
    {
        $supplierRepository = $this->container->get('app.repository.supplier');

        /** @var ResourceInterface $supplier */
        $supplier = $supplierRepository->findOneBy(['code' => $supplierCode]);

        return $supplier;
    }

    /**
     * Get manufacturer entity
     *
     * @param string $manufacturerCode
     *
     * @return ResourceInterface
     */
    private function getManufacturer(?string $manufacturerCode): ?ResourceInterface
    {
        $manufacturerRepository = $this->container->get('app.repository.manufacturer');

        /** @var ResourceInterface $manufacturer */
        $manufacturer = $manufacturerRepository->findOneBy(['code' => $manufacturerCode]);

        return $manufacturer;
    }

    /**
     * Get tax category entity
     *
     * @param string $taxCategoryCode
     *
     * @return ResourceInterface
     */
    private function getTaxCategory(?string $taxCategoryCode): ?ResourceInterface
    {
        $taxCategoryCode = 'default';
        $taxCategoryRepository = $this->container->get('sylius.repository.tax_category');

        /** @var ResourceInterface $taxCategory */
        $taxCategory = $taxCategoryRepository->findOneBy(['code' => $taxCategoryCode]);

        return $taxCategory;
    }

    /**
     * Get channel entity
     *
     * @param string $channelCode
     *
     * @return ResourceInterface
     */
    private function getChannel(?string $channelCode): ResourceInterface
    {
        $channelCode = 'default';
        $channelRepository = $this->container->get('sylius.repository.channel');

        /** @var ResourceInterface $channel */
        $channel = $channelRepository->findOneBy(['code' => $channelCode]);

        return $channel;
    }

    /**
     * Get channel pricing for product variant
     *
     * @param string $channelPricing
     *
     * @return ResourceInterface
     */
    private function resolveChannelPricingForChannel(ResourceInterface $productVariant, array $data): ResourceInterface
    {
        $channelPricingRepository = $this->container->get('sylius.repository.channel_pricing');
        $channelPricingFactory = $this->container->get('sylius.factory.channel_pricing');

        /** @var ResourceInterface $channelPricing */
        $channelPricing = $channelPricingRepository->findOneBy(['productVariant' => $productVariant]);

        if (null === $channelPricing) {
            $channelPricing = $channelPricingFactory->createNew();
            $channelPricing->setPrice((int) ($data['Price'] * 100));
            $channelPricing->setOriginalPrice(($data['Original-price'] === null ? 0 : (int) ($data['Original-price']) * 100));
            $channelPricing->setChannelCode('default');
            $channelPricing->setProductVariant($productVariant);
        } else {
            $channelPricing->setPrice((int) ($data['Price'] * 100));
            $channelPricing->setOriginalPrice(($data['Original-price'] === null ? 0 : (int) ($data['Original-price']) * 100));
        }

        return $channelPricing;
    }

    /**
     * Get heureka taxonomy entity
     *
     * @param string $heurekaCode
     *
     * @return ResourceInterface
     */
    private function getHeurekaTaxonomy(?string $heurekaCode): ?ResourceInterface
    {
        $heurekaRepository = $this->container->get('app.repository.heureka');

        /** @var ResourceInterface $heureka */
        $heureka = $heurekaRepository->findOneBy(['code' => $heurekaCode]);

        return $heureka;
    }

    /**
     * Get product options
     *
     * @param string $optionCodes
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getProductOptions(string $optionCodes): array
    {
        $optionRepository = $this->container->get('sylius.repository.product_option');
        $options = explode(',', str_replace(' ', '', $optionCodes));
        $productOptions = [];

        foreach ($options as $optionCode) {
            $productOption = $optionRepository->findOneBy(['code' => $optionCode]);
            if ($productOption) {
                $productOptions[] = $productOption;
            } else {
                throw new \Exception('Product Option with code ' . $optionCode . ' on row ' . $this->loop . ' does not exist!');
            }
        }

        return $productOptions;
    }

    /**
     * Get product options values
     *
     * @param string $optionValuesCodes
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getVariantOptionValues(string $optionValuesCodes): array
    {
        $optionValueRepository = $this->container->get('sylius.repository.product_option_value');
        $options = explode(',', str_replace(' ', '', $optionValuesCodes));

        $productOptionValues = [];
        foreach ($options as $optionValueCode) {
            $codes = explode(':', str_replace(' ', '', $optionValueCode));

            if (!array_key_exists(0, $codes) || !array_key_exists(1, $codes)) {
                throw new \Exception('Possible typo in product option value code: ' . $optionValueCode . ' on row ' . $this->loop . ' !');
            }

            $productOptionValue = $optionValueRepository->findOneBy(['code' => $codes[1]]);
            if ($productOptionValue) {
                $productOptionValues[] = $productOptionValue;
            } else {
                throw new \Exception('Product Option Value with code ' . $codes[1] . ' on row ' . $this->loop . ' does not exist!');
            }
        }

        return $productOptionValues;
    }

    /**
     * Set product categories
     *
     * @param array $data
     *
     * @return array
     */
    private function getProductCategories(array $data): array
    {
        $productCategoryFactory = $this->container->get('sylius.factory.product_taxon');
        $productCategoryRepository = $this->container->get('sylius.repository.product_taxon');
        $productCategoryManager = $this->container->get('sylius.manager.product_taxon');
        $productRepository = $this->container->get('sylius.repository.product');
        $categoryRepository = $this->container->get('sylius.repository.taxon');

        $cats = explode(',', str_replace(' ', '', $data['Category-codes']));

        $productCats = [];
        foreach ($cats as $catCode) {
            if ($catCode !== '') {
                $cat = $categoryRepository->findOneBy(['code' => $catCode]);
                $prod = $productRepository->findOneBy(['code' => $data['Product-code']]);
                $productCategory = $productCategoryRepository->findOneBy(['taxon' => $cat]);

                if (null === $productCategory) {
                    $productCategory = $productCategoryFactory->createNew();
                    $productCategory->setTaxon($cat);
                    $productCategory->setProduct($prod);

                    $productCategoryManager->persist($productCategory);
                }

                $productCats[] = $productCategory;
            }
        }

        return $productCats;
    }

    /**
     * Get product tags
     *
     * @param array $data
     *
     * @return array
     */
    private function getProductTags(array $data): array
    {
        $tagRepository = $this->container->get('app.repository.tag');

        $tags = explode(',', str_replace(' ', '', $data['Tag-codes']));

        $productTags = [];
        foreach ($tags as $tagCode) {
            if ($tagCode !== '') {
                $tag = $tagRepository->findOneBy(['code' => $tagCode]);
                $productTags[] = $tag;
            }
        }

        return $productTags;
    }
}
