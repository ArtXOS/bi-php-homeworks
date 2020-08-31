<?php


namespace HW\Tests;


use HW\Lib\MathUtils;
use PHPUnit\Framework\TestCase;

class MathUtilsTest extends TestCase
{

    /**
     * @dataProvider sumDataProvider
     */
    public function testsum($list, $expected) {
        self::assertEquals($expected, MathUtils::sum($list));
    }

    public function sumDataProvider()
    {
        return array(
            array([1, 3, 4, 6, 7, 9, -8, 10, 0, -5, 45], 72),
            array([0, 0, 0, 0], 0),
            array([-5, -10, -15, 0], -30),
            array([], 0)
        );
    }

    /** @dataProvider solveLinearDataProvider */
    public function testsolveLinear($a, $b, $expected) {
        self::assertEquals($expected, MathUtils::solveLinear($a, $b));
    }

    public function solveLinearDataProvider() {
        return array (
            array(5, 10, -2),
            array(-5, 10, 2)
        );
    }

    public function testsolveLinearException() {
        $this->expectException(\InvalidArgumentException::class);
        self::assertEquals(2, MathUtils::solveLinear(0,10));
    }

    /** @dataProvider solveQuadraticDataProvider */
    public function testsolveQuadratic($expected, $a, $b, $c) {
        self::assertEquals($expected, MathUtils::solveQuadratic($a, $b, $c));
    }

    public function solveQuadraticDataProvider() {
        return array (
            array([1.0, -4.0], 1, 3, -4),
            array([1, -1], 1, 0, -1),
            array([-2, 2], -1, 0, 4),
            array([1, -3], 1, 2, -3),
            array([-3, -3], 1, 6, 9),
            array([], 1, 2, 17),
            array([-2, -2], 0, 5, 10),
            array([2, 2], 0, 2, -4)
        );
    }

    public function testsolveQuadraticException() {
        $this->expectException(\InvalidArgumentException::class);
        self::assertEquals([0, 0], MathUtils::solveQuadratic(0,0,0));
    }



}
