<?php

declare(strict_types=1);

namespace Phpml\Regression;

use Phpml\Helper\Predictable;
use Phpml\NeuralNetwork\ActivationFunction;
use Phpml\NeuralNetwork\Network\MultilayerPerceptron;
use Phpml\NeuralNetwork\Training\Backpropagation;

class MLPRegressor implements Regression
{
    use Predictable;

    /**
     * @var MultilayerPerceptron
     */
    private $perceptron;

    /**
     * @var array
     */
    private $hiddenLayers;

    /**
     * @var float
     */
    private $desiredError;

    /**
     * @var int
     */
    private $maxIterations;

    /**
     * @var ActivationFunction
     */
    private $activationFunction;

    /**
     * @param array              $hiddenLayers
     * @param float              $desiredError
     * @param int                $maxIterations
     * @param ActivationFunction $activationFunction
     */
    public function __construct(array $hiddenLayers = [10], float $desiredError = 0.01, int $maxIterations = 10000, ActivationFunction $activationFunction = null)
    {
        $this->hiddenLayers = $hiddenLayers;
        $this->desiredError = $desiredError;
        $this->maxIterations = $maxIterations;
        $this->activationFunction = $activationFunction;
    }

    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets)
    {
        $layers = $this->hiddenLayers;
        array_unshift($layers, count($samples[0]));
        $layers[] = count($targets[0]);

        $this->perceptron = new MultilayerPerceptron($layers, $this->activationFunction);

        $trainer = new Backpropagation($this->perceptron);
        $trainer->train($samples, $targets, $this->desiredError, $this->maxIterations);
    }

    /**
     * @param array $sample
     *
     * @return array
     */
    protected function predictSample(array $sample)
    {
        return $this->perceptron->setInput($sample)->getOutput();
    }
}
