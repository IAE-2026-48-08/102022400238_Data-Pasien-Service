<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($data, $message = 'Data retrieved successfully', $code = 200)
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ];

        return response()->json($response, $code);
    }

    protected function errorResponse($message, $code, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ];

        return response()->json($response, $code);
    }
}
