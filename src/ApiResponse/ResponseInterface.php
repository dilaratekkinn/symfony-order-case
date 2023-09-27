<?php
namespace App\ApiResponse;

interface ResponseInterface {

    public function setStatus(bool $status = true);
    public function setData(array $data = []);
    public function setCode(int $code = 200);
    public function setMessages(array $messages);

    public function send();

    public function getStatus();
    public function getData();
    public function getCode();
    public function getMessages();
}
