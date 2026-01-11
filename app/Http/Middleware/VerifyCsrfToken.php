<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * 不需要CSRF保护的URL路径。
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
