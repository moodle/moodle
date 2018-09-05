<?php

declare(strict_types=1);

namespace Phpml\Classification\Linear;

use Phpml\Helper\Predictable;
use Phpml\Helper\OneVsRest;
use Phpml\Helper\Optimizer\StochasticGD;
use Phpml\Helper\Optimizer\GD;
use Phpml\Classification\Classifier;
use Phpml\Preprocessing\Normalizer;
use Phpml\IncrementalEstimator;

class Perceptron implements Classifier, IncrementalEstimator
{
    use Predictable, OneVsRest;

    /**
     * @var \Phpml\Helper\Optimizer\Optimizer
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
    protected $weights;

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
     * @var array
     */
    protected $costValues = [];

    /**
     * Initalize a perceptron classifier with given learning rate and maximum
     * number of iterations used while training the perceptron
     *
     * @param float $learningRate    Value between 0.0(exclusive) and 1.0(inclusive)
     * @param int   $maxIterations   Must be at least 1
     * @param bool  $normalizeInputs
     *
     * @throws \Exception
     */
    public function __construct(float $learningRate = 0.001, int $maxIterations = 1000, bool $normalizeInputs = true)
    {
        if ($learningRate <= 0.0 || $learningRate > 1.0) {
            throw new \Exception("Learning rate should be a float value between 0.0(exclusive) and 1.0(inclusive)");
        }

        if ($maxIterations <= 0) {
            throw new \Exception("Maximum number of iterations must be an integer greater than 0");
        }

        if ($normalizeInputs) {
            $this->normalizer = new Normalizer(Normalizer::NORM_STD);
        }

        $this->learningRate = $learningRate;
        $this->maxIterations = $maxIterations;
    }

    /**
     * @param array $samples
     * @param array $targets
     * @param array $labels
     */
    public function partialTrain(array $samples, array $targets, array $labels = [])
    {
        $this->trainByLabel($samples, $targets, $labels);
    }

   /**
     * @param array $samples
     * @param array $targets
     * @param array $labels
     */
    public function trainBinary(array $samples, array $targets, array $labels)
    {
        if ($this->normalizer) {
            $this->normalizer->transform($samples);
        }

        // Set all target values to either -1 or 1
        $this->labels = [1 => $labels[0], -1 => $labels[1]];
        foreach ($targets as $key => $target) {
            $targets[$key] = strval($target) == strval($this->labels[1]) ? 1 : -1;
        }

        // Set samples and feature count vars
        $this->featureCount = count($samples[0]);

        $this->runTraining($samples, $targets);
    }

    protected function resetBinary()
    {
        $this->labels = [];
        $this->optimizer = null;
        $this->featureCount = 0;
        $this->weights = null;
        $this->costValues = [];
    }

    /**
     * Normally enabling early stopping for the optimization procedure may
     * help saving processing time while in some cases it may result in
     * premature convergence.<br>
     *
     * If "false" is given, the optimization procedure will always be executed
     * for $maxIterations times
     *
     * @param bool $enable
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
     *
     * @return array
     */
    public function getCostValues()
    {
        return $this->costValues;
    }

    /**
     * Trains the perceptron model with Stochastic Gradient Descent optimization
     * to get the correct set of weights
     *
     * @param array $samples
     * @param array $targets
     */
    protected function runTraining(array $samples, array $targets)
    {
        // The cost function is the sum of squares
        $callback = function ($weights, $sample, $target) {
            $this->weights = $weights;

            $prediction = $this->outputClass($sample);
            $gradient = $prediction - $target;
            $error = $gradient**2;

            return [$error, $gradient];
        };

        $this->runGradientDescent($samples, $targets, $callback);
    }

    /**
     * Executes a Gradient Descent algorithm for
     * the given cost function
     *
     * @param array    $samples
     * @param array    $targets
     * @param \Closure $gradientFunc
     * @param bool     $isBatch
     */
    protected function runGradientDescent(array $samples, array $targets, \Closure $gradientFunc, bool $isBatch = false)
    {
        $class = $isBatch ? GD::class : StochasticGD::class;

        if (empty($this->optimizer)) {
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
     *
     * @param array $sample
     *
     * @return array
     */
    protected function checkNormalizedSample(array $sample)
    {
        if ($this->normalizer) {
            $samples = [$sample];
            $this->normalizer->transform($samples);
            $sample = $samples[0];
        }

        return $sample;
    }

    /**
     * Calculates net output of the network as a float value for the given input
     *
     * @param array $sample
     * @return int
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
     *
     * @param array $sample
     * @return int
     */
    protected function outputClass(array $sample)
    {
        return $this->output($sample) > 0 ? 1 : -1;
    }

    /**
     * Returns the probability of the sample of belonging to the given label.
     *
     * The probability is simply taken as the distance of the sample
     * to the decision plane.
     *
     * @param array $sample
     * @param mixed $label
     *
     * @return float
     */
    protected function predictProbability(array $sample, $label)
    {
        $predicted = $this->predictSampleBinary($sample);

        if (strval($predicted) == strval($label)) {
            $sample = $this->checkNormalizedSample($sample);
            return abs($this->output($sample));
        }

        return 0.0;
    }

    /**
     * @param array $sample
     *
     * @return mixed
     */
    protected function predictSampleBinary(array $sample)
    {
        $sample = $this->checkNormalizedSample($sample);

        $predictedClass = $this->outputClass($sample);

        return $this->labels[$predictedClass];
    }
}
