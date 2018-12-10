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

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TaxonNotFoundException extends NotFoundHttpException
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('Taxon has not been found!');
    }
}
