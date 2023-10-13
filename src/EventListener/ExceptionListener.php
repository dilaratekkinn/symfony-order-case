<?php

namespace App\EventListener;

use App\Helper\ApiResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{


    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = ApiResponse::badRequest($exception->getMessage());
        $event->setResponse($response);
    }

}