<?php declare(strict_types=1);

namespace HW\Lib;

use HW\Interfaces\IMathUtils;
use InvalidArgumentException;

class MathUtils implements IMathUtils
{
    /**
     * Sum a list of numbers.
     */
    public function sum(array $list): int
    {
        $sum = 0;
        foreach ($list as $number) {
            if (!is_numeric($number)) {
                throw new InvalidArgumentException();
            }
            $sum += $number;
        }
        return (int) $sum;
    }

    /**
     * Solve linear equation ax + b = 0.
     */
    public function solveLinear($a, $b): float|int
    {
        if (!is_numeric($a) || !is_numeric($b) || $a == 0) {
            throw new InvalidArgumentException();
        }

        return -$b / $a;
    }

    /**
     * Solve quadratic equation ax^2 + bx + c = 0.
     *
     * @return array Solution x1 and x2.
     */
    public function solveQuadratic($a, $b, $c): array
    {
        if (!is_numeric($a) || !is_numeric($b) || !is_numeric($c)) {
            throw new InvalidArgumentException();
        }

        if ($a == 0){
            return [$this->solveLinear($b, $c)];
        }

        $d = ($b * $b) - (4 * $a * $c);

        if ($d < 0) {
            return [];
        }

        $sqrtD = sqrt($d);
        $x1 = (-$b + $sqrtD) / (2 * $a);
        $x2 = (-$b - $sqrtD) / (2 * $a);

        return $d === 0 ? [$x1] : [$x1, $x2];
    }
}
