<?php

namespace Hora\LaravelMomoWallet;

use Illuminate\Support\ServiceProvider;

class LaravelMomoWalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Hora\LaravelMomoWallet\Models\AIORequest');
        //$this->mergeConfigFrom(__DIR__ . '/config/laravel-momo.php', 'laravel-momo');
        $this->mergeConfig();
        $this->publishConfig();
        //$this->publishes([__DIR__ . '/config' => config_path()]);
        //$this->app->make('Hora\LaravelMomoWallet\Models\AIORequest');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function publishConfig()
    {
        $path = $this->getConfigPath();
        $this->publishes([$path => config_path('laravel-momo.php')], 'config');
    }

    private function getConfigPath()
    {
        return __DIR__ . '/config/laravel-momo.php';
    }

    private function mergeConfig()
    {
        $path = $this->getConfigPath();
        $this->mergeConfigFrom($path, 'laravel-momo');
    }
}
