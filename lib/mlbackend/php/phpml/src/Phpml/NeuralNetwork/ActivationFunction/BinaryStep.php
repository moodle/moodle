<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\ActivationFunction;

use Phpml\NeuralNetwork\ActivationFunction;

class BinaryStep implements ActivationFunction
{
    /**
     * @param float|int $value
     *
     * @return float
     */
    public function compute($value): float
    {
        return $value >= 0 ? 1.0 : 0.0;
    }
}
