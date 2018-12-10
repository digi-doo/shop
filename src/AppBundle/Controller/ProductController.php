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

namespace AppBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ProductController extends ResourceController
{
    private const BULK_UPDATE_PRODUCT_DISCOUNT = 'bulk_update_product_discount';
    private const BULK_UPDATE_PRODUCT_VISIBILITY = 'bulk_update_product_visibility';
    private const LIMIT_NO_CHANGE = 'no_change';
    private const LIMIT_NONE = 'none';
    private const LIMIT_STOCK = 'stock';
    private const LIMIT_DATETIME = 'datetime';

    /**
     * Save product slug to session and call show action
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function showSaveSessionAction(Request $request): Response
    {
        $session = $this->container->get('sylius.storage.session');
        $viewedProducts = $session->get('viewed_products');

        if ($viewedProducts === null) {
            $viewedProducts = [];
        }

        if (in_array($request->get('slug'), $viewedProducts)) {
            $key = array_search($request->get('slug'), $viewedProducts);
            unset($viewedProducts[$key]);
            array_unshift($viewedProducts, $request->get('slug'));
        } else {
            array_unshift($viewedProducts, $request->get('slug'));
        }

        if (count($viewedProducts) > 3) {
            array_splice($viewedProducts, -1);
        }

        $session->set('viewed_products', $viewedProducts);

        return $this->showAction($request);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function bulkProductDiscountAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, self::BULK_UPDATE_PRODUCT_DISCOUNT);

        // No products checked
        $products = $this->resourcesCollectionProvider->get($configuration, $this->repository);
        if (empty($products)) {
            $this->addFlash('info', 'sylius.product.bulk_update_discount.no_products');

            return $this->redirectHandler->redirectToReferer($configuration);
        }

        $validator = $this->validateBulkDiscountForm($request);
        if (!empty($validator)) {
            $this->addFlash($validator['type'], $validator['message']);

            return $this->redirectHandler->redirectToReferer($configuration);
        }

        if (
            $configuration->isCsrfProtectionEnabled() &&
            !$this->isCsrfTokenValid(self::BULK_UPDATE_PRODUCT_DISCOUNT, $request->request->get('_csrf_token'))
        ) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        $this->eventDispatcher->dispatchMultiple(self::BULK_UPDATE_PRODUCT_DISCOUNT, $configuration, $products);

        $batchSize = 20;
        $i = 0;
        foreach ($products as $product) {
            $event = $this->eventDispatcher->dispatchPreEvent(self::BULK_UPDATE_PRODUCT_DISCOUNT, $configuration, $product);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $this->redirectHandler->redirectToIndex($configuration, $product);
            }

            try {
                $this->updateProductDiscount($product, $request);
            } catch (UpdateHandlingException $exception) {
                if (!$configuration->isHtmlRequest()) {
                    return $this->viewHandler->handle(
                        $configuration,
                        View::create(null, $exception->getApiResponseCode())
                    );
                }

                $this->flashHelper->addErrorFlash($configuration, $exception->getFlash());

                return $this->redirectHandler->redirectToReferer($configuration);
            }

            if (($i % $batchSize) === 0) {
                $this->manager->flush();
            }
            ++$i;
            $postEvent = $this->eventDispatcher->dispatchPostEvent(self::BULK_UPDATE_PRODUCT_DISCOUNT, $configuration, $product);
        }
        $this->manager->flush();

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        // Resolve success message
        $this->resolveBulkDiscountFlashMessage($request);

        if (isset($postEvent) && $postEvent->hasResponse()) {
            return $postEvent->getResponse();
        }

        return $this->redirectHandler->redirectToIndex($configuration);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function bulkProductVisibilityAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, self::BULK_UPDATE_PRODUCT_VISIBILITY);

        // No products checked
        $products = $this->resourcesCollectionProvider->get($configuration, $this->repository);
        if (empty($products)) {
            $this->addFlash('info', 'sylius.product.bulk_update_visibility.no_products');

            return $this->redirectHandler->redirectToReferer($configuration);
        }

        if (
            $configuration->isCsrfProtectionEnabled() &&
            !$this->isCsrfTokenValid('bulk_update_visibility', $request->request->get('_csrf_token'))
        ) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        $this->eventDispatcher->dispatchMultiple(self::BULK_UPDATE_PRODUCT_VISIBILITY, $configuration, $products);

        $batchSize = 20;
        $i = 0;
        foreach ($products as $product) {
            $event = $this->eventDispatcher->dispatchPreEvent(self::BULK_UPDATE_PRODUCT_VISIBILITY, $configuration, $product);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $this->redirectHandler->redirectToIndex($configuration, $product);
            }

            try {
                $this->updateProductVisibility($product, $request);
            } catch (UpdateHandlingException $exception) {
                if (!$configuration->isHtmlRequest()) {
                    return $this->viewHandler->handle(
                        $configuration,
                        View::create(null, $exception->getApiResponseCode())
                    );
                }

                $this->flashHelper->addErrorFlash($configuration, $exception->getFlash());

                return $this->redirectHandler->redirectToReferer($configuration);
            }

            if (($i % $batchSize) === 0) {
                $this->manager->flush();
            }
            ++$i;
            $postEvent = $this->eventDispatcher->dispatchPostEvent(self::BULK_UPDATE_PRODUCT_VISIBILITY, $configuration, $product);
        }
        $this->manager->flush();

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        // Resolve success message
        $this->resolveBulkVisibilityFlashMessage($request);

        if (isset($postEvent) && $postEvent->hasResponse()) {
            return $postEvent->getResponse();
        }

        return $this->redirectHandler->redirectToIndex($configuration);
    }

    /**
     * Update given products and variants discounts
     *
     * @param  ProductInterface $product
     * @param  Request $request
     */
    private function updateProductDiscount(
        ProductInterface $product,
        Request $request
    ): void {
        $discountPercent = $request->request->get('discount_percent') !== '' ? (float) $request->request->get('discount_percent') : null;
        $discountLimitType = $request->request->get('discount_limit_type') !== '' ? $request->request->get('discount_limit_type') : null;
        $discountFromDate = $request->request->get('discount_from_date') !== '' ? $request->request->get('discount_from_date') : null;
        $discountToDate = $request->request->get('discount_to_date') !== '' ? $request->request->get('discount_to_date') : null;

        $variants = $product->getVariants();
        foreach ($variants as $variant) {
            // Variant to change
            $variantPricing = $variant->getChannelPricings()->first();

            // Change percent
            if ($discountPercent !== null) {
                $variantPricing->setDiscount($discountPercent / 100);
            }

            // Continue if no change type
            if ($discountLimitType === self::LIMIT_NO_CHANGE) {
                continue;
            }
            // Discount type
            $variantPricing->setDiscountLimitType($discountLimitType === self::LIMIT_NONE ? null : $discountLimitType);

            // Date type discount
            if ($discountLimitType === self::LIMIT_DATETIME) {
                if ($discountFromDate && $discountToDate) {
                    $dateFrom = \DateTime::createFromFormat('Y-m-d', $discountFromDate);
                    $dateTo = \DateTime::createFromFormat('Y-m-d', $discountToDate);

                    $variantPricing->setDiscountFrom($dateFrom);
                    $variantPricing->setDiscountTo($dateTo);
                }

                if ($discountFromDate && !$discountToDate) {
                    $dateFrom = \DateTime::createFromFormat('Y-m-d', $discountFromDate);

                    $variantPricing->setDiscountFrom($dateFrom);
                    $variantPricing->setDiscountTo(null);
                }

                if (!$discountFromDate && $discountToDate) {
                    $dateTo = \DateTime::createFromFormat('Y-m-d', $discountToDate);

                    $variantPricing->setDiscountFrom(null);
                    $variantPricing->setDiscountTo($dateTo);
                }
            }
        }
    }

    /**
     * @param  Request $request
     */
    private function resolveBulkDiscountFlashMessage(Request $request): void
    {
        $discountPercent = $request->request->get('discount_percent') !== '' ? (float) $request->request->get('discount_percent') : null;
        $discountLimitType = $request->request->get('discount_limit_type') !== '' ? $request->request->get('discount_limit_type') : null;
        $discountFromDate = $request->request->get('discount_from_date') !== '' ? $request->request->get('discount_from_date') : null;
        $discountToDate = $request->request->get('discount_to_date') !== '' ? $request->request->get('discount_to_date') : null;

        if ($discountPercent !== null && $discountLimitType === self::LIMIT_NO_CHANGE) {
            // Only percent and no type
            $this->addFlash('success', [
                'message' => 'sylius.product.bulk_update_discount.percent_no_type',
                'parameters' => ['%discount%' => $discountPercent],
            ]);
        }
        if ($discountPercent !== null && $discountLimitType === self::LIMIT_NONE) {
            // Percent and type NONE
            $this->addFlash('success', [
                'message' => 'sylius.product.bulk_update_discount.percent_type_none',
                'parameters' => ['%discount%' => $discountPercent],
            ]);
        }
        if ($discountPercent !== null && $discountLimitType === self::LIMIT_STOCK) {
            // Percent and type STOCK
            $this->addFlash('success', [
                'message' => 'sylius.product.bulk_update_discount.percent_type_stock',
                'parameters' => ['%discount%' => $discountPercent],
            ]);
        }
        if ($discountPercent !== null && $discountLimitType === self::LIMIT_DATETIME && $discountFromDate && $discountToDate) {
            // Percent and type DATE with from and to
            $dateFrom = \DateTime::createFromFormat('Y-m-d', $discountFromDate);
            $dateTo = \DateTime::createFromFormat('Y-m-d', $discountToDate);
            $this->addFlash('success', [
                'message' => 'sylius.product.bulk_update_discount.percent_date_from_to',
                'parameters' => ['%discount%' => $discountPercent, '%dateFrom%' => $dateFrom->format('d.m.Y'), '%dateTo%' => $dateTo->format('d.m.Y')],
            ]);
        }
        if ($discountPercent !== null && $discountLimitType === self::LIMIT_DATETIME && $discountFromDate && !$discountToDate) {
            // Percent and type DATE with from
            $dateFrom = \DateTime::createFromFormat('Y-m-d', $discountFromDate);
            $this->addFlash('success', [
                'message' => 'sylius.product.bulk_update_discount.percent_date_from',
                'parameters' => ['%discount%' => $discountPercent, '%dateFrom%' => $dateFrom->format('d.m.Y')],
            ]);
        }
        if ($discountPercent !== null && $discountLimitType === self::LIMIT_DATETIME && !$discountFromDate && $discountToDate) {
            // Percent and type DATE with to
            $dateTo = \DateTime::createFromFormat('Y-m-d', $discountToDate);
            $this->addFlash('success', [
                'message' => 'sylius.product.bulk_update_discount.percent_date_to',
                'parameters' => ['%discount%' => $discountPercent, '%dateTo%' => $dateTo->format('d.m.Y')],
            ]);
        }

        if ($discountPercent === null && $discountLimitType === self::LIMIT_NONE) {
            // Only type to NONE
            $this->addFlash('success', 'sylius.product.bulk_update_discount.no_percent_type_none');
        }
        if ($discountPercent === null && $discountLimitType === self::LIMIT_STOCK) {
            // Only type to STOCK
            $this->addFlash('success', 'sylius.product.bulk_update_discount.no_percent_type_stock');
        }
        if ($discountPercent === null && $discountLimitType === self::LIMIT_DATETIME && $discountFromDate && $discountToDate) {
            // Only type to DATE and from and to
            $dateFrom = \DateTime::createFromFormat('Y-m-d', $discountFromDate);
            $dateTo = \DateTime::createFromFormat('Y-m-d', $discountToDate);
            $this->addFlash('success', [
                'message' => 'sylius.product.bulk_update_discount.no_percent_date_from_to',
                'parameters' => ['%dateFrom%' => $dateFrom->format('d.m.Y'), '%dateTo%' => $dateTo->format('d.m.Y')],
            ]);
        }
        if ($discountPercent === null && $discountLimitType === self::LIMIT_DATETIME && $discountFromDate && !$discountToDate) {
            // Only type to DATE and from
            $dateFrom = \DateTime::createFromFormat('Y-m-d', $discountFromDate);
            $this->addFlash('success', [
                'message' => 'sylius.product.bulk_update_discount.no_percent_date_from',
                'parameters' => ['%dateFrom%' => $dateFrom->format('d.m.Y')],
            ]);
        }
        if ($discountPercent === null && $discountLimitType === self::LIMIT_DATETIME && !$discountFromDate && $discountToDate) {
            // Only type to DATE and to
            $dateTo = \DateTime::createFromFormat('Y-m-d', $discountToDate);
            $this->addFlash('success', [
                'message' => 'sylius.product.bulk_update_discount.no_percent_date_to',
                'parameters' => ['%dateTo%' => $dateTo->format('d.m.Y')],
            ]);
        }
    }

    /**
     * @param  Request $request
     *
     * @return array
     */
    private function validateBulkDiscountForm(Request $request): array
    {
        $discountPercent = $request->request->get('discount_percent') !== '' ? (float) $request->request->get('discount_percent') : null;
        $discountLimitType = $request->request->get('discount_limit_type') !== '' ? $request->request->get('discount_limit_type') : null;
        $discountFromDate = $request->request->get('discount_from_date') !== '' ? $request->request->get('discount_from_date') : null;
        $discountToDate = $request->request->get('discount_to_date') !== '' ? $request->request->get('discount_to_date') : null;

        // No changes selected
        if ($discountPercent === null && $discountLimitType === self::LIMIT_NO_CHANGE) {
            return ['type' => 'info', 'message' => 'sylius.product.bulk_update_discount.no_change'];
        }

        // Limit datetime without one of dates
        if ($discountLimitType === self::LIMIT_DATETIME && !$discountFromDate && !$discountToDate) {
            return ['type' => 'error', 'message' => 'sylius.product.bulk_update_discount.invalidate_datetime'];
        }

        // Limit datetime incorrect date interval
        if ($discountLimitType === self::LIMIT_DATETIME && $discountFromDate && $discountToDate) {
            $dateFrom = \DateTime::createFromFormat('Y-m-d', $discountFromDate);
            $dateTo = \DateTime::createFromFormat('Y-m-d', $discountToDate);
            if ($dateTo < $dateFrom) {
                return ['type' => 'error', 'message' => 'sylius.product.bulk_update_discount.invalidate_from_to'];
            }

            return [];
        }

        return [];
    }

    /**
     * Update given products visibilities
     *
     * @param  ProductInterface $product
     * @param  Request $request
     */
    private function updateProductVisibility(ProductInterface $product, Request $request): void
    {
        $visible = $request->request->get('visible') === '1' ? true : false;

        if ($visible) {
            $product->enable();
        } else {
            $product->disable();
        }
    }

    /**
     * @param  Request $request
     */
    private function resolveBulkVisibilityFlashMessage(Request $request): void
    {
        $visible = $request->request->get('visible') === '1' ? true : false;

        if ($visible) {
            $this->addFlash('success', 'sylius.product.bulk_update_visibility.toggle_visible');
        } else {
            $this->addFlash('success', 'sylius.product.bulk_update_visibility.toggle_invisible');
        }
    }
}
