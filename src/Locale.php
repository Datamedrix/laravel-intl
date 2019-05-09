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

use Illuminate\Support\Str;

class Locale
{
    public const DEFAULT_LANGUAGE = 'en';

    /**
     * @var string
     */
    private $language = self::DEFAULT_LANGUAGE;

    /**
     * @var string|null
     */
    private $territory = null;

    /**
     * @var string|null
     */
    private $codeSet = null;

    /**
     * @var string|null
     */
    private $modifier = null;

    /**
     * Locale constructor.
     *
     * @param string      $language
     * @param string|null $territory
     * @param string|null $codeSet
     * @param string|null $modifier
     */
    public function __construct(string $language, ?string $territory = null, ?string $codeSet = null, ?string $modifier = null)
    {
        $this->language = Str::lower(trim($language));
        $this->territory = !empty($territory) ? Str::upper(trim($territory)) : null;
        $this->codeSet = !empty($codeSet) ? trim($codeSet) : null;
        $this->modifier = !empty($modifier) ? trim($modifier) : null;
    }

    /**
     * @return string
     */
    public function language(): string
    {
        return $this->language;
    }

    /**
     * @return string|null
     */
    public function territory(): ?string
    {
        return $this->territory;
    }

    /**
     * @return string|null
     */
    public function codeSet(): ?string
    {
        return $this->codeSet;
    }

    /**
     * @return string|null
     */
    public function modifier(): ?string
    {
        return $this->modifier;
    }

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
     * Get the ISO/IEC 15897 formatted string of the locale.
     *
     * @param bool $excludeCodeSet
     * @param bool $excludeModifier
     *
     * @return string
     *
     * @see Locale::createISO15897String()
     */
    public function toISO15897String(bool $excludeCodeSet = false, bool $excludeModifier = false): string
    {
        return self::createISO15897String(
            $this->language(),
            $this->territory(),
            $excludeCodeSet === false ? $this->codeSet() : null,
            $excludeModifier === false ? $this->modifier() : null
        );
    }

    /**
     * Get the IETF language tag of the locale.
     *
     * @see Locale::createIETFLanguageTag()
     *
     * @return string
     */
    public function toIETFLanguageTag(): string
    {
        return self::createIETFLanguageTag($this->language(), $this->territory());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toISO15897String();
    }
}
