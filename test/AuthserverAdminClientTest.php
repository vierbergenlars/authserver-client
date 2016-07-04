<?php

namespace vierbergenlars\Authserver\ClientTest;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use vierbergenlars\Authserver\Client\AuthserverAdminClient;
use vierbergenlars\Authserver\Client\Model\Group;
use vierbergenlars\Authserver\Client\Model\User;
use vierbergenlars\Authserver\Client\Model\UserSet;
use vierbergenlars\Authserver\Client\Repository\GroupRepository;
use vierbergenlars\Authserver\Client\Repository\UserRepository;

class AuthserverAdminClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MockHandler
     */
    private $guzzleMock;

    private $guzzle;
    /**
     * @var AuthserverAdminClient
     */
    private $client;

    public function setUp()
    {
        $this->guzzleMock = new MockHandler();
        $this->guzzle = new Client([
            'handler'=>HandlerStack::create($this->guzzleMock),
            'base_uri' => 'https://idp.vbgn.be',
            'debug' => true,
        ]);
        $this->client = new AuthserverAdminClient($this->guzzle);
    }
    public function testGetRepository()
    {
        $this->assertInstanceOf(UserRepository::class, $this->client->getRepository(User::class));
        $this->assertInstanceOf(GroupRepository::class, $this->client->getRepository(Group::class));
    }

    public function testGetUsers()
    {
        $users = $this->client->getUsers();
        $this->assertInstanceOf(UserSet::class, $users);
    }
}
