<?php

namespace vierbergenlars\Authserver\Client\Model;

class UserSet extends AbstractPaginatedResultSet
{
    protected function createObject(array $item)
    {
        return User::fromApiData($this->client, $item);
    }
}
