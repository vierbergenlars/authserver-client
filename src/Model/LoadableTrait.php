<?php

namespace vierbergenlars\Authserver\Client\Model;

use GuzzleHttp\ClientInterface;

trait LoadableTrait
{
    /**
     * @var ClientInterface
     */
    private $_client;
    private $_links;

    /**
     * @internal
     * @param ClientInterface $client
     * @param array $item
     * @return static
     */
    public static function fromApiData(ClientInterface $client, array $item)
    {
        $obj = new static();
        $obj->_client = $client;
        $obj->_links = $item['_links'];
        $obj->setFromApiData($item);
        return $obj;
    }

    private function setFromApiData(array $item)
    {
        $vars = get_object_vars($this);
        foreach($item as $k=>$v) {
            $k = str_replace('-', '_', $k);
            if(array_key_exists($k, $vars))
                $this->{$k} = $v;
        }
    }

    private function loadSelf()
    {
        if($this->_client&&$this->_links&&isset($this->_links['self'])&&isset($this->_links['self']['href'])) {
            $data = json_decode($this->_client->request('GET', $this->_links['self']['href'])->getBody(), true);
            $this->setFromApiData($data);
        }
    }

    private function getOrLoad($prop)
    {
        if($this->{$prop} === null)
            $this->loadSelf();
        return $this->{$prop};
    }
}
