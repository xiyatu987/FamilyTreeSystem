<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * 不需要报告的异常类型。
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];
    
    /**
     * 不需要渲染为HTTP响应的异常类型。
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];
    
    /**
     * 报告或记录异常。
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }
    
    /**
     * 将异常渲染为HTTP响应。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|Illuminate\Http\RedirectResponse
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}