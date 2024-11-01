<?php

namespace Svr\Raw\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiValidationErrors
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
            if ($response->exception instanceof ValidationException) {
                $errors = $response->exception->errors();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Ошибка валидации',
                    'errors' => $errors,
                ], 422);
            }
            return $response;
    }
}
