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
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use DMX\Application\Intl\Helper\LocaleStringConverter;

class Locale implements \Serializable
{
    /** @var string */
    public const DEFAULT_LANGUAGE = 'en';

    /** @var string */
    public const DEFAULT_TERRITORY = 'US';

    /** @var string */
    public const DEFAULT_CODE_SET = 'utf8';

    /** @var string */
    public const DEFAULT_LOCALE = self::DEFAULT_LANGUAGE . '_' . self::DEFAULT_TERRITORY . '.' . self::DEFAULT_CODE_SET;

    /**
     * @var array
     */
    public const SETTINGS_TEMPLATE = [
        'decimalPoint' => null,
        'thousandsSeparator' => null,
        'positiveSign' => null,
        'negativeSign' => null,
        'formatting' => [
            'date' => null,
            'datetime' => null,
            'timestamp' => null,
            'time' => null,
            'decimals' => null,
            'number' => null,
            'currency' => null,
        ],
    ];

    /**
     * @var string
     */
    private $language;

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
     * @var array
     */
    private $settings = self::SETTINGS_TEMPLATE;

    /**
     * @var array
     */
    private $formatting = [
        'date' => null,
        'datetime' => null,
        'timestamp' => null,
        'time' => null,
        'number' => null,
        'currency' => null,
    ];

    /**
     * @param string     $localeString
     * @param array|null $settings
     *
     * @return Locale
     *
     * @throws \InvalidArgumentException if the given locale string is empty or invalid
     */
    public static function createFromISO15897String(string $localeString, ?array $settings = null): Locale
    {
        $locale = LocaleStringConverter::explodeISO15897String($localeString);

        return new static(
            $locale['language'],
            $locale['territory'],
            $locale['codeSet'],
            $locale['modifier'],
            $settings
        );
    }

    /**
     * @param string     $tag
     * @param array|null $settings
     *
     * @return Locale
     *
     * @throws \InvalidArgumentException if the given language tag is empty or invalid
     */
    public static function createFromIETFLanguageTag(string $tag, ?array $settings = null): Locale
    {
        $locale = LocaleStringConverter::explodeIETFLanguageTag($tag);

        return new static(
            $locale['language'],
            $locale['territory'],
            $locale['codeSet'],
            $locale['modifier'],
            $settings
        );
    }

    /**
     * Locale constructor.
     *
     * @param string      $language
     * @param string|null $territory
     * @param string|null $codeSet
     * @param string|null $modifier
     * @param array|null  $settings
     */
    public function __construct(string $language, ?string $territory = null, ?string $codeSet = null, ?string $modifier = null, ?array $settings = null)
    {
        $this->language = Str::lower(trim($language));
        $this->territory = !empty($territory) ? Str::upper(trim($territory)) : null;
        $this->codeSet = !empty($codeSet) ? trim($codeSet) : null;
        $this->modifier = !empty($modifier) ? trim($modifier) : null;
        $this->settings = !empty($settings) ? array_merge(self::SETTINGS_TEMPLATE, $settings) : self::SETTINGS_TEMPLATE;
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
     * @return array
     */
    public function settings(): array
    {
        return $this->settings;
    }

    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function setting(string $key, $default = null)
    {
        return Arr::get($this->settings(), $key, $default) ?: $default;
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
        return LocaleStringConverter::createISO15897String(
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
        return LocaleStringConverter::createIETFLanguageTag($this->language(), $this->territory());
    }

    /**
     * @return string
     */
    public function dateFormat(): string
    {
        if ($this->formatting['date'] === null) {
            $this->formatting['date'] = $this->setting(
                'formatting.date',
                Factory::createIntlDateFormatter(
                    $this->toISO15897String(),
                    IntlDateFormatter::SHORT,
                    IntlDateFormatter::NONE
                )->getPattern()
            );
        }

        return $this->formatting['date'];
    }

    /**
     * @return string
     */
    public function datetimeFormat(): string
    {
        if ($this->formatting['datetime'] === null) {
            $this->formatting['datetime'] = $this->setting(
                'formatting.datetime',
                Factory::createIntlDateFormatter(
                    $this->toISO15897String(),
                    IntlDateFormatter::MEDIUM,
                    IntlDateFormatter::SHORT
                )->getPattern()
            );
        }

        return $this->formatting['datetime'];
    }

    /**
     * @return string
     */
    public function timestampFormat(): string
    {
        if ($this->formatting['timestamp'] === null) {
            $this->formatting['timestamp'] = $this->setting(
                'formatting.timestamp',
                Factory::createIntlDateFormatter(
                    $this->toISO15897String(),
                    IntlDateFormatter::FULL,
                    IntlDateFormatter::MEDIUM
                )->getPattern()
            );
        }

        return $this->formatting['timestamp'];
    }

    /**
     * @return string
     */
    public function timeFormat(): string
    {
        if ($this->formatting['time'] === null) {
            $this->formatting['time'] = $this->setting(
                'formatting.time',
                Factory::createIntlDateFormatter(
                    $this->toISO15897String(),
                    IntlDateFormatter::NONE,
                    IntlDateFormatter::MEDIUM
                )->getPattern()
            );
        }

        return $this->formatting['time'];
    }

    /**
     * @return string
     */
    public function numberFormat(): string
    {
        if ($this->formatting['number'] === null) {
            $this->formatting['number'] = $this->setting(
                'formatting.number',
                Factory::createIntlNumberFormatter($this->toISO15897String(), NumberFormatter::TYPE_INT32)->getPattern()
            );
        }

        return $this->formatting['number'];
    }

    /**
     * @return string
     */
    public function currencyFormat(): string
    {
        if ($this->formatting['currency'] === null) {
            $this->formatting['currency'] = $this->setting(
                'formatting.currency',
                Factory::createIntlNumberFormatter(
                    $this->toISO15897String(),
                    NumberFormatter::TYPE_CURRENCY
                )->getPattern()
            );
        }

        return $this->formatting['currency'];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toISO15897String();
    }

    /**
     * String representation of object.
     *
     * @see https://php.net/manual/en/serializable.serialize.php
     *
     * @return string the string representation of the object or null
     *
     * @since 5.1.0
     */
    public function serialize(): string
    {
        return serialize([
            'language' => $this->language,
            'territory' => $this->territory,
            'codeSet' => $this->codeSet,
            'modifier' => $this->modifier,
            'settings' => $this->settings,
        ]);
    }

    /**
     * Constructs the object.
     *
     * @see https://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized the string representation of the object
     *
     * @return void
     *
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        if (!empty($serialized)) {
            $data = unserialize($serialized);

            $this->language = $data['language'] ?? '?';
            $this->territory = $data['territory'] ?? null;
            $this->codeSet = $data['codeSet'] ?? null;
            $this->modifier = $data['modifier'] ?? null;
            $this->settings = $data['settings'] ?? self::SETTINGS_TEMPLATE;
        }
    }
}
