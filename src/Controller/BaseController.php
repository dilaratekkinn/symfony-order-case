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


    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->serializer = $this->container->get(SerializerInterface::class);
    }

    /**
     * @return string[]
     */
    public static function getSubscribedServices(): array
    {
        return [
            SerializerInterface::class => SerializerInterface::class,
        ];
    }
}
