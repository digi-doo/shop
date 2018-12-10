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

namespace AppBundle\Helpers\Promotion;

use AppBundle\Entity\AppAdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Model\AdjustmentInterface as OrderAdjustmentInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PaymentPercentageDiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    public const TYPE = 'payment_percentage_discount';

    /**
     * @var FactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @param FactoryInterface $adjustmentFactory
     */
    public function __construct(FactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): bool
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        if (!isset($configuration['percentage'])) {
            return false;
        }

        $adjustment = $this->createAdjustment($promotion);

        $adjustmentAmount = (int) round($subject->getAdjustmentsTotal(AppAdjustmentInterface::PAYMENT_ADJUSTMENT) * $configuration['percentage']);
        if (0 === $adjustmentAmount) {
            return false;
        }

        $adjustment->setAmount(-$adjustmentAmount);
        $subject->addAdjustment($adjustment);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedTypeException
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): void
    {
        if (!$subject instanceof OrderInterface && !$subject instanceof OrderItemInterface) {
            throw new UnexpectedTypeException(
                $subject,
                'Sylius\Component\Core\Model\OrderInterface or Sylius\Component\Core\Model\OrderItemInterface'
            );
        }

        foreach ($subject->getAdjustments(AppAdjustmentInterface::ORDER_PAYMENT_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion->getCode() === $adjustment->getOriginCode()) {
                $subject->removeAdjustment($adjustment);
            }
        }
    }

    /**
     * @param PromotionInterface $promotion
     * @param string $type
     *
     * @return OrderAdjustmentInterface
     */
    private function createAdjustment(
        PromotionInterface $promotion,
        string $type = AppAdjustmentInterface::ORDER_PAYMENT_PROMOTION_ADJUSTMENT
    ): OrderAdjustmentInterface {
        /** @var OrderAdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType($type);
        $adjustment->setLabel($promotion->getName());
        $adjustment->setOriginCode($promotion->getCode());

        return $adjustment;
    }
}
