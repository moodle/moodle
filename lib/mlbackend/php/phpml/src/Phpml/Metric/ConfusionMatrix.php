<?php

declare(strict_types=1);

namespace Phpml\Metric;

class ConfusionMatrix
{
    /**
     * @param array $actualLabels
     * @param array $predictedLabels
     * @param array $labels
     *
     * @return array
     */
    public static function compute(array $actualLabels, array $predictedLabels, array $labels = null): array
    {
        $labels = $labels ? array_flip($labels) : self::getUniqueLabels($actualLabels);
        $matrix = self::generateMatrixWithZeros($labels);

        foreach ($actualLabels as $index => $actual) {
            $predicted = $predictedLabels[$index];

            if (!isset($labels[$actual]) || !isset($labels[$predicted])) {
                continue;
            }

            if ($predicted === $actual) {
                $row = $column = $labels[$actual];
            } else {
                $row = $labels[$actual];
                $column = $labels[$predicted];
            }

            $matrix[$row][$column] += 1;
        }

        return $matrix;
    }

    /**
     * @param array $labels
     *
     * @return array
     */
    private static function generateMatrixWithZeros(array $labels): array
    {
        $count = count($labels);
        $matrix = [];

        for ($i = 0; $i < $count; ++$i) {
            $matrix[$i] = array_fill(0, $count, 0);
        }

        return $matrix;
    }

    /**
     * @param array $labels
     *
     * @return array
     */
    private static function getUniqueLabels(array $labels): array
    {
        $labels = array_values(array_unique($labels));
        sort($labels);
        $labels = array_flip($labels);

        return $labels;
    }
}
