<?php

namespace vierbergenlars\Authserver\Client\Repository;

use Psr\Http\Message\UriInterface;
use vierbergenlars\Authserver\Client\Model\AbstractResultSet;
use vierbergenlars\Authserver\Client\Model\Group;
use vierbergenlars\Authserver\Client\Model\GroupSet;

class GroupRepository extends AbstractRepository
{
    /**
     * @param UriInterface $uri
     * @return AbstractResultSet
     */
    protected function createSet(UriInterface $uri)
    {
        return new GroupSet($this->client, $uri);
    }

    /**
     * @param array $item
     * @return object
     */
    protected function createObject(array $item)
    {
        return Group::fromApiData($this->client, $item);
    }

    /**
     * @return string
     */
    protected function getUriTemplate()
    {
        return 'admin/groups{/id}{?*query,per_page}';
    }
}
