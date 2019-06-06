<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
        // 405
        if ($exception instanceof MethodNotAllowedHttpException) {
            if ($request->expectsJson()) {
                return response()->json([
                    "message" => "Method not allowed"
                ], 405);
            }
            return response()->view("errors.404");
        }

        // 403
        if ($exception instanceof AuthorizationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    "message" => "Access forbidden"
                ], 403);
            }
            return parent::render($request, $exception);
        }

        // 401
        if ($exception instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    "message" => "Authentication needed"
                ], 401);
            }
            return parent::render($request, $exception);
        }

        // 404
        if ($exception instanceof NotFoundHttpException) {
            if ($request->expectsJson()) {
                return response()->json([
                    "message" => "Not found"
                ], 404);
            }
            return parent::render($request, $exception);
        }

        if (config('app.debug') == false && !$request->expectsJson()) {
            return response()->view("errors.500");
        }

        return parent::render($request, $exception);
    }
}
