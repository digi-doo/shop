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

namespace AppBundle\Helpers\Money;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Webmozart\Assert\Assert;

/**
 * Money formatter with hardcoded rounding for CZK currency
 * due to php native \NumberFormatter class bug.
 */
final class MoneyFormatter implements MoneyFormatterInterface
{
    /**
     * Format money based on currency and locale
     *
     * @param  int $amount   money amount in pennies
     * @param  string $currency
     * @param  string $locale
     *
     * @return string
     */
    public function format(int $amount, string $currencyCode, ?string $locale = 'en'): string
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

        // Hardcoded rounding just for CZK currency
        // if ($currencyCode === 'CZK') {
        //     $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
        //     $formatter->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_HALFUP);
        // }

        $result = $formatter->formatCurrency(abs($amount / 100), $currencyCode);
        Assert::notSame(
            false,
            $result,
            sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currencyCode)
        );

        return $amount >= 0 ? $result : '-' . $result;
    }

    /**
     * Format money based on currency and locale and round it to 0 decimals
     *
     * @param  int $amount   money amount in pennies
     * @param  string $currency
     * @param  string $locale
     *
     * @return string
     */
    public function formatRounded(int $amount, string $currencyCode, ?string $locale = 'en'): string
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

        // Hardcoded rounding just for CZK currency
        if ($currencyCode === 'CZK') {
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
            $formatter->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_HALFUP);
        }

        $result = $formatter->formatCurrency(abs($amount / 100), $currencyCode);
        Assert::notSame(
            false,
            $result,
            sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currencyCode)
        );

        return $amount >= 0 ? $result : '-' . $result;
    }
}
