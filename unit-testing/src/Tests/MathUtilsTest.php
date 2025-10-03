<?php declare(strict_types=1);

namespace HW\Tests;

use Generator;
use HW\Factory\MathUtilsFactory;
use PHPUnit\Framework\TestCase;

class MathUtilsTest extends TestCase
{
    private function getMathUtils() {
        return MathUtilsFactory::get();
    }

    /**
     * @dataProvider _intDataProvider
     */
    public function testSum($sum, $numbers): void
    {
        $mathUtils = $this->getMathUtils();
        $this->assertEquals($sum, $mathUtils->sum($numbers));
        $this->expectException(\InvalidArgumentException::class);
        $mathUtils->sum([1,2,3,4,5,'a']);
    }

    public function testSolveLinear(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->assertEquals(2, $mathUtils->solveLinear(1, -2));
        $this->assertEquals(2, $mathUtils->solveLinear(-1, 2));
        $this->assertEquals(0.2, $mathUtils->solveLinear(10, -2));
    }
    public function testSolveLinearDivisionByZero(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->expectException(\InvalidArgumentException::class);
        $mathUtils->solveLinear(0.0, 2);
    }

    public function testSolveLinearTestReturnInt(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->assertIsInt($mathUtils->solveLinear(1, -2));
        $this->assertIsInt($mathUtils->solveLinear(1, 2));
        $this->assertIsInt($mathUtils->solveLinear(-1, -2));
    }

    public function testSolveLinearTestReturnFloat(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->assertIsFloat($mathUtils->solveLinear(1, -2.0));
        $this->assertIsFloat($mathUtils->solveLinear(5.0, 1));
        $this->assertIsFloat($mathUtils->solveLinear(5.0, 1.0));
        $this->assertIsFloat($mathUtils->solveLinear(10, -2));
    }

    public function testSolveLinearInvalid(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->expectException(\InvalidArgumentException::class);
        $mathUtils->solveLinear(1, "diewnf");
    }

    public function testSolveLinearInvalid2(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->expectException(\InvalidArgumentException::class);
        $mathUtils->solveLinear(0, 0.0);
    }

    public function testSolveLinearInvalid3(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->expectException(\InvalidArgumentException::class);
        $mathUtils->solveLinear("fergerg", 3);
    }

    public function testSolveLinearWithDelta() {
        $mathUtils = $this->getMathUtils();
        $this->assertEqualsWithDelta(0.5, $mathUtils->solveLinear(2, -1), 0.0001);
        $this->assertEqualsWithDelta(-0.5, $mathUtils->solveLinear(-2, -1), 0.0001);
    }

    /**
     * @dataProvider _quadraticDataProvider
     */
    public function testSolveQuadraticValid($x1, $x2, $a, $b, $c): void
    {
        $mathUtils = $this->getMathUtils();
        $this->assertEquals([$x1, $x2], $mathUtils->solveQuadratic($a, $b, $c));
        $this->assertEquals([-0.5], $mathUtils->solveQuadratic(0.0, 2, 1));
    }

    public function testSolveQuadraticInvalid(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->expectException(\InvalidArgumentException::class);
        $mathUtils->solveQuadratic(1, "diewnf", 10);
    }

    public function testSolveQuadraticInvalid2(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->expectException(\InvalidArgumentException::class);
        $mathUtils->solveQuadratic(0, 0, 0);
    }

    public function testSolveQuadraticInvalid3(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->expectException(\InvalidArgumentException::class);
        $mathUtils->solveQuadratic("fergrt", 7, 10);
    }

    public function testSolveQuadraticInvalid4(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->expectException(\InvalidArgumentException::class);
        $mathUtils->solveQuadratic(1, 7, "fvertg");
    }

    public function testSolveQuadraticDiscriminantIsLessThanZero(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->assertEquals([], $mathUtils->solveQuadratic(1, 1, 1));
    }

    public function testSolveQuadraticDiscriminantIsZero(): void
    {
        $mathUtils = $this->getMathUtils();
        $this->assertEquals([-1], $mathUtils->solveQuadratic(1, 2, 1));
    }

    public function _quadraticDataProvider() {
        yield [1, -2, 1, 1, -2];
        yield [7,-9,1,2,-63];
        yield [12,4,1,-16,48];
        yield [8,-12,1,4,-96];
        yield [1,2.0/3, 3,-5,2];
    }

    public function _intDataProvider(): Generator
    {
        yield [0,[]];
        yield [15,[1,2,3,4,5]];
        yield [0,[-1,1]];
        yield [2, [1.4, 0.8]];
    }
}
