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

use AppBundle\Entity\Taxon;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppCopyCategoryCommand extends ContainerAwareCommand
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var string|null
     */
    private $childCodeToNotCopy;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:copy-category')
            ->setDescription('Copy given category and all its products to another parent category. If second argument is empty, category will be marked as ROOT.')
            ->addArgument('cat_to_copy', InputArgument::REQUIRED, 'REQUIRED code of the category you want to copy.')
            ->addArgument('parent_cat', InputArgument::OPTIONAL, 'OPTIONAL code of the parent category. Leave it blank to mark category as ROOT.')
            ->addArgument('child_code_to_not_copy', InputArgument::OPTIONAL, 'OPTIONAL code of the child category that you dont want to copy.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $catCode = $input->getArgument('cat_to_copy');
        $parentCatCode = $input->getArgument('parent_cat');
        $this->childCodeToNotCopy = $input->getArgument('child_code_to_not_copy');

        $cat = $this->getContainer()->get('sylius.repository.taxon')->findOneByCode($catCode);
        if (!$cat) {
            $this->io->error('Category with code ' . $catCode . ' was not found in database.');

            return 0;
        }

        $parentCat = $this->getContainer()->get('sylius.repository.taxon')->findOneByCode($parentCatCode);
        if (!$parentCat) {
            $this->io->note('Parent category with code ' . ($parentCatCode ? $parentCatCode : 'NULL') . ' was not found in database. Marking category as ROOT...');

            $this->copyCategory($cat, null);
        } else {
            $this->copyCategory($cat, $parentCat);
        }
    }

    /**
     * Move category under parent category
     *
     * @param  Taxon        $cat
     * @param  Taxon|null   $parentCat
     */
    protected function copyCategory(Taxon $cat, ?Taxon $parentCat)
    {
        $taxonManager = $this->getContainer()->get('sylius.manager.taxon');
        $taxonFactory = $this->getContainer()->get('sylius.factory.taxon');

        $newCat = $taxonFactory->createNew();
        $newCat->setCode($this->generateString($cat->getCode()));
        $newCat->setName($cat->getName());
        $newCat->setDescription($cat->getDescription());
        $newCat->setMetaKeywords($cat->getMetaKeywords());
        $newCat->setMetaDescription($cat->getMetaDescription());
        $newCat->setParent($parentCat ? $parentCat : null);
        $newCat->setSlug($this->resolveNewSlug($newCat));

        $taxonManager->persist($newCat);
        $taxonManager->flush();

        if ($cat->hasChildren()) {
            $this->copyCategoryChildren($cat, $newCat);
        }

        $this->copyProductRelations($cat, $newCat);

        $this->io->success('Category with code ' . $newCat->getCode() . ' and slug ' . $newCat->getSlug() . ' and its children categories and products relations was copied under ' . ($parentCat ? ('parent category with code ' . $parentCat->getCode()) : ' ROOT.'));
    }

    /**
     * Copy category children
     *
     * @param  Taxon  $oldCat
     * @param  Taxon  $newCat
     */
    protected function copyCategoryChildren(Taxon $oldCat, Taxon $newCat): void
    {
        $taxonManager = $this->getContainer()->get('sylius.manager.taxon');
        $taxonFactory = $this->getContainer()->get('sylius.factory.taxon');
        $taxonRepo = $this->getContainer()->get('sylius.repository.taxon');

        $batchSize = 20;
        $i = 0;
        foreach ($oldCat->getChildren() as $child) {
            // Continue if child taxon already exist
            if ($newCat->hasChild($child) || $child->getCode() === $this->childCodeToNotCopy) {
                continue;
            }
            $newChildCat = $taxonFactory->createNew($newCat);
            $newChildCat->setCode($this->generateString($child->getCode()));
            $newChildCat->setName($child->getName());
            $newChildCat->setDescription($child->getDescription());
            $newChildCat->setMetaKeywords($child->getMetaKeywords());
            $newChildCat->setMetaDescription($child->getMetaDescription());
            $newChildCat->setParent($newCat);
            $newChildCat->setSlug($this->resolveNewSlug($newChildCat));

            $taxonManager->persist($newChildCat);

            $this->copyProductRelations($child, $newChildCat);

            if (($i % $batchSize) === 0) {
                $taxonManager->flush();
            }
            ++$i;
        }
        $taxonManager->flush();
    }

    /**
     * @param  Taxon $newCat
     *
     * @return string
     */
    protected function resolveNewSlug(Taxon $newCat): string
    {
        $language = 'cs_CZ';
        $taxonSlugGenerator = $this->getContainer()->get('sylius.generator.taxon_slug');
        $taxonRepo = $this->getContainer()->get('sylius.repository.taxon');
        $newSlug = $taxonSlugGenerator->generate($newCat);

        if ($taxonRepo->findOneBySlug($newSlug, $language)) {
            return $this->generateString($newSlug, 3);
        }

        return $newSlug;
    }

    /**
     * @param  string $string
     * @param  int|int $length
     *
     * @return string
     */
    protected function generateString(string $string, int $length = 5): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $string . '-' . $randomString;
    }

    /**
     * Copy product taxon relations
     *
     * @param  Taxon  $oldCat
     * @param  Taxon  $newCat
     */
    private function copyProductRelations(Taxon $oldCat, ?Taxon $newCat): void
    {
        $productTaxonRepo = $this->getContainer()->get('sylius.repository.product_taxon');
        $productTaxonFactory = $this->getContainer()->get('sylius.factory.product_taxon');
        $productTaxonManager = $this->getContainer()->get('sylius.manager.product_taxon');
        $productTaxons = $productTaxonRepo->findByTaxon($oldCat);

        $taxonToNotCopy = null;
        if ($this->childCodeToNotCopy) {
            $taxonToNotCopy = $this->getContainer()->get('sylius.repository.taxon')->findOneByCode($this->childCodeToNotCopy);
        }

        $products = [];
        foreach ($productTaxons as $productTaxon) {
            $products[] = $productTaxon->getProduct();
        }

        if (!empty($products)) {
            // Save product to new category relation
            $batchSize = 20;
            $i = 0;
            foreach ($products as $product) {
                // Continue if product taxon already exist
                if ($product->hasTaxon($newCat) || ($taxonToNotCopy !== null && $product->hasTaxon($taxonToNotCopy))) {
                    continue;
                }
                // Add new product taxon
                $productTaxonToAdd = $productTaxonFactory->createNew();
                $productTaxonToAdd->setProduct($product);
                $productTaxonToAdd->setTaxon($newCat);

                $productTaxonManager->persist($productTaxonToAdd);

                if (($i % $batchSize) === 0) {
                    $productTaxonManager->flush();
                }
                ++$i;
            }
            $productTaxonManager->flush();
        }
    }
}
