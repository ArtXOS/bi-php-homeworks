<?php

namespace HW\Lib;


use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Void_;

class MathUtils
{
    /**
     * Sum a list of numbers.
     *
     * @param $list
     * @return int
     */
    public static function sum($list)
    {
        $sum = 0;
        $i = 0;

        while ($i < count($list)) {
            $sum += $list[$i];
            $i++;
        }

        return $sum;
    }

    /**
     * Solve linear equation ax + b = 0.
     *
     * @param $a
     * @param $b
     * @return float|int
     */
    public static function solveLinear($a, $b)
    {
        if ($a === 0) {
            throw new \InvalidArgumentException();
        }

        return -$b / $a;
    }

    /**
     * Solve quadratic equation ax^2 + bx + c = 0.
     *
     * @param $a
     * @param $b
     * @param $c
     * @return array Solution x1 and x2.
     */
    public static function solveQuadratic($a, $b, $c)
    {
        if($a == 0) {
            $result = self::solveLinear($b, $c);
            return [$result, $result];
        }

        $d = pow($b, 2) - 4 * $a * $c;

        if($d == 0) {
            return [(-$b - sqrt($d)) / (2 * $a), (-$b - sqrt($d)) / (2 * $a)];
        }
        else if($d < 0) {
            return [];
        } else {
            $x1 = (-$b + sqrt($d)) / (2 * $a);
            $x2 = (-$b - sqrt($d)) / (2 * $a);
            return [$x1, $x2];
        }
    }
}
