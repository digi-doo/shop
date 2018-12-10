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

namespace AppBundle\Helpers\Order;

use Sylius\Component\Core\Cart\Modifier\LimitingOrderItemQuantityModifier;
use Sylius\Component\Core\Factory\CartItemFactory;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Webmozart\Assert\Assert;

class OrderGiftProcessor implements OrderProcessorInterface
{
    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productVariantRepo;

    /**
     * @var CartItemFactory
     */
    private $orderItemFactory;

    /**
     * @var LimitingOrderItemQuantityModifier
     */
    private $orderModifier;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
    * @param ProductVariantRepositoryInterface $productVariantRepo
    * @param CartItemFactory $orderItemFactory
    * @param LimitingOrderItemQuantityModifier $orderModifier
    * @param Session $session
    * @param RequestStack $requestStack
    */
    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepo,
        CartItemFactory $orderItemFactory,
        LimitingOrderItemQuantityModifier $orderModifier,
        Session $session,
        RequestStack $requestStack
    ) {
        $this->productVariantRepo = $productVariantRepo;
        $this->orderItemFactory = $orderItemFactory;
        $this->orderModifier = $orderModifier;
        $this->session = $session;
        $this->requestStack = $requestStack;
    }

    public function process(OrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $channel = $order->getChannel();
        if ($channel->getFreeGiftVariantCode() === null || $channel->getFreeGiftFrom() === null) {
            return;
        }

        $variant = $this->productVariantRepo->findOneByCode($channel->getFreeGiftVariantCode());
        if ($variant === null) {
            return;
        }

        if (!$variant->isInStock()) {
            return;
        }

        $this->removeGiftOrderItem($order, $variant);

        if ($order->getItemsTotal() >= $channel->getFreeGiftFrom()) {
            $orderItem = $this->orderItemFactory->createNew();
            $orderItem->setVariant($variant);
            $this->orderModifier->modify($orderItem, 1);
            $order->addItem($orderItem);

            $route = $this->requestStack->getCurrentRequest()->get('_route');
            if ($route === 'sylius_shop_cart_save' || $route === 'sylius_shop_ajax_cart_add_item') {
                $this->session->getFlashBag()->add('success', [
                    'message' => 'sylius.cart.add_gift_item',
                    'parameters' => ['%productName%' => $variant->getInventoryName()],
                ]);
            }
        }
    }

    /**
     * Remove order item as a gift or decrease its quantity
     *
     * @param  OrderInterface          $order
     * @param  ProductVariantInterface $productVariant
     */
    private function removeGiftOrderItem(OrderInterface $order, ProductVariantInterface $productVariant): void
    {
        $itemToRemove = null;
        foreach ($order->getItems() as $item) {
            if ($item->getVariant() === $productVariant) {
                $itemToRemove = $item;
            }
        }

        if ($itemToRemove !== null) {
            $order->removeItem($itemToRemove);
        }
    }
}
