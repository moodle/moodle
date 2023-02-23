<?php

declare(strict_types=1);

namespace Phpml\Helper\Optimizer;

use Closure;
use Phpml\Exception\InvalidOperationException;

/**
 * Batch version of Gradient Descent to optimize the weights
 * of a classifier given samples, targets and the objective function to minimize
 */
class GD extends StochasticGD
{
    /**
     * Number of samples given
     *
     * @var int|null
     */
    protected $sampleCount;

    public function runOptimization(array $samples, array $targets, Closure $gradientCb): array
    {
        $this->samples = $samples;
        $this->targets = $targets;
        $this->gradientCb = $gradientCb;
        $this->sampleCount = count($this->samples);

        // Batch learning is executed:
        $currIter = 0;
        $this->costValues = [];
        while ($this->maxIterations > $currIter++) {
            $theta = $this->theta;

            // Calculate update terms for each sample
            [$errors, $updates, $totalPenalty] = $this->gradient($theta);

            $this->updateWeightsWithUpdates($updates, $totalPenalty);

            $this->costValues[] = array_sum($errors) / (int) $this->sampleCount;

            if ($this->earlyStop($theta)) {
                break;
            }
        }

        $this->clear();

        return $this->theta;
    }

    /**
     * Calculates gradient, cost function and penalty term for each sample
     * then returns them as an array of values
     */
    protected function gradient(array $theta): array
    {
        $costs = [];
        $gradient = [];
        $totalPenalty = 0;

        if ($this->gradientCb === null) {
            throw new InvalidOperationException('Gradient callback is not defined');
        }

        foreach ($this->samples as $index => $sample) {
            $target = $this->targets[$index];

            $result = ($this->gradientCb)($theta, $sample, $target);
            [$cost, $grad, $penalty] = array_pad($result, 3, 0);

            $costs[] = $cost;
            $gradient[] = $grad;
            $totalPenalty += $penalty;
        }

        $totalPenalty /= $this->sampleCount;

        return [$costs, $gradient, $totalPenalty];
    }

    protected function updateWeightsWithUpdates(array $updates, float $penalty): void
    {
        // Updates all weights at once
        for ($i = 0; $i <= $this->dimensions; ++$i) {
            if ($i === 0) {
                $this->theta[0] -= $this->learningRate * array_sum($updates);
            } else {
                $col = array_column($this->samples, $i - 1);

                $error = 0;
                foreach ($col as $index => $val) {
                    $error += $val * $updates[$index];
                }

                $this->theta[$i] -= $this->learningRate *
                    ($error + $penalty * $this->theta[$i]);
            }
        }
    }

    /**
     * Clears the optimizer internal vars after the optimization process.
     */
    protected function clear(): void
    {
        $this->sampleCount = null;
        parent::clear();
    }
}
