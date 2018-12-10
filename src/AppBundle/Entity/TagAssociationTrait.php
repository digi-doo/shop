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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
trait TagAssociationTrait
{
    /**
     * @var ArrayCollection
     */
    protected $tags;

    public function initializeTagsCollection()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return ArrayCollection
     */
    public function getMinorTags(): ArrayCollection
    {
        return $this->tags->filter(function (Tag $tag) {
            return !$tag->isMainTag();
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getMainTags(): ArrayCollection
    {
        return $this->tags->filter(function (Tag $tag) {
            return $tag->isMainTag();
        });
    }

    /**
     * @param Tag $tag
     *
     * @return bool
     */
    public function hasTag(Tag $tag)
    {
        return $this->tags->contains($tag);
    }

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        $this->tags->add($tag);
    }

    /**
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        if (true === $this->hasTag($tag)) {
            $this->tags->removeElement($tag);
        }
    }
}
