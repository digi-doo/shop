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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * AppBundle supplier entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class Supplier implements ResourceInterface, TranslatableInterface
{
    // Sylius translatable trait
    // use ProductDefaultTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * Initialize translation dependency
     */
    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set supplier code
     *
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get supplier code
     *
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set supplier name
     *
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * Get supplier name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    /**
     * Enable/disable supplier
     *
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled/disabled supplier
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Set supplier description
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    /**
     * Get supplier description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    /**
     * Set supplier delivery
     *
     * @param string|null $delivery
     */
    public function setDelivery(?string $delivery): void
    {
        $this->getTranslation()->setDelivery($delivery);
    }

    /**
     * Get supplier delivery
     *
     * @return string|null
     */
    public function getDelivery(): ?string
    {
        return $this->getTranslation()->getDelivery();
    }

    /**
     * Concat name and delivery
     *
     * @return string
     */
    public function getNameDelivery(): string
    {
        return $this->getTranslation()->getName() . ' (' . $this->getTranslation()->getDelivery() . ')';
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new SupplierTranslation();
    }
}
