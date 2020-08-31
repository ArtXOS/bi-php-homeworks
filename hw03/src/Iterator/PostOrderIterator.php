<?php

namespace Iterator;

use Node;

class PostOrderIterator extends AbstractOrderIterator
{
    public function __construct(Node $root) {
        
        $q = array();
        self::postorder($root, $q);
        $this->que = $q;
    }

    private static function postorder($tmp, &$q) {
        if($tmp == null) return;
        self::postorder($tmp->getLeft(), $q);
        self::postorder($tmp->getRight(), $q);  
        $q[] = $tmp;
    }
}
