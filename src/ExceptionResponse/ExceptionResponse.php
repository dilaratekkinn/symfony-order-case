<?php


namespace App\ExceptionResponse;


use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionResponse extends JsonResponse
{

    public function __construct($message = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($this->format($message), $status, $headers, $json);
    }

    /**
     * @param string $message
     * @return array
     */
    private function format(string $message): array
    {
        return [
            'message' => $message
        ];
    }
}