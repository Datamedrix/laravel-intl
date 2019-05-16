<?php
/**
 * ----------------------------------------------------------------------------
 * This code is part of an application or library developed by Datamedrix and
 * is subject to the provisions of your License Agreement with
 * Datamedrix GmbH.
 *
 * @copyright (c) 2019 Datamedrix GmbH
 * ----------------------------------------------------------------------------
 * @author Christian Graf <c.graf@datamedrix.com>
 */

declare(strict_types=1);

if (function_exists('app')) {
    if (!function_exists('locale')) {
        /**
         * @return \DMX\Application\Intl\Locale
         */
        function locale(): \DMX\Application\Intl\Locale
        {
            return app(\DMX\Application\Intl\LocaleManager::class)->getCurrentLocale();
        }
    }

    if (!function_exists('current_language')) {
        /**
         * @return string
         */
        function current_language(): string
        {
            return app(\DMX\Application\Intl\LocaleManager::class)->getCurrentLanguage();
        }
    }

    if (!function_exists('format_date')) {
        /**
         * @param \Illuminate\Support\Carbon $carbon
         * @param string                     $as
         *
         * @return string
         */
        function format_date(\Illuminate\Support\Carbon $carbon, string $as = 'date'): string
        {
            return app(\DMX\Application\Intl\Leo::class)->formatDate($carbon, $as);
        }
    }

    if (!function_exists('format_time')) {
        /**
         * @param \Illuminate\Support\Carbon $carbon
         *
         * @return string
         */
        function format_time(\Illuminate\Support\Carbon $carbon): string
        {
            return app(\DMX\Application\Intl\Leo::class)->formatTime($carbon);
        }
    }

    if (!function_exists('format_number')) {
        /**
         * @param @param int|float $number
         *
         * @return string
         */
        function format_number($number): string
        {
            return app(\DMX\Application\Intl\Leo::class)->formatNumber($number);
        }
    }

    if (!function_exists('format_currency')) {
        /**
         * @param float $money
         *
         * @return string
         */
        function format_currency(float $money): string
        {
            return app(\DMX\Application\Intl\Leo::class)->formatCurrency($money);
        }
    }
}
