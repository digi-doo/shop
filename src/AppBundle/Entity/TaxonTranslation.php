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

use Sylius\Component\Taxonomy\Model\TaxonTranslation as BaseTaxonTranslation;

class TaxonTranslation extends BaseTaxonTranslation
{
    /**
     * @var string
     */
    protected $metaKeywords;

    /**
     * @var string
     */
    protected $metaDescription;

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }
}
