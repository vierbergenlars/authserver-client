<?php

namespace vierbergenlars\Authserver\Client\Repository;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use vierbergenlars\Authserver\Client\Model\AbstractResultSet;
use vierbergenlars\Authserver\Client\NonUniqueResultException;

abstract class AbstractRepository
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var int
     */
    protected $limit;

    /**
     * AbstractRepository constructor.
     * @param ClientInterface $client
     * @param int $limit
     */
    public function __construct(ClientInterface $client, $limit)
    {
        $this->client = $client;
        $this->limit = $limit;
    }


    public function findAll()
    {
        return $this->findBy([]);
    }

    /**
     * @param array $properties
     * @return AbstractResultSet
     */
    public function findBy(array $properties)
    {
        $params = [];
        if($properties) {
            $params['query'] = [
                'q' => $properties
            ];
        }
        $params['per_page'] = $this->limit;
        $uri = \GuzzleHttp\uri_template($this->getUriTemplate(), $params);
        $uri = $this->createUri($uri);
        return $this->createSet($uri);
    }

    /**
     * @param array $properties
     * @return object|null
     */
    public function findOneBy(array $properties)
    {
        $set = $this->findBy($properties);
        switch($set->count()) {
            case 0:
                return null;
            case 1:
                return $set[0];
            default:
                throw new NonUniqueResultException();
        }
    }

    /**
     * @param string|Uri $uri
     * @return Uri
     */
    private function createUri($uri)
    {
        $baseUri = \GuzzleHttp\Psr7\uri_for($this->client->getConfig('base_uri'));
        return Uri::resolve($baseUri, $uri);
    }

    /**
     * @param mixed $id
     * @return object
     */
    public function find($id)
    {
        $url = \GuzzleHttp\uri_template($this->getUriTemplate(), ['id' => $id]);
        $data = json_decode($this->client->request('GET', $this->createUri($url)), true);
        return $this->createObject($data);
    }

    /**
     * @param UriInterface $uri
     * @return AbstractResultSet
     */
    abstract protected function createSet(UriInterface $uri);

    /**
     * @param array $item
     * @return object
     */
    abstract protected function createObject(array $item);

    /**
     * @return string
     */
    abstract protected function getUriTemplate();
}
