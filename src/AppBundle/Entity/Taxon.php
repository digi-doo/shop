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

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\Taxon as BaseTaxon;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

/**
 * Extended Sylius taxon entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class Taxon extends BaseTaxon
{
    use StockSortingTrait;

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var bool
     */
    private $filterEnabled = true;

    /**
     * @param bool $filterEnabled
     */
    public function setFilterEnabled(?bool $filterEnabled): void
    {
        $this->filterEnabled = $filterEnabled;
    }

    /**
     * @return bool|null
     */
    public function isFilterEnabled(): ?bool
    {
        return $this->filterEnabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(?bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @return []
     **/
    public function getChildrenSlugs(): ?array
    {
        $childrenSlugs = [];
        foreach ($this->getChildren() as $child) {
            $childrenSlugs[] = $child->getSlug();
        }

        return $childrenSlugs;
    }

    /**
     * @return []
     **/
    public function getAncestorsSlugs(): ?array
    {
        $ancestorsSlugs = [];
        foreach ($this->getAncestors() as $ancestor) {
            $ancestorsSlugs[] = $ancestor->getSlug();
        }

        return $ancestorsSlugs;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords(): ?string
    {
        return $this->getTranslation()->getMetaKeywords();
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->getTranslation()->setMetaKeywords($metaKeywords);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription(): ?string
    {
        return $this->getTranslation()->getMetaDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription(?string $metaDescription): void
    {
        $this->getTranslation()->setMetaDescription($metaDescription);
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation(): TaxonTranslationInterface
    {
        return new TaxonTranslation();
    }
}
