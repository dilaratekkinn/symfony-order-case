<?php

namespace App\Service;

use App\Repository\BaseRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

abstract class BaseService implements ServiceSubscriberInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var mixed|null */
    public $repository = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->repository = $this->container->get('repository');
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        if (!$token = $this->getToken()) {
            return null;
        }
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return null;
        }
        return $user;
    }

    /**
     * @return TokenInterface|null
     */
    public function getToken(): ?TokenInterface
    {
        return $this->container->get('security.token_storage')->getToken();
    }



    /**
     * @return string[]
     */
    public static function getSubscribedServices(): array
    {
        return [
            'repository' => BaseRepository::class,
            'security.token_storage' => '?' . TokenStorageInterface::class,
        ];
    }
}