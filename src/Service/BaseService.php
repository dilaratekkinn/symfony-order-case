<?php

namespace App\Service;

use App\Repository\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em = $em;
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

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
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
            'security.token_storage' => '?' . TokenStorageInterface::class,
        ];
    }
}