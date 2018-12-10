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

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * AppBundle tag entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class Tag implements ResourceInterface, TranslatableInterface
{
    use StockSortingTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    /**
     * @var int
     * @Groups({"Autocomplete"})
     */
    private $id;

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var string
     */
    private $color = '#FF0000';

    /**
     * @var string
     */
    private $textColor = '#000000';

    /**
     * @var string
     */
    private $code;

    /**
     * @var bool
     */
    private $mainTag = false;

    /**
     * Initialize translation dependency
     */
    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tag code
     *
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get tag code
     *
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set color
     *
     * @param string $color
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * Set text color
     *
     * @param string $textColor
     */
    public function setTextColor(?string $textColor): void
    {
        $this->textColor = $textColor;
    }

    /**
     * Get text color
     *
     * @return string
     */
    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    /**
     * Set tag name
     *
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * Get tag name
     *
     * @return string
     * @Groups({"Autocomplete"})
     * @VirtualProperty()
     */
    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    /**
     * Set tag slug
     *
     * @param string $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->getTranslation()->setSlug($slug);
    }

    /**
     * Get tag slug
     *
     * @return string
     * @Groups({"Autocomplete"})
     * @VirtualProperty()
     */
    public function getSlug(): ?string
    {
        return $this->getTranslation()->getSlug();
    }

    /**
     * Enable/disable tag
     *
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled/disabled tag
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Enable/disable main tag
     *
     * @param bool $mainTag
     */
    public function setMainTag(bool $mainTag): void
    {
        $this->mainTag = $mainTag;
    }

    /**
     * Get enabled/disabled main tag
     *
     * @return bool
     */
    public function isMainTag(): bool
    {
        return $this->mainTag;
    }

    /**
     * Set tag description
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    /**
     * Get tag description
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
        return new TagTranslation();
    }
}
