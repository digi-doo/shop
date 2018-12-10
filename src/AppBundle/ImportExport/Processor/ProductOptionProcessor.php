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
class ProductOptionProcessor implements ResourceProcessorInterface
{
    /** @var ContainerInterface */
    private $container;

    private $optionRepository;
    private $optionFactory;
    private $optionManager;

    private $optionValueRepository;
    private $optionValueFactory;
    private $optionValueManager;

    /**
     * ProductOptionProcessor constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->optionRepository = $this->container->get('sylius.repository.product_option');
        $this->optionFactory = $this->container->get('sylius.factory.product_option');
        $this->optionManager = $this->container->get('sylius.manager.product_option');

        $this->optionValueRepository = $this->container->get('sylius.repository.product_option_value');
        $this->optionValueFactory = $this->container->get('sylius.factory.product_option_value');
        $this->optionValueManager = $this->container->get('sylius.manager.product_option_value');
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $data): void
    {
        $option = $this->getOption($data['Code']);
        $option->setCode((string) $data['Code']);
        $option->setName($data['Name']);
        $this->optionManager->persist($option);

        $optionValue = $this->getOptionValue((string) $data['Value']['Code']);
        $optionValue->setOption($option);
        $optionValue->setCode((string) $data['Value']['Code']);
        $optionValue->setValue((string) $data['Value']['Value']);
        $this->optionValueManager->persist($optionValue);
    }

    /**
     * @param string $code
     *
     * @return ResourceInterface
     */
    private function getOption(string $code): ResourceInterface
    {
        /** @var ResourceInterface $option */
        $option = $this->optionRepository->findOneBy(['code' => $code]);

        if (null === $option) {
            $option = $this->optionFactory->createNew();
        }

        return $option;
    }

    /**
     * @param string $code
     *
     * @return ResourceInterface
     */
    private function getOptionValue(string $code): ResourceInterface
    {
        /** @var ResourceInterface $optionValue */
        $optionValue = $this->optionValueRepository->findOneBy(['code' => $code]);

        if (null === $optionValue) {
            $optionValue = $this->optionValueFactory->createNew();
        }

        return $optionValue;
    }
}
