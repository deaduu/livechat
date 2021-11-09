<?php

namespace Deaduu\Livechat\Providers;

use Illuminate\Support\ServiceProvider;
use Deaduu\Livechat\ChatController;

class LiveChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'livechat');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            // Publish assets
            $this->publishes([
                __DIR__ . '/../../resources/assets' => public_path('livechat'),
            ], 'assets');
        }
    }
}
