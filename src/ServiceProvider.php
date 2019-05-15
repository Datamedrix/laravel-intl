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

use Illuminate\Contracts\Session\Session as SessionContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     * {@inheritdoc}
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function register()
    {
        $this->app->singleton(LocaleManager::class, function (ApplicationContract $app) {
            return new LocaleManager(
                $app,
                $app->make(SessionContract::class),
                $app->make(ConfigContract::class)
            );
        });

        $this->app->singleton(Leo::class, function (ApplicationContract $app) {
            return new Leo(
                $app->make(LocaleManager::class)
            );
        });
    }
}
