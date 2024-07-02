<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
                return response()->json([
                    'error' => 'Method dilarang',
                    'message' => 'Metode yang dipakai dilarang untuk endpoint ini'
                ], 405);
            });
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'error' => 'Method dilarang',
                'message' => 'Metode yang dipakai dilarang untuk endpoint ini'
            ], 405);
        }

        return parent::render($request, $e);
    }
}
