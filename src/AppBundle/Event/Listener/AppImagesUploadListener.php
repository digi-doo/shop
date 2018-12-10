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

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class AppImagesUploadListener
{
    /**
     * @var ImageUploaderInterface
     */
    private $uploader;

    /**
     * @param ImageUploaderInterface $uploader
     */
    public function __construct(ImageUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function uploadImage(ResourceControllerEvent $event): void
    {
        $entity = $event->getSubject();

        Assert::isInstanceOf($entity, ResourceInterface::class);

        $image = $entity->getImage();
        if (null !== $image && true === $image->hasFile()) {
            $this->uploader->upload($image);
        }
    }
}
