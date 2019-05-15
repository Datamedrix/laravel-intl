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

namespace DMX\Application\Intl;

use NumberFormatter;
use IntlDateFormatter;

class Factory
{
    /**
     * @param string $localeString
     * @param int    $dateType
     * @param int    $timeType
     * @param string $timezone
     *
     * @return IntlDateFormatter
     */
    public static function createIntlDateFormatter(string $localeString, int $dateType = IntlDateFormatter::FULL, int $timeType = IntlDateFormatter::FULL, string $timezone = 'UTC'): IntlDateFormatter
    {
        return new IntlDateFormatter(
            $localeString,
            $dateType,
            $timeType,
            $timezone,
            IntlDateFormatter::GREGORIAN
        );
    }

    /**
     * @param string $localeString
     * @param int    $style
     *
     * @return NumberFormatter
     */
    public static function createIntlNumberFormatter(string $localeString, int $style = NumberFormatter::TYPE_INT32): NumberFormatter
    {
        return new NumberFormatter(
            $localeString,
            $style
        );
    }
}
