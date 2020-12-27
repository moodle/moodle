<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\Node;

use Phpml\NeuralNetwork\Node;

class Bias implements Node
{
    public function getOutput(): float
    {
        return 1.0;
    }
}
