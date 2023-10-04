<?php


namespace App\ExceptionResponse;


use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionResponse extends JsonResponse
{

    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
    }
}