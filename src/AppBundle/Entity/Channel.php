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

use Sylius\Component\Core\Model\Channel as BaseChannel;

/**
 * AppBundle extended sylius channel entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class Channel extends BaseChannel
{
    /**
     * @var string|null
     */
    private $stockEmails;

    /**
     * @var string|null
     */
    private $orderEmails;

    /**
     * @var string|null
     */
    private $bankAccount;

    /**
     * @var string|null
     */
    private $supportNumber;

    /**
     * @var string|null
     */
    private $supportEmail;

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var string|null
     */
    private $opening;

    /**
     * @var string|null
     */
    private $metaTitle;

    /**
     * @var string|null
     */
    private $metaDescription;

    /**
     * @var string|null
     */
    private $metaKeywords;

    /**
     * @var bool
     */
    private $metaRobots = false;

    /**
     * @var string|null
     */
    private $metaAuthor;

    /**
     * @var int|null
     */
    private $freeShippingFrom;

    /**
     * @var int|null
     */
    private $freeGiftFrom;

    /**
     * @var string|null
     */
    private $freeGiftVariantCode;

    /**
     * Set free shipping for channel
     *
     * @param int $freeShippingFrom
     */
    public function setFreeShippingFrom(?int $freeShippingFrom): void
    {
        $this->freeShippingFrom = $freeShippingFrom;
    }

    /**
     * Get free shipping for channel
     *
     * @return int
     */
    public function getFreeShippingFrom(): ?int
    {
        return $this->freeShippingFrom;
    }

    /**
     * Enable/disable meta robots
     *
     * @param bool $metaRobots
     */
    public function setMetaRobots(bool $metaRobots): void
    {
        $this->metaRobots = $metaRobots;
    }

    /**
     * Get enabled/disabled meta robots
     *
     * @return bool
     */
    public function isMetaRobots(): bool
    {
        return $this->metaRobots;
    }

    /**
     * Set global meta title
     *
     * @param string|null $metaTitle
     */
    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * Get global meta title
     *
     * @return string|null
     */
    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    /**
     * Set global meta author
     *
     * @param string|null $metaAuthor
     */
    public function setMetaAuthor(?string $metaAuthor): void
    {
        $this->metaAuthor = $metaAuthor;
    }

    /**
     * Get global meta author
     *
     * @return string|null
     */
    public function getMetaAuthor(): ?string
    {
        return $this->metaAuthor;
    }

    /**
     * Set global meta description
     *
     * @param string|null $metaDescription
     */
    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * Get global meta description
     *
     * @return string|null
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * Set global meta keywords
     *
     * @param string|null $metaKeywords
     */
    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * Get global meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    /**
     * Set emails to notify about stock
     *
     * @param string|null $stockEmails
     */
    public function setStockEmails(?string $stockEmails): void
    {
        $this->stockEmails = $stockEmails;
    }

    /**
     * Get emails for notify about stock
     *
     * @return string|null
     */
    public function getStockEmails(): ?string
    {
        return $this->stockEmails;
    }

    /**
     * Set emails to notify about order
     *
     * @param string|null $orderEmails
     */
    public function setOrderEmails(?string $orderEmails): void
    {
        $this->orderEmails = $orderEmails;
    }

    /**
     * Get emails for notify about order
     *
     * @return string|null
     */
    public function getOrderEmails(): ?string
    {
        return $this->orderEmails;
    }

    /**
     * Set telephone number for channel support
     *
     * @param string|null $supportNumber
     */
    public function setSupportNumber(?string $supportNumber): void
    {
        $this->supportNumber = $supportNumber;
    }

    /**
     * Get telephone number for channel support
     *
     * @return string|null
     */
    public function getSupportNumber(): ?string
    {
        return $this->supportNumber;
    }

    /**
     * Set email for channel support
     *
     * @param string|null $supportEmail
     */
    public function setSupportEmail(?string $supportEmail): void
    {
        $this->supportEmail = $supportEmail;
    }

    /**
     * Get email for channel support
     *
     * @return string|null
     */
    public function getSupportEmail(): ?string
    {
        return $this->supportEmail;
    }

    /**
     * Get superadmin email
     *
     * @return string|null
     */
    public function getSuperAdminEmail(): ?string
    {
        return $this->getContactEmail();
    }

    /**
     * Get array of stock emails or array with admin email
     *
     * @return []
     */
    public function getNotificationStockEmails(): array
    {
        return $this->stockEmails ? array_map('trim', explode(',', $this->stockEmails)) : [$this->getContactEmail()];
    }

    /**
     * Get array of admin emails or array with admin email
     *
     * @return []
     */
    public function getNotificationAdminOrderEmails(): array
    {
        return $this->orderEmails ? array_map('trim', explode(',', $this->orderEmails)) : [$this->getContactEmail()];
    }

    /**
     * Set bank account for channel
     *
     * @param string|null $bankAccount
     */
    public function setBankAccount(?string $bankAccount): void
    {
        $this->bankAccount = $bankAccount;
    }

    /**
     * Get bank account for channel
     *
     * @return string|null
     */
    public function getBankAccount(): ?string
    {
        return $this->bankAccount;
    }

    /**
     * Set shop address for channel
     *
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * Get bank account for channel
     *
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Set shop opening for channel
     *
     * @param string|null $opening
     */
    public function setOpening(?string $opening): void
    {
        $this->opening = $opening;
    }

    /**
     * Get opening for channel
     *
     * @return string|null
     */
    public function getOpening(): ?string
    {
        return $this->opening;
    }

    /**
     * @return int|null
     */
    public function getFreeGiftFrom()
    {
        return $this->freeGiftFrom;
    }

    /**
     * @param int|null $freeGiftFrom
     *
     * @return self
     */
    public function setFreeGiftFrom($freeGiftFrom)
    {
        $this->freeGiftFrom = $freeGiftFrom;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFreeGiftVariantCode()
    {
        return $this->freeGiftVariantCode;
    }

    /**
     * @param string|null $freeGiftVariantCode
     *
     * @return self
     */
    public function setFreeGiftVariantCode($freeGiftVariantCode)
    {
        $this->freeGiftVariantCode = $freeGiftVariantCode;

        return $this;
    }
}
