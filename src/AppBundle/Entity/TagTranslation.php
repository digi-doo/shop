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

use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * AppBundle tag translation entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class TagTranslation extends AbstractTranslation implements ResourceInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $description;

    /**
     * Get tag id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tag name
     *
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get tag name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set tag slug
     *
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Get tag slug
     *
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set tag description
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get tag description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
