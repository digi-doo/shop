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

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class RobotsTxtController
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param EngineInterface $templatingEngine
     */
    public function __construct(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * @return Response
     */
    public function robotsTxtAction(): Response
    {
        $content = $this->templatingEngine->render('@SyliusShop/robots.txt.twig');

        $response = new Response($content, 200);
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}
