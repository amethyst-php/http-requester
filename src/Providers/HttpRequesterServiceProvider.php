<?php

namespace Amethyst\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Amethyst\Api\Support\Router;
use Amethyst\Common\CommonServiceProvider;

class HttpRequesterServiceProvider extends CommonServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        parent::register();
        $this->loadExtraRoutes();

        $this->app->register(\Amethyst\Providers\DataBuilderServiceProvider::class);
        $this->app->register(\Amethyst\Providers\HttpLogServiceProvider::class);
        $this->app->register(\Railken\Template\TemplateServiceProvider::class);
    }

    /**
     * Load extras routes.
     */
    public function loadExtraRoutes()
    {
        $config = Config::get('amethyst.http-requester.http.admin.http-requester');

        if (Arr::get($config, 'enabled')) {
            Router::group('admin', Arr::get($config, 'router'), function ($router) use ($config) {
                $controller = Arr::get($config, 'controller');

                $router->post('/render', ['as' => 'render', 'uses' => $controller.'@render']);
                $router->post('/{id}/execute', ['as' => 'execute', 'uses' => $controller.'@execute'])->where(['id' => '[0-9]+']);
            });
        }
    }
}
