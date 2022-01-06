<?php

declare(strict_types=1);

namespace Phpml\Math\Statistic;

use Phpml\Exception\InvalidArgumentException;

class Correlation
{
    /**
     * @param int[]|float[] $x
     * @param int[]|float[] $y
     *
     * @throws InvalidArgumentException
     */
    public static function pearson(array $x, array $y): float
    {
        if (count($x) !== count($y)) {
            throw new InvalidArgumentException('Size of given arrays does not match');
        }

        $count = count($x);
        $meanX = Mean::arithmetic($x);
        $meanY = Mean::arithmetic($y);

        $axb = 0;
        $a2 = 0;
        $b2 = 0;

        for ($i = 0; $i < $count; ++$i) {
            $a = $x[$i] - $meanX;
            $b = $y[$i] - $meanY;
            $axb += ($a * $b);
            $a2 += $a ** 2;
            $b2 += $b ** 2;
        }

        return $axb / ($a2 * $b2) ** .5;
    }
}
