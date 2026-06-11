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
        // GANTI '1234567890' DI BAWAH INI DENGAN NIM KAMU SENDIRI
        $expectedKey = '102022400238'; 
        $providedKey = $request->header('X-IAE-KEY');

        if (!$providedKey || $providedKey !== $expectedKey) {
            return $this->errorResponse('Unauthorized. Header X-IAE-KEY tidak valid atau tidak ditemukan.', 401);
        }

        return $next($request);
    }
}