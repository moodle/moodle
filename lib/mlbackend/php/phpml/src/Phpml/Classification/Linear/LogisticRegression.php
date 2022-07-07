<?php

declare(strict_types=1);

namespace Phpml\Classification\Linear;

use Closure;
use Exception;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Helper\Optimizer\ConjugateGradient;

class LogisticRegression extends Adaline
{
    /**
     * Batch training: Gradient descent algorithm (default)
     */
    public const BATCH_TRAINING = 1;

    /**
     * Online training: Stochastic gradient descent learning
     */
    public const ONLINE_TRAINING = 2;

    /**
     * Conjugate Batch: Conjugate Gradient algorithm
     */
    public const CONJUGATE_GRAD_TRAINING = 3;

    /**
     * Cost function to optimize: 'log' and 'sse' are supported <br>
     *  - 'log' : log likelihood <br>
     *  - 'sse' : sum of squared errors <br>
     *
     * @var string
     */
    protected $costFunction = 'log';

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
     * @throws InvalidArgumentException
     */
    public function __construct(
        int $maxIterations = 500,
        bool $normalizeInputs = true,
        int $trainingType = self::CONJUGATE_GRAD_TRAINING,
        string $cost = 'log',
        string $penalty = 'L2'
    ) {
        $trainingTypes = range(self::BATCH_TRAINING, self::CONJUGATE_GRAD_TRAINING);
        if (!in_array($trainingType, $trainingTypes, true)) {
            throw new InvalidArgumentException(
                'Logistic regression can only be trained with '.
                'batch (gradient descent), online (stochastic gradient descent) '.
                'or conjugate batch (conjugate gradients) algorithms'
            );
        }

        if (!in_array($cost, ['log', 'sse'], true)) {
            throw new InvalidArgumentException(
                "Logistic regression cost function can be one of the following: \n".
                "'log' for log-likelihood and 'sse' for sum of squared errors"
            );
        }

        if ($penalty !== '' && strtoupper($penalty) !== 'L2') {
            throw new InvalidArgumentException('Logistic regression supports only \'L2\' regularization');
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
     */
    public function setLearningRate(float $learningRate): void
    {
        $this->learningRate = $learningRate;
    }

    /**
     * Lambda (λ) parameter of regularization term. If 0 is given,
     * then the regularization term is cancelled
     */
    public function setLambda(float $lambda): void
    {
        $this->lambda = $lambda;
    }

    /**
     * Adapts the weights with respect to given samples and targets
     * by use of selected solver
     *
     * @throws \Exception
     */
    protected function runTraining(array $samples, array $targets): void
    {
        $callback = $this->getCostFunction();

        switch ($this->trainingType) {
            case self::BATCH_TRAINING:
                $this->runGradientDescent($samples, $targets, $callback, true);

                return;

            case self::ONLINE_TRAINING:
                $this->runGradientDescent($samples, $targets, $callback, false);

                return;

            case self::CONJUGATE_GRAD_TRAINING:
                $this->runConjugateGradient($samples, $targets, $callback);

                return;

            default:
                // Not reached
                throw new Exception(sprintf('Logistic regression has invalid training type: %d.', $this->trainingType));
        }
    }

    /**
     * Executes Conjugate Gradient method to optimize the weights of the LogReg model
     */
    protected function runConjugateGradient(array $samples, array $targets, Closure $gradientFunc): void
    {
        if ($this->optimizer === null) {
            $this->optimizer = (new ConjugateGradient($this->featureCount))
                ->setMaxIterations($this->maxIterations);
        }

        $this->weights = $this->optimizer->runOptimization($samples, $targets, $gradientFunc);
        $this->costValues = $this->optimizer->getCostValues();
    }

    /**
     * Returns the appropriate callback function for the selected cost function
     *
     * @throws \Exception
     */
    protected function getCostFunction(): Closure
    {
        $penalty = 0;
        if ($this->penalty === 'L2') {
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
                return function ($weights, $sample, $y) use ($penalty) {
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

                    $y = $y < 0 ? 0 : 1;

                    $error = -$y * log($hX) - (1 - $y) * log(1 - $hX);
                    $gradient = $hX - $y;

                    return [$error, $gradient, $penalty];
                };
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
                return function ($weights, $sample, $y) use ($penalty) {
                    $this->weights = $weights;
                    $hX = $this->output($sample);

                    $y = $y < 0 ? 0 : 1;

                    $error = ($y - $hX) ** 2;
                    $gradient = -($y - $hX) * $hX * (1 - $hX);

                    return [$error, $gradient, $penalty];
                };
            default:
                // Not reached
                throw new Exception(sprintf('Logistic regression has invalid cost function: %s.', $this->costFunction));
        }
    }

    /**
     * Returns the output of the network, a float value between 0.0 and 1.0
     */
    protected function output(array $sample): float
    {
        $sum = parent::output($sample);

        return 1.0 / (1.0 + exp(-$sum));
    }

    /**
     * Returns the class value (either -1 or 1) for the given input
     */
    protected function outputClass(array $sample): int
    {
        $output = $this->output($sample);

        if ($output > 0.5) {
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
     * @param mixed $label
     */
    protected function predictProbability(array $sample, $label): float
    {
        $sample = $this->checkNormalizedSample($sample);
        $probability = $this->output($sample);

        if (array_search($label, $this->labels, true) > 0) {
            return $probability;
        }

        return 1 - $probability;
    }
}
