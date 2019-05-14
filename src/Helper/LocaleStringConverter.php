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

namespace DMX\Application\Intl\Helper;

use Illuminate\Support\Str;

class LocaleStringConverter
{
    /**
     * Creates an ISO/IEC 15897 formatted string based on the given information.
     *
     * Format: language[_territory][.codeset][@modifier]
     * Example(s):
     *  - en
     *  - en_GB
     *  - de_AT.UTF-8
     *
     * @param string      $language
     * @param string|null $territory
     * @param string|null $codeSet
     * @param string|null $modifier
     *
     * @return string
     */
    public static function createISO15897String(string $language, ?string $territory = null, ?string $codeSet = null, ?string $modifier = null): string
    {
        return Str::lower(trim($language))
            . (!empty($territory) ? '_' . Str::upper(trim($territory)) : '')
            . (!empty($codeSet) ? '.' . trim($codeSet) : '')
            . (!empty($modifier) ? '@' . trim($modifier) : '')
        ;
    }

    /**
     * Creates an IETF language tag based on the given information.
     *
     * Format: language[-territory]
     * Example(s):
     *  - en
     *  - en-GB
     *  - de-AT
     *
     * @param string      $language
     * @param string|null $territory
     *
     * @return string
     */
    public static function createIETFLanguageTag(string $language, ?string $territory = null)
    {
        return Str::lower(trim($language))
            . (!empty($territory) ? '-' . Str::upper(trim($territory)) : '')
        ;
    }

    /**
     * @param string $localeString
     *
     * @return array
     *
     * @throws \InvalidArgumentException if the given locale string is empty or invalid
     */
    public static function explodeISO15897String(string $localeString): array
    {
        $localeString = trim($localeString);
        if (empty($localeString)) {
            throw new \InvalidArgumentException('The given ISO 15897 string is empty or invalid.');
        }

        $language = $territory = $codeSet = $modifier = null;

        if (Str::contains($localeString, '@')) {
            list($localeString, $modifier) = array_pad(explode('@', $localeString, 2), 2, null);
        }

        if (Str::contains($localeString, '.')) {
            list($localeString, $codeSet) = array_pad(explode('.', $localeString, 2), 2, null);
        }

        list($language, $territory) = array_pad(explode('_', $localeString, 2), 2, null);

        return [
            'language' => Str::lower(trim($language)),
            'territory' => !empty($territory) ? Str::upper(trim($territory)) : null,
            'codeSet' => $codeSet,
            'modifier' => $modifier,
        ];
    }

    /**
     * @param string $tag
     *
     * @return array
     *
     * @throws \InvalidArgumentException if the given language tag is empty or invalid
     */
    public static function explodeIETFLanguageTag(string $tag): array
    {
        $tag = trim($tag);
        if (empty($tag)) {
            throw new \InvalidArgumentException('The given IETF language tag is empty or invalid.');
        }

        list($language, $territory) = array_pad(explode('-', $tag), 2, null);

        return [
            'language' => Str::lower(trim($language)),
            'territory' => !empty($territory) ? Str::upper(trim($territory)) : null,
            'codeSet' => null,
            'modifier' => null,
        ];
    }
}
