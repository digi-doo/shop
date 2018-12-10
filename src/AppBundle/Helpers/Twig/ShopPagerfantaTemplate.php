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

namespace AppBundle\Helpers\Twig;

use Pagerfanta\View\Template\DefaultTemplate;

class ShopPagerfantaTemplate extends DefaultTemplate
{
    protected static $defaultOptions = [
        'previous_message' => '<i class="fas fa-angle-left"></i>',
        'next_message' => '<i class="fas fa-angle-right"></i>',
        'css_disabled_class' => 'disabled',
        'css_dots_class' => 'dots',
        'css_current_class' => 'current',
        'dots_text' => '...',
        'container_template' => '<ul>%pages%</ul>',
        'page_template' => '<li><a href="%href%"%rel%>%text%</a></li>',
        'span_template' => '<li class="%class%"><a>%text%</a></li>',
        'rel_previous' => 'prev',
        'rel_next' => 'next',
    ];
}
