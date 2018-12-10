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

namespace AppBundle\Event\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;

final class ImagesRemoveListener
{
    /** @var ImageUploaderInterface */
    private $imageUploader;

    /** @var CacheManager */
    private $cacheManager;

    /** @var FilterManager */
    private $filterManager;

    public function __construct(ImageUploaderInterface $imageUploader, CacheManager $cacheManager, FilterManager $filterManager)
    {
        $this->imageUploader = $imageUploader;
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();

        if ($entity instanceof ImagesAwareInterface) {
            foreach ($entity->getImages() as $image) {
                $this->imageUploader->remove($image->getPath());
                $this->cacheManager->remove(
                    $image->getPath(),
                    array_keys($this->filterManager->getFilterConfiguration()->all())
                );
            }
        }
    }
}
