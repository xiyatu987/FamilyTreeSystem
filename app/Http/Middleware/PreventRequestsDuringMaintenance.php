<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * 允许在维护模式下访问的URL模式。
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
