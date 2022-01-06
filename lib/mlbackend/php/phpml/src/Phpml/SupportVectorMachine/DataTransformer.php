<?php

declare(strict_types=1);

namespace Phpml\SupportVectorMachine;

use Phpml\Exception\InvalidArgumentException;

class DataTransformer
{
    public static function trainingSet(array $samples, array $labels, bool $targets = false): string
    {
        $set = '';
        $numericLabels = [];

        if (!$targets) {
            $numericLabels = self::numericLabels($labels);
        }

        foreach ($labels as $index => $label) {
            $set .= sprintf('%s %s %s', ($targets ? $label : $numericLabels[$label]), self::sampleRow($samples[$index]), PHP_EOL);
        }

        return $set;
    }

    public static function testSet(array $samples): string
    {
        if (count($samples) === 0) {
            throw new InvalidArgumentException('The array has zero elements');
        }

        if (!is_array($samples[0])) {
            $samples = [$samples];
        }

        $set = '';
        foreach ($samples as $sample) {
            $set .= sprintf('0 %s %s', self::sampleRow($sample), PHP_EOL);
        }

        return $set;
    }

    public static function predictions(string $rawPredictions, array $labels): array
    {
        $numericLabels = self::numericLabels($labels);
        $results = [];
        foreach (explode(PHP_EOL, $rawPredictions) as $result) {
            if (isset($result[0])) {
                $results[] = array_search((int) $result, $numericLabels, true);
            }
        }

        return $results;
    }

    public static function probabilities(string $rawPredictions, array $labels): array
    {
        $numericLabels = self::numericLabels($labels);

        $predictions = explode(PHP_EOL, trim($rawPredictions));

        $header = array_shift($predictions);
        $headerColumns = explode(' ', (string) $header);
        array_shift($headerColumns);

        $columnLabels = [];
        foreach ($headerColumns as $numericLabel) {
            $columnLabels[] = array_search((int) $numericLabel, $numericLabels, true);
        }

        $results = [];
        foreach ($predictions as $rawResult) {
            $probabilities = explode(' ', $rawResult);
            array_shift($probabilities);

            $result = [];
            foreach ($probabilities as $i => $prob) {
                $result[$columnLabels[$i]] = (float) $prob;
            }

            $results[] = $result;
        }

        return $results;
    }

    public static function numericLabels(array $labels): array
    {
        $numericLabels = [];
        foreach ($labels as $label) {
            if (isset($numericLabels[$label])) {
                continue;
            }

            $numericLabels[$label] = count($numericLabels);
        }

        return $numericLabels;
    }

    private static function sampleRow(array $sample): string
    {
        $row = [];
        foreach ($sample as $index => $feature) {
            $row[] = sprintf('%s:%F', $index + 1, $feature);
        }

        return implode(' ', $row);
    }
}
