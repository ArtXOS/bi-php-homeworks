<?php

namespace Iterator;
use Node;

abstract class AbstractOrderIterator implements \Iterator
{
    // TODO: shared attributes?
    protected $que = array();
    /**
     * AbstractOrderIterator constructor.
     *
     * @param Node $root
     */
    public function __construct(Node $start)
    {
    }

    public function current()
    {
        return current($this->que);
    }

    public function next()
    {
        next($this->que);
    }

    public function key()
    {
        return key($this->que);
    }

    public function valid()
    {
        $key = key($this->que);
        $bool = ($key !== null && $key !== false);
        return $bool;
    }

    public function rewind()
    {
        reset($this->que);
    }
}
