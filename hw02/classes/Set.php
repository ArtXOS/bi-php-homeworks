<?php

class Set extends Bag
{
    public function add($item): void {
        if(! $this->contains($item)) {
            $this->collection[] = $item;
        } else {
            return;
        }
    }

}