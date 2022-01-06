<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork;

interface Node
{
    public function getOutput(): float;
}
