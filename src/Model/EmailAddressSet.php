<?php

namespace vierbergenlars\Authserver\Client\Model;

use GuzzleHttp\ClientInterface;

class EmailAddressSet extends AbstractPaginatedResultSet
{
    private $user;
    public function __construct(ClientInterface $client, $request, User $user = null)
    {
        parent::__construct($client, $request);
        $this->user = $user;
    }

    protected function createObject(array $item)
    {
        if($this->user)
            $item+=['user'=>$this->user];
        return EmailAddress::fromApiData($this->client, $item);
    }

    /**
     * @return EmailAddress|null
     */
    public function getPrimaryAddress()
    {
        foreach($this as $emailAddress)
            /* @var $emailAddress EmailAddress */
            if($emailAddress->isPrimary()&&$emailAddress->isVerified())
                return $emailAddress;
        return null;
    }
}