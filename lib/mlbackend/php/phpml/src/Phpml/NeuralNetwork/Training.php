<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork;

interface Training
{
    /**
     * @param array $samples
     * @param array $targets
     * @param float $desiredError
     * @param int   $maxIterations
     */
    public function train(array $samples, array $targets, float $desiredError = 0.001, int $maxIterations = 10000);
}
