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

use Illuminate\Support\Arr;
use DMX\Application\Intl\Helper\LocaleStringConverter;
use DMX\Application\Intl\Exceptions\InvalidLocaleException;
use Illuminate\Contracts\Session\Session as SessionContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class LocaleManager
{
    /**
     * @var string
     */
    public const SESSION_KEY = 'locale';

    /**
     * @var Locale|null
     */
    private $locale = null;

    /**
     * @var ApplicationContract
     */
    private $app;

    /**
     * @var SessionContract
     */
    private $session;

    /**
     * @var ConfigContract
     */
    private $config;

    /**
     * LocaleManager constructor.
     *
     * @param ApplicationContract $app
     * @param SessionContract     $session
     * @param ConfigContract      $config
     */
    public function __construct(ApplicationContract $app, SessionContract $session, ConfigContract $config)
    {
        $this->app = $app;
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * @param Locale $locale
     *
     * @throws InvalidLocaleException if the given locale does not exist on the environment
     */
    public function setLocale(Locale $locale)
    {
        $result = setlocale(LC_ALL, [
            $locale->toISO15897String(),
            $locale->toISO15897String(false, true),
            $locale->toISO15897String(true, true),
            $locale->toIETFLanguageTag(),
            $locale->language(),
        ]);

        if ($result === false) {
            // set the locale to back the system defaults
            setlocale(LC_ALL, null);

            $message =
                'Unable to set the designated locale! The locale functionality is not implemented on this environment'
                . ' or the specified locale does not exist. [' . $locale . ']';
            throw InvalidLocaleException::becauseLocaleDoesNotExist($message);
        }

        $this->app->setLocale($locale->language());
        $this->locale = $locale;
    }

    /**
     * @param string $localeString
     *
     * @return Locale
     */
    public function createLocale(string $localeString): Locale
    {
        $details = LocaleStringConverter::explodeISO15897String($localeString);
        $settings = $this->getLocaleSettingsFromConfig($details['language'], $details['territory']);
        foreach ($this->getLocaleSettingsFromSystem() as $setting => $value) {
            if (!isset($setting) || $settings[$setting] === null) {
                $settings[$setting] = $value;
            }
        }

        return Locale::createFromISO15897String($localeString, $settings);
    }

    /**
     * @return Locale
     */
    public function getCurrentLocale(): Locale
    {
        if ($this->locale === null) {
            $locale = null;
            if ($this->session->has(self::SESSION_KEY)) {
                $locale = $this->session->get(self::SESSION_KEY, null);
                if (!$locale instanceof Locale) {
                    $locale = null;
                }
            }

            if ($locale === null) {
                $locale = $this->createLocale(setlocale(LC_CTYPE, 0));
            }

            $this->locale = $locale;
        }

        return $this->locale;
    }

    /**
     * @return string
     */
    public function getCurrentLanguage(): string
    {
        return $this->getCurrentLocale()->language();
    }

    /**
     * @param Locale $locale
     *
     * @throws InvalidLocaleException if the given locale does not exist on the environment
     */
    public function setLocaleAndPutIntoSession(Locale $locale)
    {
        $this->setLocale($locale);
        $this->session->put(self::SESSION_KEY, $locale);
    }

    /**
     * @param string      $language
     * @param string|null $territory
     *
     * @return array
     */
    protected function getLocaleSettingsFromConfig(string $language, ?string $territory = null): array
    {
        $configKey = 'locale.settings.' . $language . (!empty($territory) ? '-' . $territory : '');
        if ($this->config->has($configKey)) {
            $settings = $this->config->get($configKey, null);
        } else {
            $configKey = 'locale.settings.' . $language;
            $settings = $this->config->get($configKey, null);
        }

        if (empty($settings)) {
            $settings = $this->config->get('locale.defaults.settings', null);
        }

        return array_merge(Locale::SETTINGS_TEMPLATE, Arr::wrap($settings));
    }

    /**
     * @return array
     */
    protected function getLocaleSettingsFromSystem(): array
    {
        return array_merge(
            Locale::SETTINGS_TEMPLATE,
            [
                'decimalPoint' => localeconv()['decimal_point'],
                'thousandsSeparator' => localeconv()['thousands_sep'],
                'positiveSign' => localeconv()['positive_sign'],
                'negativeSign' => localeconv()['negative_sign'],
            ]
        );
    }
}
