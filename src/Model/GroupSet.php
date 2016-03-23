<?php

namespace vierbergenlars\Authserver\Client\Model;

use GuzzleHttp\ClientInterface;

class GroupSet extends AbstractPaginatedResultSet
{
    protected function createObject(array $item)
    {
        return Group::fromApiData($this->client, $item);
    }
}