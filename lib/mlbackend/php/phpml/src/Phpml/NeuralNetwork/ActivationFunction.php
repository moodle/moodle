<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork;

interface ActivationFunction
{
    /**
     * @param float|int $value
     *
     * @return float
     */
    public function compute($value): float;
}
