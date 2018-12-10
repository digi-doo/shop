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
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ProductAttributeProcessor
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
        $this->container = $container;
    }

    public function process(array $data, int $loop): void
    {
        if ($data['Product-code'] === null) {
            return;
        }
        if ($data['Attributes'] === null) {
            return;
        }
        $this->loop = $loop;
        $productCode = (string) $data['Product-code'];
        $attributesRow = (string) $data['Attributes'];

        $this->checkProduct($productCode, $loop);
        $this->checkAttribute($attributesRow, $loop);

        $productRepository = $this->container->get('sylius.repository.product');
        $productAttributeFactory = $this->container->get('sylius.factory.product_attribute_value');
        $productAttributeManager = $this->container->get('sylius.manager.product_attribute_value');

        // Clear attributes
        $product = $this->getProduct($productCode, $loop);
        if ($product && !$product->getAttributes()->isEmpty()) {
            foreach ($product->getAttributes() as $attribute) {
                $product->removeAttribute($attribute);
            }
        }

        foreach ($this->getAttributes($attributesRow) as $attrRow) {
            $productAttribute = $productAttributeFactory->createNew();

            $productAttribute->setProduct($product);
            $productAttribute->setAttribute($attrRow['attribute']);
            $productAttribute->setValue($attrRow['value']);
            $productAttribute->setLocaleCode($attrRow['attribute']->getTranslation()->getLocale());

            $productAttributeManager->persist($productAttribute);
        }
    }

    /**
     * Get attributes array
     *
     * @param string $code
     *
     * @return array
     */
    private function getAttributes(?string $attributesRow): array
    {
        $attributeRepo = $this->container->get('sylius.repository.product_attribute');
        $attributes = [];

        $attributeRow = explode(';', $attributesRow);
        foreach ($attributeRow as $i => $attribute) {
            $attrs = explode(':', $attribute);

            if (!array_key_exists(0, $attrs) || !array_key_exists(1, $attrs)) {
                throw new \Exception('Possible typo in product attribute: ' . $attribute . ' on row ' . $this->loop . ' !');
            }

            $attributeCode = $attrs[0];
            $trimmedCode = str_replace(' ', '', $attributeCode);

            $attrEntity = $attributeRepo->findOneBy(['code' => $trimmedCode]);

            $value = (string) $attrs[1];
            if ($attrEntity->getType() === 'float' || $attrEntity->getType() === 'Float') {
                $value = (float) $attrs[1];
            }

            if ($attrEntity->getType() === 'text' || $attrEntity->getType() === 'Text') {
                $value = (string) $attrs[1];
            }

            if ($attrEntity->getType() === 'integer' || $attrEntity->getType() === 'Number') {
                $value = (int) $attrs[1];
            }

            $attributes[$i]['attribute'] = $attrEntity;
            $attributes[$i]['value'] = $value;
        }

        return $attributes;
    }

    /**
     * Get product entity
     *
     * @param string $productCode
     *
     * @return ResourceInterface
     */
    private function getProduct(?string $productCode, int $loop): ?ResourceInterface
    {
        $productRepo = $this->container->get('sylius.repository.product');

        /** @var ResourceInterface $product */
        $product = $productRepo->findOneBy(['code' => $productCode]);

        if (!$product) {
            throw new \Exception('Product with code ' . $productCode . ' on row ' . $loop . ' was not found in database');
        }

        return $product;
    }

    /**
     * Check if product exists product entity
     *
     * @param string $productCode
     *
     * @throws \Exception
     */
    private function checkProduct(?string $productCode, int $loop): void
    {
        $productRepo = $this->container->get('sylius.repository.product');

        $product = $productRepo->findOneBy(['code' => $productCode]);
        if (!$product) {
            throw new \Exception('Product with code ' . $productCode . ' on row ' . $loop . ' was not found in database');
        }
    }

    /**
     * Check attributes entities
     *
     * @param string $attributes
     *
     * @throws \Exception
     */
    private function checkAttribute(?string $attributes, int $loop): void
    {
        $attributeRepo = $this->container->get('sylius.repository.product_attribute');

        $attributeRow = explode(';', $attributes);
        foreach ($attributeRow as $attribute) {
            $attrs = explode(':', $attribute);

            $attributeCode = $attrs[0];
            $trimmedCode = str_replace(' ', '', $attributeCode);

            $attrEntity = $attributeRepo->findOneBy(['code' => $trimmedCode]);
            if (!$attrEntity) {
                throw new \Exception('Attribute with code ' . $trimmedCode . ' on row ' . $loop . ' was not found in database');
            }
        }
    }
}
