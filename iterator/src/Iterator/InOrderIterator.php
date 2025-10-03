<?php declare(strict_types=1);

namespace Iterator;

use Node;

class InOrderIterator extends AbstractOrderIterator
{
    public function __construct(Node $root)
    {
        parent::__construct($root);
        $this->current = $root;
        $this->rewind();
    }

    public function next(): void
    {
        if ($this->current !== null && $this->current->getRight() !== null) {
            $this->visitLeft($this->current->getRight());
        }
        $this->current = !empty($this->stack) ? array_pop($this->stack) : null;
    }

    public function valid(): bool
    {
        return $this->current !== null;
    }

    public function rewind(): void
    {
        $this->stack = [];
        $this->visitLeft($this->root);
        $this->current = !empty($this->stack) ? array_pop($this->stack) : null;
    }

    private function visitLeft(?Node $node): void
    {
        while ($node !== null) {
            $this->stack[] = $node;
            $node = $node->getLeft();
        }
    }
}
