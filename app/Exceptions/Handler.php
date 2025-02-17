<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        // Sentry integration for error reporting
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        // Enhanced error reporting with context
        $this->reportable(function (Throwable $e) {
            $this->logExceptionWithContext($e);
        });

        // 404 Not Found Handler
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            $this->logSecurityEvent('404_not_found', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer')
            ]);

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found',
                    'code' => 404
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // Model Not Found Handler
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            $this->logSecurityEvent('model_not_found', [
                'model' => $e->getModel(),
                'ids' => $e->getIds(),
                'url' => $request->fullUrl(),
                'user_id' => auth()->id()
            ]);

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found',
                    'code' => 404
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // Authentication Error Handler
        $this->renderable(function (AuthenticationException $e, Request $request) {
            $this->logSecurityEvent('authentication_failed', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'attempted_guards' => $e->guards()
            ]);

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Authentication required',
                    'code' => 401
                ], 401);
            }

            return redirect()->guest(route('login'))->with('error', 'Please login to access this page');
        });

        // Authorization Error Handler
        $this->renderable(function (AuthorizationException $e, Request $request) {
            $this->logSecurityEvent('authorization_failed', [
                'message' => $e->getMessage(),
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
            'user_role' => auth()->check() ? (auth()->user()->role?->value ?? 'unknown') : 'guest'
            ]);

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied',
                    'code' => 403
                ], 403);
            }

            return response()->view('errors.403', [], 403);
        });

        // Database Query Error Handler
        $this->renderable(function (QueryException $e, Request $request) {
            Log::error('Database query error', [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'url' => $request->fullUrl(),
                'user_id' => auth()->id()
            ]);

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => app()->environment('production') 
                        ? 'Database error occurred' 
                        : $e->getMessage(),
                    'code' => 500
                ], 500);
            }

            return response()->view('errors.500', [], 500);
        });

        // Method Not Allowed Handler
        $this->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            $this->logSecurityEvent('method_not_allowed', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'allowed_methods' => $e->getHeaders()['Allow'] ?? 'Unknown'
            ]);

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Method not allowed',
                    'allowed_methods' => explode(', ', $e->getHeaders()['Allow'] ?? ''),
                    'code' => 405
                ], 405);
            }

            return response()->view('errors.405', [], 405);
        });

        // Generic HTTP Exception Handler
        $this->renderable(function (HttpException $e, Request $request) {
            $statusCode = $e->getStatusCode();
            
            Log::warning('HTTP Exception occurred', [
                'status_code' => $statusCode,
                'message' => $e->getMessage(),
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
                'headers' => $e->getHeaders()
            ]);

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage() ?: 'Server Error',
                    'code' => $statusCode
                ], $statusCode);
            }

            $view = "errors.{$statusCode}";
            
            if (view()->exists($view)) {
                return response()->view($view, [], $statusCode);
            }

            return response()->view('errors.generic', [
                'code' => $statusCode,
                'message' => $e->getMessage()
            ], $statusCode);
        });
    }

    /**
     * Convert a validation exception into a JSON response.
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        Log::warning('Validation failed', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'errors' => $exception->errors(),
            'user_id' => auth()->id(),
            'ip' => $request->ip()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
            'code' => $exception->status
        ], $exception->status);
    }

    /**
     * Log exception with enhanced context
     */
    private function logExceptionWithContext(Throwable $exception): void
    {
        $request = request();
        
        Log::error('Exception occurred', [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $this->getSafeInput($request),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'user_role' => optional(auth()->user())->role?->value ?? 'guest',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'timestamp' => now(),
            'session_id' => session()->getId(),
            'environment' => app()->environment()
        ]);
    }

    /**
     * Log security-related events
     */
    private function logSecurityEvent(string $event, array $context = []): void
    {
        Log::warning('Security event: ' . $event, array_merge([
            'event_type' => $event,
            'timestamp' => now(),
            'session_id' => session()->getId(),
            'environment' => app()->environment()
        ], $context));
    }

    /**
     * Get safe input data (excluding sensitive fields)
     */
    private function getSafeInput(Request $request): array
    {
        $input = $request->all();
        
        // Remove sensitive data from logs
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'api_key',
            'secret',
            'private_key',
            'credit_card',
            'card_number',
            'cvv',
            'ssn'
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($input[$field])) {
                $input[$field] = '[REDACTED]';
            }
        }

        return $input;
    }
}
