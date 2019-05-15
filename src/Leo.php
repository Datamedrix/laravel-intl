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
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class Leo
{
    public const DEFAULT_INT_CURRENCY_NOTATION = 'EUR';
    public const DEFAULT_CURRENCY_SYMBOL = '€';
    public const DEFAULT_LANGUAGE = 'en';
    public const DEFAULT_TERRITORY = 'GB';
    public const DEFAULT_CODE_SET = 'UTF-8';
    public const DEFAULT_LOCALE = self::DEFAULT_LANGUAGE . '_' . self::DEFAULT_TERRITORY;

    /**
     * @var LocaleManager
     */
    protected $localeManager;

    /**
     * Leo constructor.
     *
     * @param LocaleManager $manager
     */
    public function __construct(LocaleManager $manager)
    {
        $this->localeManager = $manager;
    }

    /**
     * Simple wrapper for DMX\Application\Intl\LocaleManager::getCurrentLocale().
     *
     * @return Locale
     * @codeCoverageIgnore
     */
    public function getCurrentLocale(): Locale
    {
        return $this->localeManager->getCurrentLocale();
    }

    /**
     * Simple wrapper for DMX\Application\Intl\LocaleManager::getCurrentLocale().
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getCurrentLanguage(): string
    {
        return $this->localeManager->getCurrentLanguage();
    }

    /**
     * @param Carbon $carbon
     * @param string $as
     *
     * @return string
     */
    public function formatDate(Carbon $carbon, string $as = 'date'): string
    {
        switch (Str::lower($as)) {
            case 'datetime':
                $format = $this->getCurrentLocale()->datetimeFormat();
                break;
            case 'timestamp':
                $format = $this->getCurrentLocale()->timestampFormat();
                break;
            case 'date':
            default:
                $format = $this->getCurrentLocale()->dateFormat();
                break;
        }

        return IntlDateFormatter::formatObject(
            $carbon,
            $format,
            $this->getCurrentLocale()->toISO15897String()
        );
    }

    /**
     * @param Carbon $carbon
     *
     * @return string
     */
    public function formatTime(Carbon $carbon): string
    {
        return IntlDateFormatter::formatObject(
            $carbon,
            $this->getCurrentLocale()->timeFormat(),
            $this->getCurrentLocale()->toISO15897String()
        );
    }

    /**
     * @param int|float $number
     *
     * @return string
     */
    public function formatNumber($number): string
    {
        $locale = $this->getCurrentLocale();
        if (is_integer($number)) {
            $type = NumberFormatter::TYPE_INT32;
            $number = (int) $number;
        } else {
            $type = NumberFormatter::TYPE_DEFAULT;
            $number = (float) $number;
        }

        $formatter = new NumberFormatter($locale->toISO15897String(), $type, $locale->numberFormat());

        return $formatter->format($number, $type);
    }

    /**
     * @param float $money
     *
     * @return string
     */
    public function formatCurrency(float $money): string
    {
        $locale = $this->getCurrentLocale();
        $formatter = new NumberFormatter($locale->toISO15897String(), NumberFormatter::CURRENCY);

        return $formatter->formatCurrency(
            $money,
            $locale->setting('intCurrencyNotation', self::DEFAULT_INT_CURRENCY_NOTATION)
        );
    }
}
