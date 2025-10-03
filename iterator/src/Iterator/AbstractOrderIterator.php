<?php declare(strict_types=1);

namespace Iterator;

use Node;

abstract class AbstractOrderIterator implements \Iterator
{
    protected ?Node $current = null;
    protected ?Node $root = null;
    protected ?Node $node = null;
    protected array $stack = [];

    public function __construct(Node $root)
    {
        $this->root = $root;
    }

    public function current(): ?Node
    {
        return $this->current;
    }

    public abstract function next(): void;

    public function key(): bool|int|float|string|null
    {
        return $this?->current->getValue();
    }

    public abstract function valid(): bool;

    public abstract function rewind(): void;
}
