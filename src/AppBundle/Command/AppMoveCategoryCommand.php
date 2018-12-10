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

class AppMoveCategoryCommand extends ContainerAwareCommand
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
            ->setName('app:move-category')
            ->setDescription('Move given category and all its products to another parent category. If second argument is empty, category will be marked as ROOT.')
            ->addArgument('cat_to_move', InputArgument::REQUIRED, 'REQUIRED code of the category you want to move.')
            ->addArgument('parent_cat', InputArgument::OPTIONAL, 'OPTIONAL code of the parent category. Leave it blank to mark category as ROOT.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $catCode = $input->getArgument('cat_to_move');
        $parentCatCode = $input->getArgument('parent_cat');

        $cat = $this->getContainer()->get('sylius.repository.taxon')->findOneByCode($catCode);
        if (!$cat) {
            $this->io->error('Category with code ' . $catCode . ' was not found in database.');

            return 0;
        }

        $parentCat = $this->getContainer()->get('sylius.repository.taxon')->findOneByCode($parentCatCode);
        if (!$parentCat) {
            if (!$cat->getParent()) {
                $this->io->error('Category with code ' . $catCode . ' is already marked as ROOT.');

                return 0;
            }
            $this->io->note('Parent category with code ' . ($parentCatCode ? $parentCatCode : 'NULL') . ' was not found in database. Marking category as ROOT...');
            $this->moveCategory($cat, null);
        } else {
            if ($cat->getAncestors()->contains($parentCat)) {
                $this->io->error('Category with code ' . $catCode . ' is already under category with code ' . $parentCatCode);

                return 0;
            }
            $this->moveCategory($cat, $parentCat);
        }
    }

    /**
     * Move category under parent category
     *
     * @param  Taxon        $cat
     * @param  Taxon|null   $parentCat
     */
    protected function moveCategory(Taxon $cat, ?Taxon $parentCat)
    {
        $taxonManager = $this->getContainer()->get('sylius.manager.taxon');
        $taxonSlugGenerator = $this->getContainer()->get('sylius.generator.taxon_slug');

        // Remove old product relations to category ancestors
        if (!$cat->getAncestors()->isEmpty() && $cat->getParent() !== null) {
            $this->fixProductRelations($cat, true);
        }

        // Set desired parent category
        $cat->setParent($parentCat ? $parentCat : null);

        // Add new product relations to category ancestors
        if ($cat->getParent() !== null) {
            $this->fixProductRelations($cat, false);
        }

        // We don't want to change slugs due to SEO
        // $cat->setSlug($taxonSlugGenerator->generate($cat));

        // We don't want to change slugs due to SEO
        // if ($cat->hasChildren()) $this->fixChildrenSlugs($cat);

        $taxonManager->persist($cat);
        $taxonManager->flush();

        $this->io->success('Category with code ' . $cat->getCode() . ' and its children categories and products relations was moved under ' . ($parentCat ? ('parent category with code ' . $parentCat->getCode()) : 'ROOT.'));
    }

    /**
     * Fix product category children slugs
     *
     * @param  Taxon  $cat
     */
    private function fixChildrenSlugs(Taxon $cat)
    {
        $children = $cat->getChildren();
        $taxonSlugGenerator = $this->getContainer()->get('sylius.generator.taxon_slug');

        foreach ($children as $childCat) {
            $childCat->setSlug($taxonSlugGenerator->generate($childCat));
        }
    }

    /**
     * Fix product taxon relations
     *
     * @param  Taxon  $cat
     */
    private function fixProductRelations(Taxon $cat, bool $removeRelations)
    {
        $ancestors = $cat->getAncestors();
        $productTaxonRepo = $this->getContainer()->get('sylius.repository.product_taxon');
        $productTaxonFactory = $this->getContainer()->get('sylius.factory.product_taxon');
        $productTaxonManager = $this->getContainer()->get('sylius.manager.product_taxon');
        $productTaxons = $productTaxonRepo->findByTaxon($cat);
        $products = [];
        foreach ($productTaxons as $productTaxon) {
            $products[] = $productTaxon->getProduct();
        }

        if ($removeRelations && !$ancestors->isEmpty()) {
            // Remove old ancestor relations
            foreach ($ancestors as $ancestor) {
                foreach ($products as $product) {
                    if ($product->hasTaxon($ancestor)) {
                        $productTaxonToRemove = $productTaxonRepo->findOneBy(['product' => $product, 'taxon' => $ancestor]);
                        $product->removeProductTaxon($productTaxonToRemove);

                        $productTaxonManager->remove($productTaxonToRemove);
                    }
                }
            }
        } elseif (!$ancestors->isEmpty()) {
            // Add new ancestor relations
            foreach ($ancestors as $ancestor) {
                foreach ($products as $product) {
                    if (!$product->hasTaxon($ancestor)) {
                        $productTaxonToAdd = $productTaxonFactory->createNew();
                        $productTaxonToAdd->setProduct($product);
                        $productTaxonToAdd->setTaxon($ancestor);

                        $productTaxonManager->persist($productTaxonToAdd);
                    }
                }
            }
        }
    }
}
