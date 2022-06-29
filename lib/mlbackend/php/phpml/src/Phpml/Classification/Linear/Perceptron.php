<?php

declare(strict_types=1);

namespace Phpml\Classification\Linear;

use Closure;
use Phpml\Classification\Classifier;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Helper\OneVsRest;
use Phpml\Helper\Optimizer\GD;
use Phpml\Helper\Optimizer\Optimizer;
use Phpml\Helper\Optimizer\StochasticGD;
use Phpml\Helper\Predictable;
use Phpml\IncrementalEstimator;
use Phpml\Preprocessing\Normalizer;

class Perceptron implements Classifier, IncrementalEstimator
{
    use Predictable;
    use OneVsRest;

    /**
     * @var Optimizer|GD|StochasticGD|null
     */
    protected $optimizer;

    /**
     * @var array
     */
    protected $labels = [];

    /**
     * @var int
     */
    protected $featureCount = 0;

    /**
     * @var array
     */
    protected $weights = [];

    /**
     * @var float
     */
    protected $learningRate;

    /**
     * @var int
     */
    protected $maxIterations;

    /**
     * @var Normalizer
     */
    protected $normalizer;

    /**
     * @var bool
     */
    protected $enableEarlyStop = true;

    /**
     * Initalize a perceptron classifier with given learning rate and maximum
     * number of iterations used while training the perceptron
     *
     * @param float $learningRate  Value between 0.0(exclusive) and 1.0(inclusive)
     * @param int   $maxIterations Must be at least 1
     *
     * @throws InvalidArgumentException
     */
    public function __construct(float $learningRate = 0.001, int $maxIterations = 1000, bool $normalizeInputs = true)
    {
        if ($learningRate <= 0.0 || $learningRate > 1.0) {
            throw new InvalidArgumentException('Learning rate should be a float value between 0.0(exclusive) and 1.0(inclusive)');
        }

        if ($maxIterations <= 0) {
            throw new InvalidArgumentException('Maximum number of iterations must be an integer greater than 0');
        }

        if ($normalizeInputs) {
            $this->normalizer = new Normalizer(Normalizer::NORM_STD);
        }

        $this->learningRate = $learningRate;
        $this->maxIterations = $maxIterations;
    }

    public function partialTrain(array $samples, array $targets, array $labels = []): void
    {
        $this->trainByLabel($samples, $targets, $labels);
    }

    public function trainBinary(array $samples, array $targets, array $labels): void
    {
        if ($this->normalizer !== null) {
            $this->normalizer->transform($samples);
        }

        // Set all target values to either -1 or 1
        $this->labels = [
            1 => $labels[0],
            -1 => $labels[1],
        ];
        foreach ($targets as $key => $target) {
            $targets[$key] = (string) $target == (string) $this->labels[1] ? 1 : -1;
        }

        // Set samples and feature count vars
        $this->featureCount = count($samples[0]);

        $this->runTraining($samples, $targets);
    }

    /**
     * Normally enabling early stopping for the optimization procedure may
     * help saving processing time while in some cases it may result in
     * premature convergence.<br>
     *
     * If "false" is given, the optimization procedure will always be executed
     * for $maxIterations times
     *
     * @return $this
     */
    public function setEarlyStop(bool $enable = true)
    {
        $this->enableEarlyStop = $enable;

        return $this;
    }

    /**
     * Returns the cost values obtained during the training.
     */
    public function getCostValues(): array
    {
        return $this->costValues;
    }

    protected function resetBinary(): void
    {
        $this->labels = [];
        $this->optimizer = null;
        $this->featureCount = 0;
        $this->weights = [];
        $this->costValues = [];
    }

    /**
     * Trains the perceptron model with Stochastic Gradient Descent optimization
     * to get the correct set of weights
     */
    protected function runTraining(array $samples, array $targets): void
    {
        // The cost function is the sum of squares
        $callback = function ($weights, $sample, $target): array {
            $this->weights = $weights;

            $prediction = $this->outputClass($sample);
            $gradient = $prediction - $target;
            $error = $gradient ** 2;

            return [$error, $gradient];
        };

        $this->runGradientDescent($samples, $targets, $callback);
    }

    /**
     * Executes a Gradient Descent algorithm for
     * the given cost function
     */
    protected function runGradientDescent(array $samples, array $targets, Closure $gradientFunc, bool $isBatch = false): void
    {
        $class = $isBatch ? GD::class : StochasticGD::class;

        if ($this->optimizer === null) {
            $this->optimizer = (new $class($this->featureCount))
                ->setLearningRate($this->learningRate)
                ->setMaxIterations($this->maxIterations)
                ->setChangeThreshold(1e-6)
                ->setEarlyStop($this->enableEarlyStop);
        }

        $this->weights = $this->optimizer->runOptimization($samples, $targets, $gradientFunc);
        $this->costValues = $this->optimizer->getCostValues();
    }

    /**
     * Checks if the sample should be normalized and if so, returns the
     * normalized sample
     */
    protected function checkNormalizedSample(array $sample): array
    {
        if ($this->normalizer !== null) {
            $samples = [$sample];
            $this->normalizer->transform($samples);
            $sample = $samples[0];
        }

        return $sample;
    }

    /**
     * Calculates net output of the network as a float value for the given input
     *
     * @return int|float
     */
    protected function output(array $sample)
    {
        $sum = 0;
        foreach ($this->weights as $index => $w) {
            if ($index == 0) {
                $sum += $w;
            } else {
                $sum += $w * $sample[$index - 1];
            }
        }

        return $sum;
    }

    /**
     * Returns the class value (either -1 or 1) for the given input
     */
    protected function outputClass(array $sample): int
    {
        return $this->output($sample) > 0 ? 1 : -1;
    }

    /**
     * Returns the probability of the sample of belonging to the given label.
     *
     * The probability is simply taken as the distance of the sample
     * to the decision plane.
     *
     * @param mixed $label
     */
    protected function predictProbability(array $sample, $label): float
    {
        $predicted = $this->predictSampleBinary($sample);

        if ((string) $predicted == (string) $label) {
            $sample = $this->checkNormalizedSample($sample);

            return (float) abs($this->output($sample));
        }

        return 0.0;
    }

    /**
     * @return mixed
     */
    protected function predictSampleBinary(array $sample)
    {
        $sample = $this->checkNormalizedSample($sample);

        $predictedClass = $this->outputClass($sample);

        return $this->labels[$predictedClass];
    }
}
