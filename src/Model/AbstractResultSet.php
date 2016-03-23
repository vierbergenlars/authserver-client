<?php

namespace vierbergenlars\Authserver\Client\Model;

abstract class AbstractResultSet implements \Countable, \ArrayAccess, \Iterator
{
    protected $items = [];

    private $ptr = 0;

    private $total = null;

    /**
     * AbstractResultSet constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function count()
    {
        if($this->total === null)
            $this->total = $this->loadCount();
        return $this->total;
    }

    public function offsetExists($offset)
    {
        return is_int($offset)&&$offset < $this->count();
    }

    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset))
            throw new \OutOfBoundsException('The offset '.$offset.' does not exist.');
        return $this->createObject($this->items[$offset]);
    }

    abstract protected function createObject(array $item);

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Result sets are immutable');
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('Result sets are immutable');
    }

    protected function loadCount()
    {
        return count($this->items);
    }

    public function current()
    {
        return $this->offsetGet($this->ptr);
    }

    public function next()
    {
        $this->ptr++;
    }

    public function key()
    {
        return $this->ptr;
    }

    public function valid()
    {
        return $this->ptr < $this->count();
    }

    public function rewind()
    {
        $this->ptr = 0;
    }
}
