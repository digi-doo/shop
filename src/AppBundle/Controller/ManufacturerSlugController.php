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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ManufacturerSlugController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function generateAction(Request $request): JsonResponse
    {
        $name = $request->query->get('name');

        return new JsonResponse([
            'slug' => $this->get('sylius.generator.slug')->generate($name),
        ]);
    }
}
