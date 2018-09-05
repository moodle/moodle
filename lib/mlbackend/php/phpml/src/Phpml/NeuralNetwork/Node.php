<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork;

interface Node
{
    /**
     * @return float
     */
    public function getOutput(): float;
}
