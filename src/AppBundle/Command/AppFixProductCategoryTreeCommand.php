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

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppFixProductCategoryTreeCommand extends ContainerAwareCommand
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:fix-product-category-tree')
            ->setDescription('Fix missing category tree parents product relations.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $productRepo = $this->getContainer()->get('sylius.repository.product');
        $productTaxonManager = $this->getContainer()->get('sylius.manager.product_taxon');
        $productTaxonFactory = $this->getContainer()->get('sylius.factory.product_taxon');

        $products = $productRepo->findAll();
        $this->io->progressStart(count($products));
        $batchSize = 20;
        $i = 0;
        $notOk = [];
        $missing = 0;
        foreach ($products as $product) {
            $taxons = $product->getTaxons();
            $addedTaxonCode = null;
            foreach ($taxons as $taxon) {
                if ($taxon->getParent() && $taxons->contains($taxon->getParent())) {
                    // Ok, parent relation exists
                    continue;
                }
                if ($taxon->getParent() && $addedTaxonCode !== $taxon->getParent()->getCode()) {
                    // Not OK, create product taxon relation
                    $notOk[] = [$product->getCode(), $taxon->getParent()->getCode()];
                    ++$missing;

                    $productTaxonToAdd = $productTaxonFactory->createNew();
                    $productTaxonToAdd->setProduct($product);
                    $productTaxonToAdd->setTaxon($taxon->getParent());

                    $productTaxonManager->persist($productTaxonToAdd);
                    $addedTaxonCode = $taxon->getParent()->getCode();

                    if (($i % $batchSize) === 0) {
                        $productTaxonManager->flush();
                    }
                    ++$i;
                }
            }

            $this->io->progressAdvance();
        }
        $productTaxonManager->flush();
        $this->io->progressFinish();

        if ($missing > 0) {
            $this->io->warning('Found ' . $missing . ' missing product to categories relations.');
            $this->io->table(['Product Code', 'Category Code'], $notOk);
        }
        $this->io->success('All missing relations to parents in category tree were fixed.');
    }
}
