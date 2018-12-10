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
class ProductTaxonProcessor
{
    /** @var ContainerInterface */
    private $container;

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
        if ($data['Category-codes'] === null) {
            return;
        }
        $productCode = (string) $data['Product-code'];
        $taxonsRow = (string) $data['Category-codes'];

        $this->checkProduct($productCode, $loop);
        $this->checkTaxon($taxonsRow, $loop);

        $productTaxonFactory = $this->container->get('sylius.factory.product_taxon');
        $productTaxonManager = $this->container->get('sylius.manager.product_taxon');
        $productTaxonRepository = $this->container->get('sylius.repository.product_taxon');

        // Clear relations
        $product = $this->getProduct($productCode);
        if ($product && !$product->getProductTaxons()->isEmpty()) {
            foreach ($product->getProductTaxons() as $productTaxon) {
                $productTaxonRepository->remove($productTaxon);
            }
        }

        // Add relations
        foreach ($this->getTaxons($taxonsRow) as $taxon) {
            $productTaxon = $productTaxonFactory->createNew();

            $productTaxon->setProduct($product);
            $productTaxon->setTaxon($taxon);

            $productTaxonManager->persist($productTaxon);
        }
    }

    /**
     * Get product entity
     *
     * @param string $productCode
     *
     * @return ResourceInterface
     */
    private function getProduct(?string $productCode): ?ResourceInterface
    {
        $productRepo = $this->container->get('sylius.repository.product');

        return $productRepo->findOneBy(['code' => $productCode]);
    }

    /**
     * Get array of used taxons
     *
     * @param string $taxonsRow
     */
    private function getTaxons(?string $taxonsRow): array
    {
        $taxonRepo = $this->container->get('sylius.repository.taxon');

        $trimmedTaxonRow = str_replace(' ', '', $taxonsRow);
        $taxonRow = explode(',', str_replace(' ', '', $trimmedTaxonRow));

        $taxons = [];
        foreach ($taxonRow as $taxonCode) {
            $taxons[] = $taxonRepo->findOneBy(['code' => $taxonCode]);
        }

        return $taxons;
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
            throw new \Exception('Product with code ' . $productCode . ' on row ' . $loop . ' was not found in database!');
        }
    }

    /**
     * Check taxons entities
     *
     * @param string $taxonsRow
     *
     * @throws \Exception
     */
    private function checkTaxon(?string $taxonsRow, int $loop): void
    {
        $taxonRepo = $this->container->get('sylius.repository.taxon');

        $trimmedTaxonRow = str_replace(' ', '', $taxonsRow);
        $taxonRow = explode(',', str_replace(' ', '', $trimmedTaxonRow));

        foreach ($taxonRow as $taxonCode) {
            $taxonEntity = $taxonRepo->findOneBy(['code' => $taxonCode]);
            if (!$taxonEntity) {
                throw new \Exception('Taxon with code ' . $taxonCode . ' on row ' . $loop . ' was not found in database!');
            }
        }
    }
}
