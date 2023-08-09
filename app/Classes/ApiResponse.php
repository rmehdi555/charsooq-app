<?php

namespace App\Classes;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function successResponse($data, $message = '', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => $statusCode,
            'errors' => '',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public function errorResponse($message = '', $statusCode = 422): JsonResponse
    {
        return response()->json([
            'status' => $statusCode,
            'errors' => '',
            'message' => $message,
        ], $statusCode);
    }
}
