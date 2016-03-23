<?php

namespace vierbergenlars\Authserver\Client\Model;

class Group
{
    use LoadableTrait;

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $display_name;
    /**
     * @var array
     */
    private $members;
    /**
     * @var array
     */
    private $parents;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * @return GroupSet
     */
    public function getMembers()
    {
        return new GroupSet($this->_client, $this->getOrLoad('members'));
    }

    /**
     * @return GroupSet
     */
    public function getParents()
    {
        return new GroupSet($this->_client, $this->getOrLoad('parents'));
    }

    /**
     * @return UserSet
     */
    public function getUsers()
    {
        return new UserSet($this->_client, $this->_links['members']['href']);
    }
}
