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
 * AppBundle supplier translation entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class SupplierTranslation extends AbstractTranslation implements ResourceInterface
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
    private $delivery;

    /**
     * @var string|null
     */
    private $description;

    /**
     * Get supplier id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set supplier name
     *
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get supplier name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set supplier delivery
     *
     * @param string|null $delivery
     */
    public function setDelivery(?string $delivery): void
    {
        $this->delivery = $delivery;
    }

    /**
     * Get supplier delivery
     *
     * @return string|null
     */
    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    /**
     * Set supplier description
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get supplier description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
