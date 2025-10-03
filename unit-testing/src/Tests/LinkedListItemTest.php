<?php declare(strict_types=1);

namespace HW\Tests;

use HW\Factory\LinkedListFactory;
use HW\Lib\LinkedList;
use HW\Lib\LinkedListItem;
use PHPUnit\Framework\TestCase;

class LinkedListItemTest extends TestCase
{
    protected $list;
    private int $VALUES_COUNT = 60;

    public function setUp(): void
    {
        parent::setUp();
        $this->list = LinkedListFactory::get();
    }

    public function testGetValue() {
        $item = new LinkedListItem('test');
        $this->assertEquals('test', $item->getValue());
    }

    public function testSetValue() {
        $item = new LinkedListItem('test');
        $item->setValue('test2');
        $this->assertEquals('test2', $item->getValue());
    }

    public function testGetNext() {
        $item = new LinkedListItem('test');
        $this->assertNull($item->getNext());
        $item->setNext(new LinkedListItem('test2'));
        $this->assertNotNull($item->getNext());
    }

    public function testSetNext() {
        $item = new LinkedListItem('test');
        $item2 = new LinkedListItem('test2');
        $item->setNext($item2);
        $this->assertEquals($item2, $item->getNext());
    }

    public function testGetPrev() {
        $item = new LinkedListItem('test');
        $this->assertNull($item->getPrev());
        $item->setPrev(new LinkedListItem('test2'));
        $this->assertNotNull($item->getPrev());
    }

public function testSetPrev() {
        $item = new LinkedListItem('test');
        $item2 = new LinkedListItem('test2');
        $item->setPrev($item2);
        $this->assertEquals($item2, $item->getPrev());
    }

    public function testGetFirst(): void
    {
        $this->assertNull($this->list->getFirst());
        $this->list->prependList('test');
        $this->assertNotNull($this->list->getFirst());
    }

    public function testGetLast(): void
    {
        $list = new LinkedList();
        $this->assertNull($list->getLast());

        $list->prependList('test');
        $this->assertNotNull($list->getLast());
    }

    public function testSetFirst(): void
    {
        $this->list->setFirst(null);
        $this->assertNull($this->list->getFirst());
    }

    public function testSetLast(): void
    {
        $this->list->setLast(null);
        $this->assertNull($this->list->getLast());
    }

    /**
     * @dataProvider _valuesGenerator
     */
    public function testPrependList($values): void
    {
        $this->assertEquals(null, $this->list->getFirst());

        for ($i = $this->VALUES_COUNT - 1; $i >= 0; $i--){
            $item = $this->list->prependList($values[$i]);
            $this->assertEquals($item, $this->list->getFirst());
        }

        $item = $this->list->getFirst();
        for ($i = 0; $i < $this->VALUES_COUNT; $i++){
            $this->assertEquals($values[$i], $item->getValue());
            $item = $item->getNext();
        }

        $item = $this->list->getLast();
        for ($i = $this->VALUES_COUNT - 1; $i >= 0; $i--){
            $this->assertEquals($values[$i], $item->getValue());
            $item = $item->getPrev();
        }
    }

    /**
     * @dataProvider _valuesGenerator
     */
    public function testAppendList($values): void
    {
        $this->assertEquals(null, $this->list->getFirst());

        for ($i = 0; $i < $this->VALUES_COUNT; $i++){
            $item = $this->list->appendList($values[$i]);
            $this->assertEquals($item, $this->list->getLast());
        }

        $item = $this->list->getFirst();
        for ($i = 0; $i < $this->VALUES_COUNT; $i++){
            $this->assertEquals($values[$i], $item->getValue());
            $item = $item->getNext();
        }

        $item = $this->list->getLast();
        for ($i = $this->VALUES_COUNT - 1; $i >= 0; $i--){
            $this->assertEquals($values[$i], $item->getValue());
            $item = $item->getPrev();
        }
    }

    /**
     * @depends testPrependList
     */
    public function testPrependItem(): void
    {
        $this->list->prependList('test');
        $n = $this->list->prependItem($this->list->getFirst(), 'test2');
        $this->assertEquals('test2', $this->list->getLast()->getPrev()->getValue());
        $this->assertEquals($n, $this->list->getLast()->getPrev());

        $n1 = $this->list->prependItem($this->list->getFirst(), 'testttt');
        $this->assertEquals('testttt', $this->list->getFirst()->getValue());
        $this->assertEquals($n1, $this->list->getFirst());
        $this->assertEquals('test2', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals('test', $this->list->getLast()->getValue());

        $n2 = $this->list->prependItem($this->list->getLast()->getPrev(), 't');
        $this->assertEquals('t', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals($n2, $this->list->getFirst()->getNext());

        $n3 = $this->list->prependItem($this->list->getFirst()->getNext(), 't2');
        $this->assertEquals('test', $this->list->getFirst()->getNext()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('test2', $this->list->getFirst()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('t', $this->list->getFirst()->getNext()->getNext()->getValue());
        $this->assertEquals('t2', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals($n3, $this->list->getFirst()->getNext());
        $this->assertEquals('testttt', $this->list->getFirst()->getValue());
    }

    /**
     * @depends testAppendList
     */
    public function testAppendItem(): void
    {
        $n = $this->list->prependList('test');

        $n1 = $this->list->appendItem($this->list->getFirst(), 'test2');
        $this->assertEquals('test', $this->list->getFirst()->getValue());
        $this->assertEquals('test2', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals($n1, $this->list->getLast());
        $this->assertEquals($n, $this->list->getFirst());

        $n2 = $this->list->appendItem($this->list->getFirst(), 'test3');
        $this->assertEquals('test', $this->list->getFirst()->getValue());
        $this->assertEquals($n2, $this->list->getFirst()->getNext());
        $this->assertEquals('test3', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals('test2', $this->list->getFirst()->getNext()->getNext()->getValue());

        $n3 = $this->list->appendItem($this->list->getFirst()->getNext(), 'test4');
        $this->assertEquals('test', $this->list->getFirst()->getValue());
        $this->assertEquals('test3', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals('test4', $this->list->getFirst()->getNext()->getNext()->getValue());
        $this->assertEquals($n3, $this->list->getFirst()->getNext()->getNext());
        $this->assertEquals('test2', $this->list->getFirst()->getNext()->getNext()->getNext()->getValue());

        $n4 = $this->list->appendItem($this->list->getLast(), 'test5');
        $this->assertEquals('test', $this->list->getFirst()->getValue());
        $this->assertEquals('test3', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals('test4', $this->list->getFirst()->getNext()->getNext()->getValue());
        $this->assertEquals('test2', $this->list->getFirst()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('test5', $this->list->getLast()->getValue());

        $n5 = $this->list->appendItem($this->list->getLast()->getPrev(), 'test6');
        $this->assertEquals('test', $this->list->getFirst()->getValue());
        $this->assertEquals('test3', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals('test4', $this->list->getFirst()->getNext()->getNext()->getValue());
        $this->assertEquals('test2', $this->list->getFirst()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('test6', $this->list->getFirst()->getNext()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('test5', $this->list->getLast()->getValue());
        $this->assertEquals($n5, $this->list->getLast()->getPrev());

        $n6 = $this->list->appendItem($this->list->getLast(), 'testtt');
        $this->assertEquals('test', $this->list->getFirst()->getValue());
        $this->assertEquals('test3', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals('test4', $this->list->getFirst()->getNext()->getNext()->getValue());
        $this->assertEquals('test2', $this->list->getFirst()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('test6', $this->list->getFirst()->getNext()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('test5', $this->list->getFirst()->getNext()->getNext()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('testtt', $this->list->getLast()->getValue());
        $this->assertEquals($n6, $this->list->getLast());

        $n7 = $this->list->appendItem($this->list->getFirst()->getNext()->getNext()->getNext(), 't');
        $this->assertEquals('test', $this->list->getFirst()->getValue());
        $this->assertEquals('test3', $this->list->getFirst()->getNext()->getValue());
        $this->assertEquals('test4', $this->list->getFirst()->getNext()->getNext()->getValue());
        $this->assertEquals('test2', $this->list->getFirst()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('t', $this->list->getFirst()->getNext()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('test6', $this->list->getFirst()->getNext()->getNext()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('test5', $this->list->getFirst()->getNext()->getNext()->getNext()->getNext()->getNext()->getNext()->getValue());
        $this->assertEquals('testtt', $this->list->getLast()->getValue());
        $this->assertEquals($n7, $this->list->getFirst()->getNext()->getNext()->getNext()->getNext());
    }

    public function _valuesGenerator () : \Generator {
        $values = [];
        for ($i = 0; $i < $this->VALUES_COUNT; $i++){
            $values[$i] = '';
            $values[$i] = "test" . $i;
        }
        yield [$values];
    }
}
