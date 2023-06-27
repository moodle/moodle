<?php

declare(strict_types=1);

namespace Phpml\Math\Statistic;

/**
 * In probability theory and statistics, variance is the expectation of the squared deviation of a random variable from its mean.
 * Informally, it measures how far a set of (random) numbers are spread out from their average value
 * https://en.wikipedia.org/wiki/Variance
 */
final class Variance
{
    /**
     * Population variance
     * Use when all possible observations of the system are present.
     * If used with a subset of data (sample variance), it will be a biased variance.
     *
     *      ∑⟮xᵢ - μ⟯²
     * σ² = ----------
     *          N
     */
    public static function population(array $population): float
    {
        return StandardDeviation::sumOfSquares($population) / count($population);
    }
}
