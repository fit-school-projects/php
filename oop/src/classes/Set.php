<?php declare(strict_types=1);

class Set extends Bag
{
    public function add(mixed $item): void
    {
        if (!in_array($item, $this->items, true)) {
            $this->items[] = $item;
        }
    }
}