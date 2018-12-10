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
 * AppBundle manufacturer translation entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ManufacturerTranslation extends AbstractTranslation implements ResourceInterface
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
     * @var string|null
     */
    private $description;

    /**
     * Get manufacturer id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set manufacturer name
     *
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get manufacturer name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set manufacturer slug
     *
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Get manufacturer slug
     *
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set manufacturer description
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get manufacturer description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
