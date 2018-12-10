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
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * Extended Sylius shipping method entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ShippingMethod extends BaseShippingMethod
{
    /**
     * @var ArrayCollection
     */
    protected $paymentMethods;

    /**
     * Set paymentMethods as ArrayCollection
     */
    public function __construct()
    {
        parent::__construct();

        $this->paymentMethods = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }

    /**
     * Check if shipping method has payment method
     *
     * @return bool|null
     */
    public function hasPaymentMethod(PaymentMethodInterface $paymentMethod): bool
    {
        return $this->paymentMethods->contains($paymentMethod);
    }

    /**
     * Add payment method to the shipping method
     *
     * @param PaymentMethodInterface
     */
    public function addPaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        if (!$this->hasPaymentMethod($paymentMethod)) {
            $this->paymentMethods->add($paymentMethod);
        }
    }

    /**
     * Remove payment given method from shipping method
     *
     * @param PaymentMethodInterface
     */
    public function removePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        if ($this->hasPaymentMethod($paymentMethod)) {
            $this->paymentMethods->removeElement($paymentMethod);
        }
    }
}
