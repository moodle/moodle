<?php

declare(strict_types=1);

namespace Phpml\Metric;

class ConfusionMatrix
{
    public static function compute(array $actualLabels, array $predictedLabels, array $labels = []): array
    {
        $labels = count($labels) === 0 ? self::getUniqueLabels($actualLabels) : array_flip($labels);
        $matrix = self::generateMatrixWithZeros($labels);

        foreach ($actualLabels as $index => $actual) {
            $predicted = $predictedLabels[$index];

            if (!isset($labels[$actual], $labels[$predicted])) {
                continue;
            }

            if ($predicted === $actual) {
                $row = $column = $labels[$actual];
            } else {
                $row = $labels[$actual];
                $column = $labels[$predicted];
            }

            ++$matrix[$row][$column];
        }

        return $matrix;
    }

    private static function generateMatrixWithZeros(array $labels): array
    {
        $count = count($labels);
        $matrix = [];

        for ($i = 0; $i < $count; ++$i) {
            $matrix[$i] = array_fill(0, $count, 0);
        }

        return $matrix;
    }

    private static function getUniqueLabels(array $labels): array
    {
        $labels = array_values(array_unique($labels));
        sort($labels);

        return array_flip($labels);
    }
}
