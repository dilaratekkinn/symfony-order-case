<?php


namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{
    /**
     * @param $response
     * @param $status
     * @return JsonResponse
     */
    private static function json($response, $status=null): JsonResponse
    {
        if (!$status){
            return new JsonResponse($response);
        }
        return new JsonResponse($response, $status);
    }

    /**
     * @param bool $success
     * @param string $message
     * @param $data
     * @return JsonResponse
     */
    public static function message(bool $success, string $message, $data=null):JsonResponse
    {
        $response = [
            'message'=>$message
        ];
        if($data){
            $response['data']=$data;
        }
        return ApiResponse::json($response);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public static function data($data):JsonResponse
    {
        return ApiResponse::json($data);
    }

    /**
     * @param int $status
     * @param string $message
     * @param array|null $errors
     * @return JsonResponse
     */
    public static function exception(int $status, string $message, array $errors=null):JsonResponse
    {
        $response=[
            'message'=>$message,
        ];
        if($errors){
            $response['errors']=$errors;
        }
        return ApiResponse::json($response, $status);
    }
}