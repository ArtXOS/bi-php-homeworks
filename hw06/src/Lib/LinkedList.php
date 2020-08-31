<?php

namespace HW\Lib;

class LinkedList
{
    /** @var LinkedListItem|null */
    protected $first = null;

    /** @var LinkedListItem|null */
    protected $last = null;

    /**
     * @return LinkedListItem|null
     */
    public function getFirst(): ?LinkedListItem
    {
        return $this->first;
    }

    /**
     * @param LinkedListItem|null $first
     * @return LinkedList
     */
    public function setFirst(?LinkedListItem $first): LinkedList
    {
        $this->first = $first;

        return $this;
    }

    /**
     * @return LinkedListItem|null
     */
    public function getLast(): ?LinkedListItem
    {
        return $this->last;
    }

    /**
     * @param LinkedListItem|null $last
     * @return LinkedList
     */
    public function setLast(?LinkedListItem $last): LinkedList
    {
        $this->last = $last;

        return $this;
    }

    /**
     * Place new item at the begining of the list
     *
     * @param string $value
     * @return LinkedListItem
     */
    public function prependList(string $value)
    {
        $item = new LinkedListItem($value);
        $item->setPrev(null);

        if($this->getFirst() == null) {
            $this->setFirst($item);
            $this->setLast($item);
            $item->setNext(null);
        } else {
            $this->getFirst()->setPrev($item);
            $item->setNext($this->getFirst());
            $this->setFirst($item);
        }

        return $item;
    }

    /**
     * Place new item at the end of the list
     *
     * @param string $value
     * @return LinkedListItem
     */
    public function appendList(string $value)
    {
        $item = new LinkedListItem($value);
        $item->setNext(null);

        if($this->getFirst() == null) {
            $this->setFirst($item);
            $this->setLast($item);
            $item->setPrev(null);
        } else {
            $this->getLast()->setNext($item);
            $item->setPrev($this->getLast());
            $this->setLast($item);
        }

        return $item;
    }

    /**
     * Insert item before $nextItem and maintain continuity
     *
     * @param LinkedListItem $nextItem
     * @param string         $value
     * @return LinkedListItem
     */
    public function prependItem(LinkedListItem $nextItem, string $value)
    {
        $tmp = $this->first;
        while ($tmp->getValue() != $nextItem->getValue()) {
            $tmp = $tmp->getNext();
        };

        $nextItem = $tmp;

        $item = new LinkedListItem($value);
        $item->setNext($nextItem);
        if($nextItem->getPrev() == null) {
            $this->first = $item;
            $item->setPrev(null);
        } else {
            $item->setPrev($nextItem->getPrev());
            $nextItem->getPrev()->setNext($item);
        }
        $nextItem->setPrev($item);

        return $item;
    }

    /**
     * Insert item after $prevItem and maintain continuity
     *
     * @param LinkedListItem $prevItem
     * @param string         $value
     * @return LinkedListItem
     */
    public function appendItem(LinkedListItem $prevItem, string $value)
    {
        $tmp = $this->first;

        while ($tmp->getValue() != $prevItem->getValue()) {
            $tmp = $tmp->getNext();
        };

        $prevItem = $tmp;

        $item = new LinkedListItem($value);

        $item->setPrev($prevItem);
        if($prevItem->getNext() == null) {
            $this->last = $item;
            $item->setNext(null);
        } else {
            $item->setNext($prevItem->getNext());
            $prevItem->getNext()->setPrev($item);
        }
        
        $prevItem->setNext($item);

        return $item;
    }
}
