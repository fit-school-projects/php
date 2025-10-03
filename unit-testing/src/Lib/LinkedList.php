<?php declare(strict_types=1);

namespace HW\Lib;

use HW\Interfaces\ILinkedList;
use HW\Interfaces\ILinkedListItem;

class LinkedList implements ILinkedList
{
    protected ?ILinkedListItem $first = null;

    protected ?ILinkedListItem $last = null;

    public function getFirst(): ?ILinkedListItem
    {
        if ($this->first === null) {
            return null;
        }
        return $this->first;
    }

    public function setFirst(?ILinkedListItem $first): LinkedList
    {
        $this->first = $first;
        return $this;
    }

    public function getLast(): ?ILinkedListItem
    {
        if ($this->last === null) {
            return null;
        }
        return $this->last;
    }

    public function setLast(?ILinkedListItem $last): LinkedList
    {
        $this->last = $last;
        return $this;
    }

    /**
     * Place new item at the begining of the list
     */
    public function prependList(string $value): ILinkedListItem
    {
        $item = new LinkedListItem($value);
        if ($this->first === null) {
            $this->first = $this->last = $item;
        } else {
            $second = $this->first;
            $this->first = $item;
            $item->setNext($second);
            $second->setPrev($item);
        }
        return $item;
    }

    /**
     * Place new item at the end of the list
     */
    public function appendList(string $value): ILinkedListItem
    {
        $item = new LinkedListItem($value);
        if ($this->last === null) {
            $this->first = $this->last = $item;
        } else {
            $last = $this->last;
            $this->last = $item;
            $item->setPrev($last);
            $last->setNext($item);
        }
        return $item;
    }

    /**
     * Insert item before $nextItem and maintain continuity
     */
    public function prependItem(ILinkedListItem $nextItem, string $value): ILinkedListItem
    {
        $item = new LinkedListItem($value);
        $prev = $nextItem->getPrev();
        $item->setNext($nextItem);
        $item->setPrev($prev);

        if ($prev !== null) {
            $prev->setNext($item);
        } else {
            $this->first = $item;
        }

        $nextItem->setPrev($item);

        return $item;
    }

    /**
     * Insert item after $prevItem and maintain continuity
     */
    public function appendItem(ILinkedListItem $prevItem, string $value): ILinkedListItem
    {
        $item = new LinkedListItem($value);
        $next = $prevItem->getNext();
        $item->setPrev($prevItem);
        $prevItem->setNext($item);

        if ($next !== null) {
            $next->setPrev($item);
        } else {
            $this->last = $item;
        }

        $item->setNext($next);

        return $item;
    }
}
