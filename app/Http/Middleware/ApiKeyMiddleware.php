<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    use ApiResponse; // Memanggil trait standar respon

    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = env('IAE_API_KEY', '102022400238');
        $providedKey = $request->header('X-IAE-KEY') ?? $request->header('X-IAE-KKEY');

        if (!$providedKey) {
            return $this->errorResponse('Unauthorized. Header X-IAE-KEY tidak ditemukan.', 401);
        }

        if ($providedKey !== $expectedKey) {
            return $this->errorResponse('Forbidden. Header X-IAE-KEY tidak valid.', 403);
        }

        return $next($request);
    }
}
