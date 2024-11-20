<?php

declare(strict_types=1);

namespace Phpml\Dataset;

use Phpml\Exception\DatasetException;
use Phpml\Exception\FileException;

class SvmDataset extends ArrayDataset
{
    public function __construct(string $filePath)
    {
        [$samples, $targets] = self::readProblem($filePath);

        parent::__construct($samples, $targets);
    }

    private static function readProblem(string $filePath): array
    {
        $handle = self::openFile($filePath);

        $samples = [];
        $targets = [];
        $maxIndex = 0;
        while (false !== $line = fgets($handle)) {
            [$sample, $target, $maxIndex] = self::processLine($line, $maxIndex);
            $samples[] = $sample;
            $targets[] = $target;
        }

        fclose($handle);

        foreach ($samples as &$sample) {
            $sample = array_pad($sample, $maxIndex + 1, 0);
        }

        return [$samples, $targets];
    }

    /**
     * @return resource
     */
    private static function openFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new FileException(sprintf('File "%s" missing.', basename($filePath)));
        }

        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            throw new FileException(sprintf('File "%s" can\'t be open.', basename($filePath)));
        }

        return $handle;
    }

    private static function processLine(string $line, int $maxIndex): array
    {
        $columns = self::parseLine($line);

        $target = self::parseTargetColumn($columns[0]);
        $sample = array_fill(0, $maxIndex + 1, 0);

        $n = count($columns);
        for ($i = 1; $i < $n; ++$i) {
            [$index, $value] = self::parseFeatureColumn($columns[$i]);
            if ($index > $maxIndex) {
                $maxIndex = $index;
                $sample = array_pad($sample, $maxIndex + 1, 0);
            }

            $sample[$index] = $value;
        }

        return [$sample, $target, $maxIndex];
    }

    private static function parseLine(string $line): array
    {
        $line = explode('#', $line, 2)[0];
        $line = rtrim($line);
        $line = str_replace("\t", ' ', $line);

        return explode(' ', $line);
    }

    private static function parseTargetColumn(string $column): float
    {
        if (!is_numeric($column)) {
            throw new DatasetException(sprintf('Invalid target "%s".', $column));
        }

        return (float) $column;
    }

    private static function parseFeatureColumn(string $column): array
    {
        $feature = explode(':', $column, 2);
        if (count($feature) !== 2) {
            throw new DatasetException(sprintf('Invalid value "%s".', $column));
        }

        $index = self::parseFeatureIndex($feature[0]);
        $value = self::parseFeatureValue($feature[1]);

        return [$index, $value];
    }

    private static function parseFeatureIndex(string $index): int
    {
        if (!is_numeric($index) || !ctype_digit($index)) {
            throw new DatasetException(sprintf('Invalid index "%s".', $index));
        }

        if ((int) $index < 1) {
            throw new DatasetException(sprintf('Invalid index "%s".', $index));
        }

        return (int) $index - 1;
    }

    private static function parseFeatureValue(string $value): float
    {
        if (!is_numeric($value)) {
            throw new DatasetException(sprintf('Invalid value "%s".', $value));
        }

        return (float) $value;
    }
}
