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

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * AppBundle manufacturer entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class Manufacturer implements ResourceInterface, TranslatableInterface
{
    use StockSortingTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    /**
     * @var bool
     */
    protected $filterable = true;

    /**
     * @var ImageInterface|null
     */
    protected $image;

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
     * @var bool
     */
    private $stockSortingEnabled = true;

    /**
     * Initialize translation dependency
     */
    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    /**
     * @param bool $stockSortingEnabled
     */
    public function setStockSortingEnabled(?bool $stockSortingEnabled): void
    {
        $this->stockSortingEnabled = $stockSortingEnabled;
    }

    /**
     * @return bool|null
     */
    public function isStockSortingEnabled(): ?bool
    {
        return $this->stockSortingEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage(): ?ImageInterface
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage(?ImageInterface $image): void
    {
        $image->setOwner($this);

        $this->image = $image;
    }

    /**
     * @return bool
     */
    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    /**
     * @param bool $filterable
     */
    public function setFilterable(?bool $filterable): void
    {
        $this->filterable = (bool) $filterable;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set manufacturer code
     *
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get manufacturer code
     *
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set manufacturer name
     *
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * Get manufacturer name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    /**
     * Set manufacturer nslug
     *
     * @param string $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->getTranslation()->setSlug($slug);
    }

    /**
     * Get manufacturer slug
     *
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->getTranslation()->getSlug();
    }

    /**
     * Enable/disable manufacturer
     *
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled/disabled manufacturer
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Set manufacturer description
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    /**
     * Get manufacturer description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new ManufacturerTranslation();
    }
}
