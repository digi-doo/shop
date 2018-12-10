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

namespace AppBundle\Validator;

use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemAvailability;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class CartItemAvailabilityValidator extends ConstraintValidator
{
    /**
     * @var AvailabilityCheckerInterface
     */
    private $availabilityChecker;

    /**
     * @param AvailabilityCheckerInterface $availabilityChecker
     */
    public function __construct(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * @param AddToCartCommandInterface $addCartItemCommand
     *
     * {@inheritdoc}
     */
    public function validate($addCartItemCommand, Constraint $constraint): void
    {
        Assert::isInstanceOf($addCartItemCommand, AddToCartCommandInterface::class);
        Assert::isInstanceOf($constraint, CartItemAvailability::class);

        /** @var OrderItemInterface $cartItem */
        $cartItem = $addCartItemCommand->getCartItem();

        $channel = $cartItem->getVariant()->getProduct()->getChannels()->first();
        $isNotForSale = $channel->getFreeGiftVariantCode() === $cartItem->getVariant()->getCode();
        if ($isNotForSale) {
            $product = $cartItem->getVariant()->getProduct();
            $this->context->addViolation(
                $product->isSimple() ? 'sylius.cart_item.product_not_for_sale' : 'sylius.cart_item.variant_not_for_sale',
                [
                    '%itemName%' => $cartItem->getVariant()->getInventoryName(),
                ]
            );
        }

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $cartItem->getVariant(),
            $cartItem->getQuantity() + $this->getExistingCartItemQuantityFromCart($addCartItemCommand->getCart(), $cartItem)
        );
        if (!$isStockSufficient && !$isNotForSale) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '%itemName%' => $cartItem->getVariant()->getInventoryName(),
                    '%itemOnHand%' => ($cartItem->getVariant()->getOnHand() - $cartItem->getVariant()->getOnHold()),
                ]
            );
        }
    }

    /**
     * @param OrderInterface $cart
     * @param OrderItemInterface $cartItem
     *
     * @return int
     */
    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem): int
    {
        foreach ($cart->getItems() as $existingCartItem) {
            if ($existingCartItem->equals($cartItem)) {
                return $existingCartItem->getQuantity();
            }
        }

        return 0;
    }
}
