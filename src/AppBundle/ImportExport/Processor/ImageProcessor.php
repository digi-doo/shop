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

namespace AppBundle\ImportExport\Processor;

use FriendsOfSylius\SyliusImportExportPlugin\Processor\ResourceProcessorInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Main image processor
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ImageProcessor implements ResourceProcessorInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * Set all product dependencies
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        /** @var ContainerInterface */
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $data): void
    {
        $imageUploader = $this->container->get('sylius.image_uploader');
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $filterManager = $this->container->get('liip_imagine.filter.manager');
        $productManager = $this->container->get('sylius.manager.product');

        $productCode = (string) $data['Product-code'];
        $mediaCode = array_key_exists('Import-id', $data) && $data['Import-id'] !== null ? (string) $data['Import-id'] : $productCode;

        if ($this->hasMediafolder($mediaCode) && $this->getProduct($productCode)) {
            /** @var ResourceInterface $product */
            $product = $this->getProduct($productCode);

            // Remove all product images before upload
            $images = $product->getImages();
            if (!$images->isEmpty()) {
                foreach ($images as $image) {
                    $product->removeImage($image);
                    $imageUploader->remove($image->getPath());
                    $cacheManager->remove(
                        $image->getPath(),
                        array_keys($filterManager->getFilterConfiguration()->all())
                    );
                }
            }

            // Add and upload new images from the media folder
            foreach ($this->getProductImages($mediaCode) as $image) {
                $product->addImage($image);
            }

            // Persist only existing product
            $productManager->persist($product);
        }
    }

    /**
     * Get product entity
     *
     * @param string $productCode
     *
     * @return ResourceInterface
     */
    private function getProduct(string $productCode): ?ResourceInterface
    {
        $productRepo = $this->container->get('sylius.repository.product');

        /** @var ResourceInterface $product */
        $product = $productRepo->findOneBy(['code' => $productCode]);

        return $product;
    }

    /**
     * Get product images from its media folder
     *
     * @param string $productCode
     */
    private function getProductImages(string $productCode): array
    {
        $imageFactory = $this->container->get('sylius.factory.product_image');
        $imageUploader = $this->container->get('sylius.image_uploader');

        $images = [];
        $imagesToUpload = glob($this->container->getParameter('kernel.root_dir') . '/../import/media/' . $productCode . '/*.{jpg,JPG,jpeg,JPEG,png,PNG}', GLOB_BRACE);

        foreach ($imagesToUpload as $imageToUpload) {
            $image = $imageFactory->createNew();
            $image->setFile(new UploadedFile($imageToUpload, $productCode));

            if (pathinfo($imageToUpload)['filename'] === $productCode || count($imagesToUpload) == 1) {
                $image->setType('main');
            } else {
                $image->setType('thumbnail');
            }

            $imageUploader->upload($image);
            $images[] = $image;
        }

        return $images;
    }

    /**
     * Check if media folder exists and its not empty
     *
     * @param string $code
     */
    private function hasMediafolder(string $code): bool
    {
        $fs = new Filesystem();

        if (!$fs->exists($this->container->getParameter('kernel.root_dir') . '/../import/media/' . $code)) {
            return false;
        }

        return true;
    }
}
