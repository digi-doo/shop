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
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\Order as BaseOrder;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class Order extends BaseOrder implements OrderInterface
{
    /**
     * @var Collection|OrderInternalNote[]
     */
    protected $internalNotes;

    /**
     * @var \DateTimeInterface|null
     */
    protected $exportedAt;

    public function __construct()
    {
        parent::__construct();

        $this->internalNotes = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getInternalNotes(): Collection
    {
        return $this->internalNotes;
    }

    /**
     * @return bool
     */
    public function hasInternalNotes(): bool
    {
        return !$this->internalNotes->isEmpty();
    }

    /**
     * @return bool
     */
    public function hasUnapprovedInternalNotes(): bool
    {
        $notes = $this->internalNotes->filter(function (OrderInternalNote $note) {
            return !$note->isApproved();
        });

        return !$notes->isEmpty();
    }

    /**
     * @return bool
     */
    public function hasInternalNote(OrderInternalNote $internalNote): bool
    {
        return $this->internalNotes->contains($internalNote);
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExportedAt(): ?\DateTimeInterface
    {
        return $this->exportedAt;
    }

    /**
     * @param \DateTimeInterface|null $exportedAt
     */
    public function setExportedAt(?\DateTimeInterface $exportedAt): void
    {
        $this->exportedAt = $exportedAt;
    }

    /**
     * Returns payment fee together with taxes decreased by shipping discount.
     *
     * @return int
     */
    public function getPaymentTotal(): int
    {
        $paymentTotal = $this->getAdjustmentsTotal(AppAdjustmentInterface::PAYMENT_ADJUSTMENT);
        $paymentTotal += $this->getAdjustmentsTotal(AppAdjustmentInterface::ORDER_PAYMENT_PROMOTION_ADJUSTMENT);
        $paymentTotal += $this->getAdjustmentsTotal(AppAdjustmentInterface::PAYMENT_TAX_ADJUSTMENT);

        return $paymentTotal;
    }

    /**
     * Returns shipping fee together with taxes decreased by shipping discount.
     *
     * {@inheritdoc}
     */
    public function getShippingTotal(): int
    {
        $shippingTotal = $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingTotal += $this->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $shippingTotal += $this->getAdjustmentsTotal(AdjustmentInterface::TAX_ADJUSTMENT);

        return $shippingTotal;
    }

    /**
     * Override and use payment TAX adjustment
     * Returns sum of neutral and non neutral tax adjustments on order and total tax of order items.
     *
     * {@inheritdoc}
     */
    public function getTaxTotal(): int
    {
        $taxTotal = 0;

        foreach ($this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $taxAdjustment) {
            $taxTotal += $taxAdjustment->getAmount();
        }

        foreach ($this->getAdjustments(AppAdjustmentInterface::PAYMENT_TAX_ADJUSTMENT) as $paymentTaxAdjustment) {
            $taxTotal += $paymentTaxAdjustment->getAmount();
        }

        foreach ($this->items as $item) {
            $taxTotal += $item->getTaxTotal();
        }

        return $taxTotal;
    }

    /**
     * Returns amount of order discount. Does not include order item and shipping discounts.
     *
     * {@inheritdoc}
     */
    public function getOrderGiftPromotionTotal(): int
    {
        return $this->getAdjustmentsTotal(AppAdjustmentInterface::ORDER_GIFT_PROMOTION_ADJUSTMENT);
    }
}
