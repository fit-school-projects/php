<?php declare(strict_types=1);

class Bag
{
    protected $items = [];
    public function add(mixed $item): void
    {
        $this->items[] = $item;
    }

    public function clear(): void
    {
        $this->items = [];
    }

    public function contains(mixed $item): bool
    {
        return in_array($item, $this->items, true);
    }

    public function elementSize(mixed $item): int
    {
        return count(array_filter($this->items, function ($i) use ($item) {
            return $i === $item;
        }));
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function remove(mixed $item): void
    {
        $key = array_search($item, $this->items, true);
        if ($key !== false) {
            unset($this->items[$key]);
            $this->items = array_values($this->items);
        }
    }

    public function size(): int
    {
        return count($this->items);
    }
}
