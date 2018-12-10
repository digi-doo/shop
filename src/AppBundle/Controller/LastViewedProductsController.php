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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 * USAGE in twig template:
 * {{ render(controller('AppBundle\\Controller\\LastViewedProductsController::viewedAction')) }}
 */
class LastViewedProductsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function viewedAction(Request $request)
    {
        $products = $this->container->get('app.last_viewed_products_helper')->getProducts($request->get('_locale'));

        return $this->render('SyliusShopBundle:Product:_lastViewed.html.twig', ['products' => $products]);
    }
}
