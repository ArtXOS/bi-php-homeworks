<?php

namespace Iterator;

use Node;

class InOrderIterator extends AbstractOrderIterator
{
    public function __construct(Node $root) {
        
        $q = array();
        self::inorder($root, $q);
        $this->que = $q;
    }

    private static function inorder($tmp, &$q) {
        if($tmp == null) return;
        self::inorder($tmp->getLeft(), $q);
        $q[] = $tmp;
        self::inorder($tmp->getRight(), $q);  
    }
}
