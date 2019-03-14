<?php namespace Nayjest\Grids;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * This method required for backward compatibility with Laravel 4.
     *
     * @deprecated
     * @return string
     */
    public function guessPackagePath()
    {
        return __DIR__;
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $pkgPath = dirname(__DIR__);
        $viewsPath = $pkgPath . '/resources/views';

        $this->loadViewsFrom($viewsPath, 'grids');
        $this->loadTranslationsFrom($pkgPath . '/resources/lang', 'grids');
        $this->publishes([
            $viewsPath => base_path('resources/views/vendor/grids'),
        ]);

        if (!class_exists('Grids')) {
            class_alias('\\Nayjest\\Grids\\Grids', '\\Grids');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
