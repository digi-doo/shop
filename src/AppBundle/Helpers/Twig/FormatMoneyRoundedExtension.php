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

final class FormatMoneyRoundedExtension extends \Twig_Extension
{
    /**
     * @var FormatMoneyRoundedHelper
     */
    private $helper;

    /**
     * @param FormatMoneyRoundedHelper $helper
     */
    public function __construct(FormatMoneyRoundedHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('sylius_format_money_rounded', [$this->helper, 'formatAmountRounded']),
        ];
    }
}
