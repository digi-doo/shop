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

namespace AppBundle\Helpers;

class StringFormatter
{
    /**
     * @param string $input
     *
     * @return string
     */
    public function formatToLowercaseWithoutSpaces(string $input): string
    {
        return mb_strtolower(str_replace([' ', '-'], '_', $input));
    }
}
