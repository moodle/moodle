<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\ActivationFunction;

use Phpml\NeuralNetwork\ActivationFunction;

class Sigmoid implements ActivationFunction
{
    /**
     * @var float
     */
    private $beta;

    /**
     * @param float $beta
     */
    public function __construct($beta = 1.0)
    {
        $this->beta = $beta;
    }

    /**
     * @param float|int $value
     *
     * @return float
     */
    public function compute($value): float
    {
        return 1 / (1 + exp(-$this->beta * $value));
    }
}
