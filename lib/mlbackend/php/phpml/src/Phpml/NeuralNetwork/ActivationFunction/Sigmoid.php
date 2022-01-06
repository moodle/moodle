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

    public function __construct(float $beta = 1.0)
    {
        $this->beta = $beta;
    }

    /**
     * @param float|int $value
     */
    public function compute($value): float
    {
        return 1 / (1 + exp(-$this->beta * $value));
    }

    /**
     * @param float|int $value
     * @param float|int $computedvalue
     */
    public function differentiate($value, $computedvalue): float
    {
        return $computedvalue * (1 - $computedvalue);
    }
}
