<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public function okay($data = [], $statusCode = 200)
    {
        return response()->json([
            'message' => 'Okay',
            'data' => $data,
        ], $statusCode);
    }

    public function error(int $statusCode, string $message, ?array $data = [])
    {
        $data = $statusCode === JsonResponse::HTTP_UNPROCESSABLE_ENTITY ? ['errors' => $data] : $data;
        return response()->json(array_merge([
            'code' => $statusCode,
            'message' => $message,
        ], $data), $statusCode);
    }
}
