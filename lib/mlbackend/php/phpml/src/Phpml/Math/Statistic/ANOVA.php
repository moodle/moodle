<?php

declare(strict_types=1);

namespace Phpml\Math\Statistic;

use Phpml\Exception\InvalidArgumentException;

/**
 * Analysis of variance
 * https://en.wikipedia.org/wiki/Analysis_of_variance
 */
final class ANOVA
{
    /**
     * The one-way ANOVA tests the null hypothesis that 2 or more groups have
     * the same population mean. The test is applied to samples from two or
     * more groups, possibly with differing sizes.
     *
     * @param array[] $samples - each row is class samples
     *
     * @return float[]
     */
    public static function oneWayF(array $samples): array
    {
        $classes = count($samples);
        if ($classes < 2) {
            throw new InvalidArgumentException('The array must have at least 2 elements');
        }

        $samplesPerClass = array_map(function (array $class): int {
            return count($class);
        }, $samples);
        $allSamples = (int) array_sum($samplesPerClass);
        $ssAllSamples = self::sumOfSquaresPerFeature($samples);
        $sumSamples = self::sumOfFeaturesPerClass($samples);
        $squareSumSamples = self::sumOfSquares($sumSamples);
        $sumSamplesSquare = self::squaresSum($sumSamples);
        $ssbn = self::calculateSsbn($samples, $sumSamplesSquare, $samplesPerClass, $squareSumSamples, $allSamples);
        $sswn = self::calculateSswn($ssbn, $ssAllSamples, $squareSumSamples, $allSamples);
        $dfbn = $classes - 1;
        $dfwn = $allSamples - $classes;

        $msb = array_map(function ($s) use ($dfbn) {
            return $s / $dfbn;
        }, $ssbn);
        $msw = array_map(function ($s) use ($dfwn) {
            return $s / $dfwn;
        }, $sswn);

        $f = [];
        foreach ($msb as $index => $msbValue) {
            $f[$index] = $msbValue / $msw[$index];
        }

        return $f;
    }

    private static function sumOfSquaresPerFeature(array $samples): array
    {
        $sum = array_fill(0, count($samples[0][0]), 0);
        foreach ($samples as $class) {
            foreach ($class as $sample) {
                foreach ($sample as $index => $feature) {
                    $sum[$index] += $feature ** 2;
                }
            }
        }

        return $sum;
    }

    private static function sumOfFeaturesPerClass(array $samples): array
    {
        return array_map(function (array $class) {
            $sum = array_fill(0, count($class[0]), 0);
            foreach ($class as $sample) {
                foreach ($sample as $index => $feature) {
                    $sum[$index] += $feature;
                }
            }

            return $sum;
        }, $samples);
    }

    private static function sumOfSquares(array $sums): array
    {
        $squares = array_fill(0, count($sums[0]), 0);
        foreach ($sums as $row) {
            foreach ($row as $index => $sum) {
                $squares[$index] += $sum;
            }
        }

        return array_map(function ($sum) {
            return $sum ** 2;
        }, $squares);
    }

    private static function squaresSum(array $sums): array
    {
        foreach ($sums as &$row) {
            foreach ($row as &$sum) {
                $sum **= 2;
            }
        }

        return $sums;
    }

    private static function calculateSsbn(array $samples, array $sumSamplesSquare, array $samplesPerClass, array $squareSumSamples, int $allSamples): array
    {
        $ssbn = array_fill(0, count($samples[0][0]), 0);
        foreach ($sumSamplesSquare as $classIndex => $class) {
            foreach ($class as $index => $feature) {
                $ssbn[$index] += $feature / $samplesPerClass[$classIndex];
            }
        }

        foreach ($squareSumSamples as $index => $sum) {
            $ssbn[$index] -= $sum / $allSamples;
        }

        return $ssbn;
    }

    private static function calculateSswn(array $ssbn, array $ssAllSamples, array $squareSumSamples, int $allSamples): array
    {
        $sswn = [];
        foreach ($ssAllSamples as $index => $ss) {
            $sswn[$index] = ($ss - $squareSumSamples[$index] / $allSamples) - $ssbn[$index];
        }

        return $sswn;
    }
}
