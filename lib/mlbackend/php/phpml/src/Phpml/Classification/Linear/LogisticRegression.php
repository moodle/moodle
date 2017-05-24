<?php

declare(strict_types=1);

namespace Phpml\Classification\Linear;

use Phpml\Helper\Optimizer\ConjugateGradient;

class LogisticRegression extends Adaline
{
    /**
     * Batch training: Gradient descent algorithm (default)
     */
    const BATCH_TRAINING = 1;

    /**
     * Online training: Stochastic gradient descent learning
     */
    const ONLINE_TRAINING = 2;

    /**
     * Conjugate Batch: Conjugate Gradient algorithm
     */
    const CONJUGATE_GRAD_TRAINING = 3;

    /**
     * Cost function to optimize: 'log' and 'sse' are supported <br>
     *  - 'log' : log likelihood <br>
     *  - 'sse' : sum of squared errors <br>
     *
     * @var string
     */
    protected $costFunction = 'sse';

    /**
     * Regularization term: only 'L2' is supported
     *
     * @var string
     */
    protected $penalty = 'L2';

    /**
     * Lambda (λ) parameter of regularization term. If λ is set to 0, then
     * regularization term is cancelled.
     *
     * @var float
     */
    protected $lambda = 0.5;

    /**
     * Initalize a Logistic Regression classifier with maximum number of iterations
     * and learning rule to be applied <br>
     *
     * Maximum number of iterations can be an integer value greater than 0 <br>
     * If normalizeInputs is set to true, then every input given to the algorithm will be standardized
     * by use of standard deviation and mean calculation <br>
     *
     * Cost function can be 'log' for log-likelihood and 'sse' for sum of squared errors <br>
     *
     * Penalty (Regularization term) can be 'L2' or empty string to cancel penalty term
     *
     * @param int $maxIterations
     * @param bool $normalizeInputs
     * @param int $trainingType
     * @param string $cost
     * @param string $penalty
     *
     * @throws \Exception
     */
    public function __construct(int $maxIterations = 500, bool $normalizeInputs = true,
        int $trainingType = self::CONJUGATE_GRAD_TRAINING, string $cost = 'sse',
        string $penalty = 'L2')
    {
        $trainingTypes = range(self::BATCH_TRAINING, self::CONJUGATE_GRAD_TRAINING);
        if (!in_array($trainingType, $trainingTypes)) {
            throw new \Exception("Logistic regression can only be trained with " .
                "batch (gradient descent), online (stochastic gradient descent) " .
                "or conjugate batch (conjugate gradients) algorithms");
        }

        if (!in_array($cost, ['log', 'sse'])) {
            throw new \Exception("Logistic regression cost function can be one of the following: \n" .
                "'log' for log-likelihood and 'sse' for sum of squared errors");
        }

        if ($penalty != '' && strtoupper($penalty) !== 'L2') {
            throw new \Exception("Logistic regression supports only 'L2' regularization");
        }

        $this->learningRate = 0.001;

        parent::__construct($this->learningRate, $maxIterations, $normalizeInputs);

        $this->trainingType = $trainingType;
        $this->costFunction = $cost;
        $this->penalty = $penalty;
    }

    /**
     * Sets the learning rate if gradient descent algorithm is
     * selected for training
     *
     * @param float $learningRate
     */
    public function setLearningRate(float $learningRate)
    {
        $this->learningRate = $learningRate;
    }

    /**
     * Lambda (λ) parameter of regularization term. If 0 is given,
     * then the regularization term is cancelled
     *
     * @param float $lambda
     */
    public function setLambda(float $lambda)
    {
        $this->lambda = $lambda;
    }

    /**
     * Adapts the weights with respect to given samples and targets
     * by use of selected solver
     *
     * @param array $samples
     * @param array $targets
     *
     * @throws \Exception
     */
    protected function runTraining(array $samples, array $targets)
    {
        $callback = $this->getCostFunction();

        switch ($this->trainingType) {
            case self::BATCH_TRAINING:
                return $this->runGradientDescent($samples, $targets, $callback, true);

            case self::ONLINE_TRAINING:
                return $this->runGradientDescent($samples, $targets, $callback, false);

            case self::CONJUGATE_GRAD_TRAINING:
                return $this->runConjugateGradient($samples, $targets, $callback);

            default:
                throw new \Exception('Logistic regression has invalid training type: %s.', $this->trainingType);
        }
    }

    /**
     * Executes Conjugate Gradient method to optimize the weights of the LogReg model
     *
     * @param array    $samples
     * @param array    $targets
     * @param \Closure $gradientFunc
     */
    protected function runConjugateGradient(array $samples, array $targets, \Closure $gradientFunc)
    {
        if (empty($this->optimizer)) {
            $this->optimizer = (new ConjugateGradient($this->featureCount))
                ->setMaxIterations($this->maxIterations);
        }

        $this->weights = $this->optimizer->runOptimization($samples, $targets, $gradientFunc);
        $this->costValues = $this->optimizer->getCostValues();
    }

    /**
     * Returns the appropriate callback function for the selected cost function
     *
     * @return \Closure
     *
     * @throws \Exception
     */
    protected function getCostFunction()
    {
        $penalty = 0;
        if ($this->penalty == 'L2') {
            $penalty = $this->lambda;
        }

        switch ($this->costFunction) {
            case 'log':
                /*
                 * Negative of Log-likelihood cost function to be minimized:
                 *		J(x) = ∑( - y . log(h(x)) - (1 - y) . log(1 - h(x)))
                 *
                 * If regularization term is given, then it will be added to the cost:
                 *		for L2 : J(x) = J(x) +  λ/m . w
                 *
                 * The gradient of the cost function to be used with gradient descent:
                 *		∇J(x) = -(y - h(x)) = (h(x) - y)
                 */
                $callback = function ($weights, $sample, $y) use ($penalty) {
                    $this->weights = $weights;
                    $hX = $this->output($sample);

                    // In cases where $hX = 1 or $hX = 0, the log-likelihood
                    // value will give a NaN, so we fix these values
                    if ($hX == 1) {
                        $hX = 1 - 1e-10;
                    }
                    if ($hX == 0) {
                        $hX = 1e-10;
                    }
                    $error = -$y * log($hX) - (1 - $y) * log(1 - $hX);
                    $gradient = $hX - $y;

                    return [$error, $gradient, $penalty];
                };

                return $callback;

            case 'sse':
                /*
                 * Sum of squared errors or least squared errors cost function:
                 *		J(x) = ∑ (y - h(x))^2
                 *
                 * If regularization term is given, then it will be added to the cost:
                 *		for L2 : J(x) = J(x) +  λ/m . w
                 *
                 * The gradient of the cost function:
                 *		∇J(x) = -(h(x) - y) . h(x) . (1 - h(x))
                 */
                $callback = function ($weights, $sample, $y) use ($penalty) {
                    $this->weights = $weights;
                    $hX = $this->output($sample);

                    $error = ($y - $hX) ** 2;
                    $gradient = -($y - $hX) * $hX * (1 - $hX);

                    return [$error, $gradient, $penalty];
                };

                return $callback;

            default:
                throw new \Exception(sprintf('Logistic regression has invalid cost function: %s.', $this->costFunction));
        }
    }

    /**
     * Returns the output of the network, a float value between 0.0 and 1.0
     *
     * @param array $sample
     *
     * @return float
     */
    protected function output(array $sample)
    {
        $sum = parent::output($sample);

        return 1.0 / (1.0 + exp(-$sum));
    }

    /**
     * Returns the class value (either -1 or 1) for the given input
     *
     * @param array $sample
     *
     * @return int
     */
    protected function outputClass(array $sample)
    {
        $output = $this->output($sample);

        if (round($output) > 0.5) {
            return 1;
        }

        return -1;
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
            return abs($this->output($sample) - 0.5);
        }

        return 0.0;
    }
}
