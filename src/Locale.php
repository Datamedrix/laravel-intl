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
use DMX\Application\Intl\Helper\LocaleStringConverter;

class Locale implements \Serializable
{
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
