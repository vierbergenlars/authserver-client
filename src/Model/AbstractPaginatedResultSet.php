<?php

namespace vierbergenlars\Authserver\Client\Model;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

abstract class AbstractPaginatedResultSet extends AbstractResultSet
{
    /**
     * @var ClientInterface
     */
    protected $client;

    private $request;

    /**
     * AbstractPaginatedResultSet constructor.
     * @param ClientInterface $client
     * @param string|Uri|RequestInterface|array $request
     */
    public function __construct(ClientInterface $client, $request = null)
    {
        $this->client = $client;
        if(is_array($request)) {
            parent::__construct($request);
        } elseif($request) {
            if (!$request instanceof RequestInterface) {
                $request = $this->createRequest($request);
            }
            $this->request = $request;
        }
    }

    protected function loadCount()
    {
        return $this->loadNext();
    }

    private function loadNext()
    {
        if($this->request) {
            $data = json_decode($this->client->send($this->request)->getBody(), true);
            if(isset($data['_links'])&&isset($data['_links']['next'])&&isset($data['_links']['next']['href'])) {
                $this->request = $this->createRequest($data['_links']['next']['href']);
            }
            if(isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $this->items[] = $item;
                }
            }
            return $data['total'];
        }
        return count($this->items);
    }

    private function createRequest($uri)
    {
        $baseUri = \GuzzleHttp\Psr7\uri_for($this->client->getConfig('base_uri'));
        $uri = Uri::resolve($baseUri, $uri);
        return new Request('GET', $uri);
    }

    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset))
            throw new \OutOfBoundsException('The offset '.$offset.' does not exist.');
        while(!isset($this->items[$offset])) {
            $this->loadNext();
        }
        return parent::offsetGet($offset);
    }

}

