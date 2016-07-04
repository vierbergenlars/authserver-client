<?php

namespace vierbergenlars\Authserver\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Uri;
use vierbergenlars\Authserver\Client\Model\Group;
use vierbergenlars\Authserver\Client\Model\GroupSet;
use vierbergenlars\Authserver\Client\Model\User;
use vierbergenlars\Authserver\Client\Model\UserSet;
use vierbergenlars\Authserver\Client\Repository\AbstractRepository;
use vierbergenlars\Authserver\Client\Repository\GroupRepository;
use vierbergenlars\Authserver\Client\Repository\UserRepository;

class AuthserverAdminClient
{
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var int
     */
    private $perPage = 100;

    private $repositories = [];

    /**
     * AuthserverAdminClient constructor.
     * @param ClientInterface $client
     * @param int $limit
     */
    public function __construct(ClientInterface $client, $limit = 100)
    {
        $this->client = $client;
        $this->perPage = $limit;
    }

    /**
     * @param $class
     * @return AbstractRepository
     */
    public function getRepository($class)
    {
        static $repositories;
        if(!$repositories) {
            $repositories = [
                User::class => UserRepository::class,
                Group::class => GroupRepository::class,
            ];
        }

        if(!isset($repositories[$class]))
            throw new \LogicException('There is no repository for '.$class);
        if(!isset($this->repositories[$class]))
            $this->repositories[$class] = new $repositories[$class]($this->client, $this->perPage);
        return $this->repositories[$class];
    }

    /**
     * @param array $query
     * @return UserSet
     */
    public function getUsers(array $query = [])
    {
        return $this->getRepository(User::class)->findBy($query);
    }

    /**
     * @param array $query
     * @return GroupSet
     */
    public function getGroups(array $query = [])
    {
        return $this->getRepository(Group::class)->findBy($query);
    }
}
