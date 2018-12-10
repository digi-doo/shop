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

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Symfony\Component\Templating\Helper\Helper;

class FormatMoneyRoundedHelper extends Helper
{
    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @param MoneyFormatterInterface $moneyFormatter
     */
    public function __construct(MoneyFormatterInterface $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmountRounded(int $amount, string $currencyCode, string $localeCode): string
    {
        return $this->moneyFormatter->formatRounded($amount, $currencyCode, $localeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_format_money_rounded';
    }
}
