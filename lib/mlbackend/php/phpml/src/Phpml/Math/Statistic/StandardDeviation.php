<?php

declare(strict_types=1);

namespace Phpml\Math\Statistic;

use Phpml\Exception\InvalidArgumentException;

class StandardDeviation
{
    /**
     * @param float[]|int[] $numbers
     */
    public static function population(array $numbers, bool $sample = true): float
    {
        $n = count($numbers);
        if ($n === 0) {
            throw new InvalidArgumentException('The array has zero elements');
        }

        if ($sample && $n === 1) {
            throw new InvalidArgumentException('The array must have at least 2 elements');
        }

        $mean = Mean::arithmetic($numbers);
        $carry = 0.0;
        foreach ($numbers as $val) {
            $carry += ($val - $mean) ** 2;
        }

        if ($sample) {
            --$n;
        }

        return ($carry / $n) ** .5;
    }

    /**
     * Sum of squares deviations
     * ∑⟮xᵢ - μ⟯²
     *
     * @param float[]|int[] $numbers
     */
    public static function sumOfSquares(array $numbers): float
    {
        if (count($numbers) === 0) {
            throw new InvalidArgumentException('The array has zero elements');
        }

        $mean = Mean::arithmetic($numbers);

        return array_sum(array_map(
            static function ($val) use ($mean): float {
                return ($val - $mean) ** 2;
            },
            $numbers
        ));
    }
}
