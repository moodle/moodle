<?php

declare(strict_types=1);

namespace Phpml\Helper\Optimizer;

/**
 * Stochastic Gradient Descent optimization method
 * to find a solution for the equation A.ϴ = y where
 *  A (samples) and y (targets) are known and ϴ is unknown.
 */
class StochasticGD extends Optimizer
{
    /**
     * A (samples)
     *
     * @var array
     */
    protected $samples = [];

    /**
     * y (targets)
     *
     * @var array
     */
    protected $targets = [];

    /**
     * Callback function to get the gradient and cost value
     * for a specific set of theta (ϴ) and a pair of sample & target
     *
     * @var \Closure
     */
    protected $gradientCb = null;

    /**
     * Maximum number of iterations used to train the model
     *
     * @var int
     */
    protected $maxIterations = 1000;

    /**
     * Learning rate is used to control the speed of the optimization.<br>
     *
     * Larger values of lr may overshoot the optimum or even cause divergence
     * while small values slows down the convergence and increases the time
     * required for the training
     *
     * @var float
     */
    protected $learningRate = 0.001;

    /**
     * Minimum amount of change in the weights and error values
     * between iterations that needs to be obtained to continue the training
     *
     * @var float
     */
    protected $threshold = 1e-4;

    /**
     * Enable/Disable early stopping by checking the weight & cost values
     * to see whether they changed large enough to continue the optimization
     *
     * @var bool
     */
    protected $enableEarlyStop = true;
    /**
     * List of values obtained by evaluating the cost function at each iteration
     * of the algorithm
     *
     * @var array
     */
    protected $costValues= [];

    /**
     * Initializes the SGD optimizer for the given number of dimensions
     *
     * @param int $dimensions
     */
    public function __construct(int $dimensions)
    {
        // Add one more dimension for the bias
        parent::__construct($dimensions + 1);

        $this->dimensions = $dimensions;
    }

    /**
     * Sets minimum value for the change in the theta values
     * between iterations to continue the iterations.<br>
     *
     * If change in the theta is less than given value then the
     * algorithm will stop training
     *
     * @param float $threshold
     *
     * @return $this
     */
    public function setChangeThreshold(float $threshold = 1e-5)
    {
        $this->threshold = $threshold;

        return $this;
    }

    /**
     * Enable/Disable early stopping by checking at each iteration
     * whether changes in theta or cost value are not large enough
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
     * @param float $learningRate
     *
     * @return $this
     */
    public function setLearningRate(float $learningRate)
    {
        $this->learningRate = $learningRate;

        return $this;
    }

    /**
     * @param int $maxIterations
     *
     * @return $this
     */
    public function setMaxIterations(int $maxIterations)
    {
        $this->maxIterations = $maxIterations;

        return $this;
    }

    /**
     * Optimization procedure finds the unknow variables for the equation A.ϴ = y
     * for the given samples (A) and targets (y).<br>
     *
     * The cost function to minimize and the gradient of the function are to be
     * handled by the callback function provided as the third parameter of the method.
     *
     * @param array $samples
     * @param array $targets
     * @param \Closure $gradientCb
     *
     * @return array
     */
    public function runOptimization(array $samples, array $targets, \Closure $gradientCb)
    {
        $this->samples = $samples;
        $this->targets = $targets;
        $this->gradientCb = $gradientCb;

        $currIter = 0;
        $bestTheta = null;
        $bestScore = 0.0;
        $this->costValues = [];

        while ($this->maxIterations > $currIter++) {
            $theta = $this->theta;

            // Update the guess
            $cost = $this->updateTheta();

            // Save the best theta in the "pocket" so that
            // any future set of theta worse than this will be disregarded
            if ($bestTheta == null || $cost <= $bestScore) {
                $bestTheta = $theta;
                $bestScore = $cost;
            }

            // Add the cost value for this iteration to the list
            $this->costValues[] = $cost;

            // Check for early stop
            if ($this->enableEarlyStop && $this->earlyStop($theta)) {
                break;
            }
        }

        $this->clear();

        // Solution in the pocket is better than or equal to the last state
        // so, we use this solution
        return $this->theta = $bestTheta;
    }

    /**
     * @return float
     */
    protected function updateTheta()
    {
        $jValue = 0.0;
        $theta = $this->theta;

        foreach ($this->samples as $index => $sample) {
            $target = $this->targets[$index];

            $result = ($this->gradientCb)($theta, $sample, $target);

            list($error, $gradient, $penalty) = array_pad($result, 3, 0);

            // Update bias
            $this->theta[0] -= $this->learningRate * $gradient;

            // Update other values
            for ($i = 1; $i <= $this->dimensions; ++$i) {
                $this->theta[$i] -= $this->learningRate *
                    ($gradient * $sample[$i - 1] + $penalty * $this->theta[$i]);
            }

            // Sum error rate
            $jValue += $error;
        }

        return $jValue / count($this->samples);
    }

    /**
     * Checks if the optimization is not effective enough and can be stopped
     * in case large enough changes in the solution do not happen
     *
     * @param array $oldTheta
     *
     * @return boolean
     */
    protected function earlyStop($oldTheta)
    {
        // Check for early stop: No change larger than threshold (default 1e-5)
        $diff = array_map(
            function ($w1, $w2) {
                return abs($w1 - $w2) > $this->threshold ? 1 : 0;
            },
            $oldTheta, $this->theta);

        if (array_sum($diff) == 0) {
            return true;
        }

        // Check if the last two cost values are almost the same
        $costs = array_slice($this->costValues, -2);
        if (count($costs) == 2 && abs($costs[1] - $costs[0]) < $this->threshold) {
            return true;
        }

        return false;
    }

    /**
     * Returns the list of cost values for each iteration executed in
     * last run of the optimization
     *
     * @return array
     */
    public function getCostValues()
    {
        return $this->costValues;
    }

    /**
     * Clears the optimizer internal vars after the optimization process.
     *
     * @return void
     */
    protected function clear()
    {
        $this->samples = [];
        $this->targets = [];
        $this->gradientCb = null;
    }
}
