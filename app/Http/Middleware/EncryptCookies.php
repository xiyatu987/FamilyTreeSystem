<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * 需要加密的cookie名称数组。
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
