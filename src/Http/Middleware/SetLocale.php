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

namespace DMX\Application\Intl\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DMX\Application\Intl\LocaleManager;

class SetLocale
{
    /**
     * @var LocaleManager
     */
    private $localeManager;

    /**
     * SetLocale constructor.
     *
     * @param LocaleManager $manager
     * @codeCoverageIgnore
     */
    public function __construct(LocaleManager $manager)
    {
        $this->localeManager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     *
     * @return mixed
     * @codeCoverageIgnore
     */
    public function handle(Request $request, Closure $next)
    {
        $this->localeManager->setLocaleAndPutIntoSession($this->localeManager->getCurrentLocale());

        return $next($request);
    }
}
