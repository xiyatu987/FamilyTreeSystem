<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * 不需要修剪的属性名称。
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];
}
