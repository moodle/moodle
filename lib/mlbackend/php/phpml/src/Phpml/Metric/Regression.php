<?php

declare(strict_types=1);

namespace Phpml\Metric;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Statistic\Correlation;
use Phpml\Math\Statistic\Mean;

final class Regression
{
    public static function meanSquaredError(array $targets, array $predictions): float
    {
        self::assertCountEquals($targets, $predictions);

        $errors = [];
        foreach ($targets as $index => $target) {
            $errors[] = (($target - $predictions[$index]) ** 2);
        }

        return Mean::arithmetic($errors);
    }

    public static function meanSquaredLogarithmicError(array $targets, array $predictions): float
    {
        self::assertCountEquals($targets, $predictions);

        $errors = [];
        foreach ($targets as $index => $target) {
            $errors[] = log((1 + $target) / (1 + $predictions[$index])) ** 2;
        }

        return Mean::arithmetic($errors);
    }

    public static function meanAbsoluteError(array $targets, array $predictions): float
    {
        self::assertCountEquals($targets, $predictions);

        $errors = [];
        foreach ($targets as $index => $target) {
            $errors[] = abs($target - $predictions[$index]);
        }

        return Mean::arithmetic($errors);
    }

    public static function medianAbsoluteError(array $targets, array $predictions): float
    {
        self::assertCountEquals($targets, $predictions);

        $errors = [];
        foreach ($targets as $index => $target) {
            $errors[] = abs($target - $predictions[$index]);
        }

        return (float) Mean::median($errors);
    }

    public static function r2Score(array $targets, array $predictions): float
    {
        self::assertCountEquals($targets, $predictions);

        return Correlation::pearson($targets, $predictions) ** 2;
    }

    public static function maxError(array $targets, array $predictions): float
    {
        self::assertCountEquals($targets, $predictions);

        $errors = [];
        foreach ($targets as $index => $target) {
            $errors[] = abs($target - $predictions[$index]);
        }

        return (float) max($errors);
    }

    private static function assertCountEquals(array &$targets, array &$predictions): void
    {
        if (count($targets) !== count($predictions)) {
            throw new InvalidArgumentException('Targets count must be equal with predictions count');
        }
    }
}
