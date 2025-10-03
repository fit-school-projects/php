<?php declare(strict_types=1);

namespace Iterator;

use Node;

class PreOrderIterator extends AbstractOrderIterator
{
    public function __construct(Node $root)
    {
        parent::__construct($root);
        $this->current = $root;
        $this->rewind();
    }

    public function next(): void
    {
        $this->node = array_pop($this->stack);
        if ($this->node !== null) {
            if ($this->node->getRight() !== null) {
                $this->stack[] = $this->node->getRight();
            }
            if ($this->node->getLeft() !== null) {
                $this->stack[] = $this->node->getLeft();
            }
        }
        $this->current = !empty($this->stack) ? end($this->stack) : null;
    }

    public function valid(): bool
    {
        return $this->current !== null;
    }

    public function rewind(): void
    {
        $this->stack = [];
        if ($this->root !== null) {
            $this->stack[] = $this->root;
        }
    }
}
