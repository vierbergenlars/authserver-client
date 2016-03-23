<?php

namespace vierbergenlars\Authserver\Client\Repository;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\UriInterface;
use vierbergenlars\Authserver\Client\Model\User;
use vierbergenlars\Authserver\Client\Model\UserSet;

class UserRepository extends AbstractRepository
{
    protected function createSet(UriInterface $uri)
    {
        return new UserSet($this->client, $uri);
    }

    protected function createObject(array $item)
    {
        return User::fromApiData($this->client, $item);
    }

    /**
     * @return string
     */
    protected function getUriTemplate()
    {
        return 'admin/users{/id}{?query*,per_page}';
    }
}
