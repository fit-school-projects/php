<?php declare(strict_types=1);

use Iterator\InOrderIterator;

class Node extends BaseNode implements IteratorAggregate
{
    public function getIterator(): Traversable
    {
        return new InOrderIterator($this);
    }
}
