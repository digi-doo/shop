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

use Sylius\Component\Core\Model\PaymentMethod as BasePaymentMethod;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * Extended Sylius payment method entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class PaymentMethod extends BasePaymentMethod implements TaxableInterface
{
    /**
     * @var TaxCategoryInterface
     */
    protected $taxCategory;
    /**
     * @var int
     */
    private $price;

    /**
     * @var string|null
     */
    private $externalCode;

    /**
     * Set price
     *
     * @param int $price
     */
    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCategory(): ?TaxCategoryInterface
    {
        return $this->taxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCategory(?TaxCategoryInterface $category): void
    {
        $this->taxCategory = $category;
    }

    /**
     * @return string|null
     */
    public function getExternalCode(): ?string
    {
        return $this->externalCode;
    }

    /**
     * @param string|null $externalCode
     */
    public function setExternalCode(?string $externalCode): void
    {
        $this->externalCode = $externalCode;
    }
}
