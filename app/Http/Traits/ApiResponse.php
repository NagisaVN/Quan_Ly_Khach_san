<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Thao tác thành công',
        int $statusCode = 200,
        ?array $pagination = null
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => $pagination,
            'errors' => null,
            'statusCode' => $statusCode,
        ], $statusCode);
    }

    protected function errorResponse(
        string $message = 'Có lỗi xảy ra',
        int $statusCode = 400,
        mixed $errors = null,
        mixed $data = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'pagination' => null,
            'errors' => $errors,
            'statusCode' => $statusCode,
        ], $statusCode);
    }
}
