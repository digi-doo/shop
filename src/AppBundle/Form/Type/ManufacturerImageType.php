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

namespace AppBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ImageType as BaseImageType;

final class ManufacturerImageType extends BaseImageType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_manufacturer_image';
    }
}
