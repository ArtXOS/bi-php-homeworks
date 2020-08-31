<?php

class Bag {

    protected $collection = array();

    public function add($item) {
        $this->collection[] = $item;
    }

    public function clear(): void {
        foreach ($this->collection as $i => $value) {
            unset($this->collection[$i]);
        }
    }

    public function contains($item): bool {
        foreach ($this->collection as $i => $value) {
            if($this->collection[$i] === $item) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function elementSize($item): int {
        $counter = 0;
        foreach ($this->collection as $i => $value) {
            if($this->collection[$i] === $item) {
                $counter++;
            }
        }
        return $counter;
    }

    public function isEmpty(): bool {
        return empty($this->collection);
    }

    public function remove($item): void {
        foreach ($this->collection as $i => $value) {
            if($value == $item) {
                unset($this->collection[$i]);
                return;
            }
        }
    }

    public function size(): int {
        return count($this->collection);
    }

}
