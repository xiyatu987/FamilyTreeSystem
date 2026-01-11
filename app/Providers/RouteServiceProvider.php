<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * 应用的路由模型绑定、模式过滤器等。
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * 定义路由模型绑定和其他路由配置。
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            
        });
    }

    /**
     * 配置应用的速率限制。
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        //
    }
}
