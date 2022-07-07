<?php

declare(strict_types=1);

namespace Phpml\FeatureSelection\ScoringFunction;

use Phpml\FeatureSelection\ScoringFunction;
use Phpml\Math\Matrix;
use Phpml\Math\Statistic\Mean;

/**
 * Quick linear model for testing the effect of a single regressor,
 * sequentially for many regressors.
 *
 * This is done in 2 steps:
 *
 * 1. The cross correlation between each regressor and the target is computed,
 * that is, ((X[:, i] - mean(X[:, i])) * (y - mean_y)) / (std(X[:, i]) *std(y)).
 * 2. It is converted to an F score.
 *
 * Ported from scikit-learn f_regression function (http://scikit-learn.org/stable/modules/generated/sklearn.feature_selection.f_regression.html#sklearn.feature_selection.f_regression)
 */
final class UnivariateLinearRegression implements ScoringFunction
{
    /**
     * @var bool
     */
    private $center;

    /**
     * @param bool $center - if true samples and targets will be centered
     */
    public function __construct(bool $center = true)
    {
        $this->center = $center;
    }

    public function score(array $samples, array $targets): array
    {
        if ($this->center) {
            $this->centerTargets($targets);
            $this->centerSamples($samples);
        }

        $correlations = [];
        foreach (array_keys($samples[0]) as $index) {
            $featureColumn = array_column($samples, $index);
            $correlations[$index] =
                (Matrix::dot($targets, $featureColumn)[0] / (new Matrix($featureColumn, false))->transpose()->frobeniusNorm())
                / (new Matrix($targets, false))->frobeniusNorm();
        }

        $degreesOfFreedom = count($targets) - ($this->center ? 2 : 1);

        return array_map(function (float $correlation) use ($degreesOfFreedom): float {
            return $correlation ** 2 / (1 - $correlation ** 2) * $degreesOfFreedom;
        }, $correlations);
    }

    private function centerTargets(array &$targets): void
    {
        $mean = Mean::arithmetic($targets);
        array_walk($targets, function (&$target) use ($mean): void {
            $target -= $mean;
        });
    }

    private function centerSamples(array &$samples): void
    {
        $means = [];
        foreach ($samples[0] as $index => $feature) {
            $means[$index] = Mean::arithmetic(array_column($samples, $index));
        }

        foreach ($samples as &$sample) {
            foreach ($sample as $index => &$feature) {
                $feature -= $means[$index];
            }
        }
    }
}
