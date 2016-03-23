<?php

namespace vierbergenlars\Authserver\Client\Model;

class EmailAddress
{
    use LoadableTrait;
    /**
     * @var string
     */
    private $addr;
    /**
     * @var boolean
     */
    private $verified;
    /**
     * @var boolean
     */
    private $primary;
    /**
     * @var User|array
     */
    private $user;

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->addr;
    }

    /**
     * @return boolean
     */
    public function isVerified()
    {
        return $this->verified;
    }

    /**
     * @return boolean
     */
    public function isPrimary()
    {
        return $this->primary;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if(!$this->user instanceof User)
            $this->user = User::fromApiData($this->_client, $this->getOrLoad('user'));
        return $this->user;
    }
}
