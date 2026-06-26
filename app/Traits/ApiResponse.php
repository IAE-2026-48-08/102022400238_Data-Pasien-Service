<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($data, $message = 'Data retrieved successfully', $code = 200, $meta = [])
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'meta' => array_merge([
                'service_name' => 'Service Data Pasien',
                'api_version' => 'v1'
            ], $meta)
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
