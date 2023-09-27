<?php
namespace App\ApiResponse;

use Symfony\Component\HttpFoundation\JsonResponse;

class SuccessResponse implements ResponseInterface{


    private  $status;
    private $data;
    private  $needLogin;
    private  $code;
    private $messages;

    public function __construct()
    {
        $this->status = true;
        $this->data = new \stdClass();
        $this->needLogin = false;
        $this->code = 200;
        $this->messages = new \stdClass();
    }

    public function setStatus(bool $status = true)
    {
        $this->status = $status;

        return $this;
    }

    public function setData($data = [])
    {
        $this->data = $data;

        return $this;
    }

    public function setCode(int $code = 200)
    {
        $this->code = $code;

        return $this;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;

        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getData()
    {
        return $this->data;
    }


    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function send(): JsonResponse
    {
        return new JsonResponse([
            'status' => $this->getStatus(),
            'messages' => $this->getMessages(),
            'data' => $this->getData(),
        ], $this->getCode());
    }


}
