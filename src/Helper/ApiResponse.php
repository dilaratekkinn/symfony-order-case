<?php


namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{
    /**
     * @param array $data
     * @param string $message
     * @return JsonResponse
     */
    public static function create(string $message = '', array $data = []): JsonResponse
    {
        return ApiResponse::response($message ?? 'Başarıyla Kaydedildi', $data, 201);
    }

    /**
     * @param array $data
     * @param string $message
     * @return JsonResponse
     */
    public static function update(string $message = '', array $data = []): JsonResponse
    {
        return ApiResponse::response($message ?? 'Başarıyla Güncellendi', $data);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public static function remove(string $message = ''): JsonResponse
    {
        return ApiResponse::response($message ?? 'Başarıyla Silindi');
    }

    /**
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    public static function badRequest(string $message = '', array $data = []): JsonResponse
    {
        return ApiResponse::response($message ?? 'Başarısız istek', $data, 400);
    }

    /**
     * @param array $data
     * @param string $message
     * @return JsonResponse
     */
    public static function success(array $data, string $message = ''): JsonResponse
    {
        return ApiResponse::response($message ?? 'Başarıyla Gerçekleştirildi', $data);
    }

    /**
     * @param string $message
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    private static function response(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return new JsonResponse([
            'message' => $message,
            'data' => $data
        ], $status);
    }
}