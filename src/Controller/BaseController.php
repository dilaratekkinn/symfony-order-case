<?php

namespace App\Controller;

use App\Service\BaseService;
use JMS\Serializer\SerializerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    /** @var ContainerInterface */
    protected $container;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var mixed|null */
    protected $service = null;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->serializer = $this->container->get(SerializerInterface::class);
        $this->service = $this->container->get('service');
    }

    /**
     * @return string[]
     */
    public static function getSubscribedServices(): array
    {
        return [
            SerializerInterface::class => SerializerInterface::class,
            'service' => BaseService::class
        ];
    }
}
