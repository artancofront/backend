<?php

namespace App\Exceptions;

use App\Exceptions\Cart\StockUnavailableException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $e)
    {
        // Handle validation errors
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }

        // Handle authentication errors
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please log in.'
            ], 401);
        }

        // Handle StockUnavailableException errors
        if ($e instanceof StockUnavailableException) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock for product.'
            ], 422);
        }

        // Handle authorization errors
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to perform this action.'
            ], 403);
        }

        // Handle method not allowed errors
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Method Not Allowed. This HTTP method is not supported for this route.'
            ], 405);
        }

        // Handle route not found errors
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Route Not Found. The requested URL was not found on the server.'
            ], 404);
        }

        // Handle model not found errors
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found. The requested resource could not be found in the database.'
            ], 404);
        }

        // Handle file-related errors (catch any generic file-not-found exceptions)
        if ($e instanceof FileNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'File not found. The requested file does not exist on the server.'
            ], 404);
        }

        // Handle file upload size errors
        if ($e instanceof PostTooLargeException) {
            return response()->json([
                'success' => false,
                'message' => 'Request Entity Too Large. The uploaded file exceeds the allowed size.'
            ], 413);
        }

        // Handle unexpected errors
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'trace' => config('app.debug') ? $e->getTrace() : null,
            ], 500);
        }

        return parent::render($request, $e);
    }

}
