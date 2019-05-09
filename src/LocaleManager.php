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

use DMX\Application\Intl\Exceptions\InvalidLocaleException;
use Illuminate\Contracts\Session\Session as SessionContract;
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
     * LocaleManager constructor.
     *
     * @param ApplicationContract $app
     * @param SessionContract     $session
     */
    public function __construct(ApplicationContract $app, SessionContract $session)
    {
        $this->app = $app;
        $this->session = $session;
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
     * @return Locale
     */
    public function getCurrentLocale(): Locale
    {
        if ($this->locale === null) {
            if ($this->session->has(self::SESSION_KEY)) {
                $localString = $this->session->get(self::SESSION_KEY, setlocale(LC_CTYPE, 0));
            } else {
                $localString = setlocale(LC_CTYPE, 0);
            }

            $this->locale = Locale::createFromISO15897String($localString);
        }

        return $this->locale;
    }

    /**
     * @param Locale $locale
     *
     * @throws InvalidLocaleException if the given locale does not exist on the environment
     */
    public function setLocaleAnPutIntoSession(Locale $locale)
    {
        $this->setLocale($locale);
        $this->session->put(self::SESSION_KEY, $locale->toISO15897String());
    }
}
