<?php namespace Ariol\Admin;

use File;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Ariol\Commands\ControllerAriolCommand;
use Ariol\Admin\Middleware\OwnerMiddleware;

/**
 * Провайдер пакета админки.
 * @package Ariol\Admin
 */
class AdminServiceProvider extends ServiceProvider
{
    /**
     * Начальная загрузка приложения.
     *
     * @param Router $router
     */
    public function boot(Router $router)
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'ariol');
        $this->loadTranslationsFrom(__DIR__ . '/Resources/lang', 'ariol');

        $router->aliasMiddleware('role', OwnerMiddleware::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ControllerAriolCommand::class
            ]);
        }

        $this->publishes([
            __DIR__ . '/Config' => base_path() . '/config'
        ], 'ariol-config');

        $this->publishes([
            __DIR__ . '/Resources/lang' => base_path() . '/resources/lang'
        ], 'ariol-lang');

        $this->publishes([
            __DIR__ . '/Public' => public_path()
        ], 'ariol-public');

        $this->publishes([
            __DIR__ . '/Public' => public_path(),
            __DIR__ . '/Config' => base_path() . '/config',
            __DIR__ . '/Resources/lang' => base_path() . '/resources/lang'
        ], 'ariol');

        $indexContent = File::get(public_path() . '/index.php');
        if (! preg_match("/GlideImage/i", $indexContent)) {
            $content = str_replace(
                "require __DIR__.'/../bootstrap/autoload.php';",
                "require __DIR__ . '/../bootstrap/autoload.php';\nrequire __DIR__ . '/../packages/ariol/admin/src/Libs/GlideImage.php';",
                $indexContent
            );

            File::put(public_path() . '/index.php', $content);
        }
    }

    /**
     * Регистрация приложения.
     *
     * @return void
     */
    public function register() {}
}
