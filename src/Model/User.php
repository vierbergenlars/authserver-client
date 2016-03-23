<?php

namespace vierbergenlars\Authserver\Client\Model;

class User
{
    use LoadableTrait;
    /**
     * @var string
     */
    private $guid;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $display_name;
    /**
     * @var boolean
     */
    private $non_locked;
    /**
     * @var boolean
     */
    private $enabled;
    /**
     * @var string
     */
    private $role;

    /**
     * @var array
     */
    private $emails;

    /**
     * @var array
     */
    private $groups;

    /**
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * @return boolean
     */
    public function isNonLocked()
    {
        return $this->getOrLoad('non_locked');
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getOrLoad('enabled');
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->getOrLoad('role');
    }

    /**
     * @return EmailAddressSet
     */
    public function getEmails()
    {
        return new EmailAddressSet($this->_client, $this->getOrLoad('emails'), $this);
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return new GroupSet($this->_client, $this->getOrLoad('groups'));
    }
}
