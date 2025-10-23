<?php

namespace Numanrki\WhmcsLicense;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Numanrki\WhmcsLicense\Http\Middleware\VerifyWhmcsLicense;
use Numanrki\WhmcsLicense\Console\Commands\VerifyLicenseCommand;

class WhmcsLicenseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/whmcs-license.php', 'whmcs-license');

        $this->app->singleton(WhmcsLicenseManager::class, function ($app) {
            return new WhmcsLicenseManager($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        $this->publishes([
            __DIR__ . '/../config/whmcs-license.php' => config_path('whmcs-license.php'),
        ], 'whmcs-license-config');

        $router->aliasMiddleware('whmcs.verified', VerifyWhmcsLicense::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                VerifyLicenseCommand::class,
            ]);
        }

        if (config('whmcs-license.enable_route')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        }
    }
}