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

use FriendsOfSylius\SyliusImportExportPlugin\Exception\AccessorNotFoundException;
use FriendsOfSylius\SyliusImportExportPlugin\Exception\ItemIncompleteException;
use FriendsOfSylius\SyliusImportExportPlugin\Processor\MetadataValidatorInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Processor\ResourceProcessor;
use FriendsOfSylius\SyliusImportExportPlugin\Processor\ResourceProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Asset\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class TaxonProcessor implements ResourceProcessorInterface
{
    /** @var FactoryInterface */
    private $resourceFactory;

    /** @var RepositoryInterface */
    private $resourceRepository;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    /** @var MetadataValidatorInterface */
    private $metadataValidator;

    /** @var array */
    private $headerKeys;

    /**
     * ResourceProcessor constructor.
     *
     * @param FactoryInterface $resourceFactory
     * @param RepositoryInterface $resourceRepository
     * @param PropertyAccessorInterface $propertyAccessor
     * @param MetadataValidatorInterface $metadataValidator
     * @param array $headerKeys
     */
    public function __construct(
        FactoryInterface $resourceFactory,
        RepositoryInterface $resourceRepository,
        PropertyAccessorInterface $propertyAccessor,
        MetadataValidatorInterface $metadataValidator,
        array $headerKeys
    ) {
        $this->resourceFactory = $resourceFactory;
        $this->resourceRepository = $resourceRepository;
        $this->propertyAccessor = $propertyAccessor;
        $this->metadataValidator = $metadataValidator;
        $this->headerKeys = $headerKeys;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AccessorNotFoundException
     * @throws ItemIncompleteException
     * @throws \Symfony\Component\PropertyAccess\Exception\AccessException
     * @throws \Symfony\Component\PropertyAccess\Exception\InvalidArgumentException
     * @throws \Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException
     */
    public function process(array $data): void
    {
        // $this->metadataValidator->validateHeaders($this->headerKeys, $data);
        $taxon = $this->completeTaxonHeaders($data);

        $resource = $this->getResource($taxon['Code']);

        foreach ($this->headerKeys as $headerKey) {
            // if (false === $this->propertyAccessor->isReadable($resource, $headerKey)) {
            //     throw new InvalidArgumentException(
            //         sprintf(
            //             'No Accessor found for %s in Resource %s, ' .
            //             'please implement one or change the Header-Key to an existing field',
            //             $headerKey,
            //             \get_class($resource)
            //         )
            //     );
            // }

            $this->propertyAccessor->setValue($resource, $headerKey, $taxon[$headerKey]);
        }

        $this->resourceRepository->add($resource);
    }

    /**
     * @param string $code
     *
     * @return ResourceInterface
     */
    private function getResource(string $code): ResourceInterface
    {
        /** @var ResourceInterface $resource */
        $resource = $this->resourceRepository->findOneBy(['code' => $code]);

        if (null === $resource) {
            $resource = $this->resourceFactory->createNew();
        }

        return $resource;
    }

    /**
     * Complete taxon headers
     *
     * @param array $data
     *
     * @return array
     */
    private function completeTaxonHeaders(array $data): array
    {
        // Parent
        $parentCode = $data['Parent-code'];
        unset($data['Parent-code']);

        if ($parentCode !== null) {
            /** @var ResourceInterface $resource */
            $data['Parent'] = $this->resourceRepository->findOneBy(['code' => $parentCode]);
        } else {
            $data['Parent'] = null;
        }

        // Enabled
        if ($data['Enabled'] === 'N') {
            /** @var ResourceInterface $resource */
            $data['Enabled'] = false;
        } else {
            $data['Enabled'] = true;
        }

        return $data;
    }
}
