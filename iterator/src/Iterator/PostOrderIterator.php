<?php declare(strict_types=1);

namespace Iterator;

use Node;

class PostOrderIterator extends AbstractOrderIterator
{
    public function __construct(Node $root)
    {
        parent::__construct($root);
        $this->rewind();
    }

    public function next(): void
    {
        $node = array_pop($this->stack);

        if (!empty($this->stack)) {
            $topNode = end($this->stack);
            if ($node === $topNode->getLeft()) {
                $this->init($topNode->getRight());
            }
        }
        $this->current = $node;
    }

    public function valid(): bool
    {
        return $this->current !== null;
    }

    public function rewind(): void
    {
        $this->stack = [];
        $this->init($this->root);
        $this->current = !empty($this->stack) ? end($this->stack) : null;
        $this->next();
    }

    private function init($root): void
    {
        while ($root !== null) {
            $this->stack[] = $root;
            $root = $root->getLeft() !== null ? $root->getLeft() : $root->getRight();
        }
    }
}
