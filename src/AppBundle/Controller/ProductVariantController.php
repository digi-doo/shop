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
use Sylius\Bundle\CoreBundle\Controller\ProductVariantController as BaseProductVariantController;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ProductVariantController extends BaseProductVariantController
{
    private const BULK_UPDATE_PRODUCT_VARIANT_VISIBILITY = 'bulk_update_product_variant_visibility';

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function bulkVariantVisibilityAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, self::BULK_UPDATE_PRODUCT_VARIANT_VISIBILITY);

        // No variants checked
        $variants = $this->resourcesCollectionProvider->get($configuration, $this->repository);
        if (empty($variants)) {
            $this->addFlash('info', 'sylius.product_variant.bulk_update_visibility.no_variants');

            return $this->redirectHandler->redirectToReferer($configuration);
        }

        if (
            $configuration->isCsrfProtectionEnabled() &&
            !$this->isCsrfTokenValid('bulk_update_visibility', $request->request->get('_csrf_token'))
        ) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        $this->eventDispatcher->dispatchMultiple(self::BULK_UPDATE_PRODUCT_VARIANT_VISIBILITY, $configuration, $variants);

        $batchSize = 20;
        $i = 0;
        foreach ($variants as $variant) {
            $event = $this->eventDispatcher->dispatchPreEvent(self::BULK_UPDATE_PRODUCT_VARIANT_VISIBILITY, $configuration, $variant);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $this->redirectHandler->redirectToIndex($configuration, $variant);
            }

            try {
                $this->updateVariantVisibility($variant, $request);
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
            $postEvent = $this->eventDispatcher->dispatchPostEvent(self::BULK_UPDATE_PRODUCT_VARIANT_VISIBILITY, $configuration, $variant);
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
     * Update given variants visibilities
     *
     * @param  ProductVariantInterface $productVariant
     * @param  Request $request
     */
    private function updateVariantVisibility(ProductVariantInterface $productVariant, Request $request): void
    {
        $visible = $request->request->get('visible') === '1' ? true : false;

        if ($visible) {
            $productVariant->enable();
        } else {
            $productVariant->disable();
        }
    }

    /**
     * @param  Request $request
     */
    private function resolveBulkVisibilityFlashMessage(Request $request): void
    {
        $visible = $request->request->get('visible') === '1' ? true : false;

        if ($visible) {
            $this->addFlash('success', 'sylius.product_variant.bulk_update_visibility.toggle_visible');
        } else {
            $this->addFlash('success', 'sylius.product_variant.bulk_update_visibility.toggle_invisible');
        }
    }
}
