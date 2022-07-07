<?php

declare(strict_types=1);

namespace Phpml\Classification\Linear;

use Phpml\Exception\InvalidArgumentException;

class Adaline extends Perceptron
{
    /**
     * Batch training is the default Adaline training algorithm
     */
    public const BATCH_TRAINING = 1;

    /**
     * Online training: Stochastic gradient descent learning
     */
    public const ONLINE_TRAINING = 2;

    /**
     * Training type may be either 'Batch' or 'Online' learning
     *
     * @var string|int
     */
    protected $trainingType;

    /**
     * Initalize an Adaline (ADAptive LInear NEuron) classifier with given learning rate and maximum
     * number of iterations used while training the classifier <br>
     *
     * Learning rate should be a float value between 0.0(exclusive) and 1.0 (inclusive) <br>
     * Maximum number of iterations can be an integer value greater than 0 <br>
     * If normalizeInputs is set to true, then every input given to the algorithm will be standardized
     * by use of standard deviation and mean calculation
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        float $learningRate = 0.001,
        int $maxIterations = 1000,
        bool $normalizeInputs = true,
        int $trainingType = self::BATCH_TRAINING
    ) {
        if (!in_array($trainingType, [self::BATCH_TRAINING, self::ONLINE_TRAINING], true)) {
            throw new InvalidArgumentException('Adaline can only be trained with batch and online/stochastic gradient descent algorithm');
        }

        $this->trainingType = $trainingType;

        parent::__construct($learningRate, $maxIterations, $normalizeInputs);
    }

    /**
     * Adapts the weights with respect to given samples and targets
     * by use of gradient descent learning rule
     */
    protected function runTraining(array $samples, array $targets): void
    {
        // The cost function is the sum of squares
        $callback = function ($weights, $sample, $target) {
            $this->weights = $weights;

            $output = $this->output($sample);
            $gradient = $output - $target;
            $error = $gradient ** 2;

            return [$error, $gradient];
        };

        $isBatch = $this->trainingType == self::BATCH_TRAINING;

        parent::runGradientDescent($samples, $targets, $callback, $isBatch);
    }
}
