<?php

namespace Iterator;

use Node;

class PreOrderIterator extends AbstractOrderIterator
{
    public function __construct(Node $root) {
        
        $q = array();
        self::preorder($root, $q);
        $this->que = $q;
    }

    private static function preorder($tmp, &$q) {
        if($tmp == null) return;
        $q[] = $tmp;
        self::preorder($tmp->getLeft(), $q);
        self::preorder($tmp->getRight(), $q);  
    }
}
