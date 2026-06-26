<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null;
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $errors = null;

            if ($e instanceof ValidationException) {
                $status = 422;
                $message = 'Validasi gagal.';
                $errors = $e->errors();
            } elseif ($e instanceof NotFoundHttpException) {
                $message = 'Endpoint tidak ditemukan.';
            } elseif ($e instanceof MethodNotAllowedHttpException) {
                $message = 'Method tidak diizinkan untuk endpoint ini.';
            } elseif ($status === 401) {
                $message = 'Unauthorized.';
            } elseif ($status >= 500) {
                $message = 'Terjadi kesalahan pada server.';
                $errors = config('app.debug') ? [
                    'exception' => class_basename($e),
                    'message' => $e->getMessage(),
                ] : null;
            } else {
                $message = $e->getMessage() ?: 'Request gagal.';
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'data' => null,
                'errors' => $errors,
            ], $status);
        });
    })->create();
