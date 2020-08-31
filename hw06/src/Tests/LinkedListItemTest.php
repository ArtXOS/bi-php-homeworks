<?php

namespace HW\Tests;

use Composer\Package\LinkConstraint\EmptyConstraint;
use HW\Lib\LinkedList;
use HW\Lib\LinkedListItem;
use PHPUnit\Framework\TestCase;

class LinkedListItemTest extends TestCase
{
    protected $list;

    public function setUp(): void
    {
        parent::setUp();
        $this->list = new LinkedList();
    }

    public function testEmpty() {
        self::assertEquals(null, $this->list->getFirst());
        self::assertEquals(null, $this->list->getLast());
    }

    public function testAppendList() {

        $this->list = new LinkedList();

        $this->list->appendList('Car');

        self::assertEquals('Car', $this->list->getFirst()->getValue());
        self::assertEquals('Car', $this->list->getLast()->getValue());
        self::assertEquals(null, $this->list->getLast()->getPrev());
        self::assertEquals(null, $this->list->getLast()->getNext());
        self::assertEquals(null, $this->list->getFirst()->getPrev());
        self::assertEquals(null, $this->list->getFirst()->getNext());

        $this->list->appendList('Tree');

        self::assertEquals('Car', $this->list->getFirst()->getValue());
        self::assertEquals('Tree', $this->list->getLast()->getValue());
        self::assertEquals('Car', $this->list->getLast()->getPrev()->getValue());
        self::assertEquals(null, $this->list->getLast()->getNext());

        $this->list->appendList('Box');

        self::assertEquals('Car', $this->list->getFirst()->getValue());
        self::assertEquals('Tree', $this->list->getFirst()->getNext()->getValue());
        self::assertEquals('Box', $this->list->getFirst()->getNext()->getNext()->getValue());
        self::assertEquals('Box', $this->list->getLast()->getValue());

        $this->list->getFirst()->setValue('Table');
        self::assertEquals('Table', $this->list->getFirst()->getValue());

    }

    public function testPrependList() {

        $this->list = new LinkedList();

        $this->list->prependList('Table1');

        self::assertEquals('Table1', $this->list->getFirst()->getValue());
        self::assertEquals('Table1', $this->list->getLast()->getValue());
        self::assertEquals(null, $this->list->getLast()->getPrev());
        self::assertEquals(null, $this->list->getLast()->getNext());
        self::assertEquals(null, $this->list->getFirst()->getPrev());
        self::assertEquals(null, $this->list->getFirst()->getNext());

        $this->list->appendList('Car');
        $this->list->appendList('Tree');
        $this->list->appendList('Box');

        $this->list->prependList('Table2');

        self::assertEquals(null, $this->list->getFirst()->getPrev());
        self::assertEquals('Table2', $this->list->getFirst()->getValue());
        self::assertEquals('Table1', $this->list->getFirst()->getNext()->getValue());

    }

    public function testPrependItem() {

        $this->list = new LinkedList();

        $this->list->appendList('Car');
        $this->list->appendList('Tree');
        $this->list->appendList('Box');

        $this->list->prependItem(new LinkedListItem('Tree'), 'Table');

        self::assertEquals('Car', $this->list->getFirst()->getValue());
        self::assertEquals('Table', $this->list->getFirst()->getNext()->getValue());
        self::assertEquals('Tree', $this->list->getFirst()->getNext()->getNext()->getValue());
        self::assertEquals('Box', $this->list->getFirst()->getNext()->getNext()->getNext()->getValue());

        $this->list = new LinkedList();
        $this->list->appendList('Car');
        $this->list->prependItem(new LinkedListItem('Car'), 'Table');
        self::assertEquals('Table', $this->list->getFirst()->getValue());
        self::assertEquals('Car', $this->list->getFirst()->getNext()->getValue());
        self::assertEquals('Table', $this->list->getFirst()->getNext()->getPrev()->getValue());

    }

    public function testAppendItem() {

        $this->list = new LinkedList();

        $this->list->appendList('Car');
        $this->list->appendList('Tree');
        $this->list->appendList('Box');

        $this->list->appendItem(new LinkedListItem('Tree'), 'Table');

        self::assertEquals('Car', $this->list->getFirst()->getValue());
        self::assertEquals('Tree', $this->list->getFirst()->getNext()->getValue());
        self::assertEquals('Table', $this->list->getFirst()->getNext()->getNext()->getValue());
        self::assertEquals('Box', $this->list->getFirst()->getNext()->getNext()->getNext()->getValue());

        $this->list = new LinkedList();
        $this->list->appendList('Car');
        $this->list->appendItem(new LinkedListItem('Car'), 'Table');
        self::assertEquals('Car', $this->list->getFirst()->getValue());
        self::assertEquals('Table', $this->list->getFirst()->getNext()->getValue());
        self::assertEquals('Table', $this->list->getLast()->getValue());
        self::assertEquals('Car', $this->list->getLast()->getPrev()->getValue());
    }

}
