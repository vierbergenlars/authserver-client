<?php

namespace vierbergenlars\Authserver\ClientTest\Model;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use vierbergenlars\Authserver\Client\AuthserverAdminClient;
use vierbergenlars\Authserver\Client\Model\User;
use vierbergenlars\Authserver\Client\Model\UserSet;

class UserSetTest extends \PHPUnit_Framework_TestCase
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

    public function testDataLoading()
    {
        $users = $this->client->getRepository(User::class)->findAll();
        $this->assertInstanceOf(UserSet::class, $users);

        $this->assertNull($this->guzzleMock->getLastRequest());

        $this->guzzleMock->append(new Response(200, ['Content-Type'=>'application/json'], file_get_contents(__DIR__.'/../Resources/http/admin/users@per_page=100&page=1.json')));

        $this->assertEquals(152, $users->count()); // Loads the first page
        $this->assertNotNull($this->guzzleMock->getLastRequest());
        $this->assertEquals('/admin/users', $this->guzzleMock->getLastRequest()->getUri()->getPath());
        $this->assertEquals('per_page=100', $this->guzzleMock->getLastRequest()->getUri()->getQuery());

        while($users->key() < 99) // Iterating over the first 100 users should not generate any further requests
            $users->next();

        $this->assertInstanceOf(User::class, $users->current()); // Load the 100th user

        $this->guzzleMock->append(new Response(200, ['Content-Type'=>'application/json'], file_get_contents(__DIR__.'/../Resources/http/admin/users@per_page=100&page=2.json')));

        $users->next();
        $this->assertInstanceOf(User::class, $users->current()); // Load the 101st user
        $this->assertNotNull($this->guzzleMock->getLastRequest());
        $this->assertEquals('/admin/users', $this->guzzleMock->getLastRequest()->getUri()->getPath());
        $this->assertEquals('per_page=100&page=2', $this->guzzleMock->getLastRequest()->getUri()->getQuery());

        $this->assertInstanceOf(User::class, $users->offsetGet(1)); // Should not cause a request

        $user = null;
        while($users->valid()) {// Go to the last user
            $user = $users->current();
            $users->next();
        }

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($users->offsetGet(151), $user);
    }

    public function testOutOfBoundsAccess()
    {
        $users = $this->client->getRepository(User::class)->findAll();
        $this->assertInstanceOf(UserSet::class, $users);
        $this->guzzleMock->append(new Response(200, ['Content-Type'=>'application/json'], file_get_contents(__DIR__.'/../Resources/http/admin/users@per_page=100&page=1.json')));

        $this->setExpectedException(\OutOfBoundsException::class);

        try {
            $users->offsetGet(152);
        } finally {
            $this->assertNotNull($this->guzzleMock->getLastRequest());
            $this->assertEquals('/admin/users', $this->guzzleMock->getLastRequest()->getUri()->getPath());
            $this->assertEquals('per_page=100', $this->guzzleMock->getLastRequest()->getUri()->getQuery());
        }
    }

    public function testRewind()
    {
        $users = $this->client->getRepository(User::class)->findAll();
        $this->assertInstanceOf(UserSet::class, $users);
        $this->guzzleMock->append(new Response(200, ['Content-Type'=>'application/json'], file_get_contents(__DIR__.'/../Resources/http/admin/users@per_page=100&page=1.json')));

        $originalUser = $users->current();
        $users->next();
        $this->assertNotSame($originalUser, $users->current());
        $users->rewind();
        $this->assertSame($originalUser, $users->current());
    }
}
