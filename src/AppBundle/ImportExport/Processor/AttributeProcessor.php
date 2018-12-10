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

use FriendsOfSylius\SyliusImportExportPlugin\Processor\ResourceProcessorInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class AttributeProcessor implements ResourceProcessorInterface
{
    /** @var ContainerInterface */
    private $container;

    private $attributeRepository;
    private $attributeFactory;
    private $attributeManager;

    /**
     * AttributeProcessor constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->attributeRepository = $this->container->get('sylius.repository.product_attribute');
        $this->attributeFactory = $this->container->get('sylius.factory.product_attribute');
        $this->attributeManager = $this->container->get('sylius.manager.product_attribute');
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $data): void
    {
        $attribute = $this->getAttribute($data['Code']);

        $type = strtolower($data['Type']);
        if ($type === 'number') {
            $type = 'integer';
        }

        $attribute->setCode((string) $data['Code']);
        $attribute->setName($data['Name']);
        $attribute->setType($type);
        $attribute->setStorageType($type);
        $attribute->setConfiguration($this->getAttributeConfiguration($type));

        $this->attributeManager->persist($attribute);
    }

    /**
     * @param string $code
     *
     * @return ResourceInterface
     */
    private function getAttribute(string $code): ResourceInterface
    {
        /** @var ResourceInterface $attribute */
        $attribute = $this->attributeRepository->findOneBy(['code' => $code]);

        if (null === $attribute) {
            $attribute = $this->attributeFactory->createNew();
        }

        return $attribute;
    }

    /**
     * @param string $code
     *
     * @return ResourceInterface
     */
    private function getAttributeConfiguration(?string $type): array
    {
        $config = [];

        if ($type === 'text') {
            $config['min'] = null;
            $config['max'] = null;
        }

        return $config;
    }
}
