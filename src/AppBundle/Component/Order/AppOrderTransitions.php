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

namespace AppBundle\Component\Order;

final class AppOrderTransitions
{
    public const TRANSITION_PROCESS = 'process';
    public const TRANSITION_EXPEDITE = 'expedite';
    public const TRANSITION_ISSUE = 'issue';

    private function __construct()
    {
    }
}
