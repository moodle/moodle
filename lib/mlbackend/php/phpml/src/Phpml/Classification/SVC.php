<?php

declare(strict_types=1);

namespace Phpml\Classification;

use Phpml\SupportVectorMachine\Kernel;
use Phpml\SupportVectorMachine\SupportVectorMachine;
use Phpml\SupportVectorMachine\Type;

class SVC extends SupportVectorMachine implements Classifier
{
    public function __construct(
        int $kernel = Kernel::RBF,
        float $cost = 1.0,
        int $degree = 3,
        ?float $gamma = null,
        float $coef0 = 0.0,
        float $tolerance = 0.001,
        int $cacheSize = 100,
        bool $shrinking = true,
        bool $probabilityEstimates = false
    ) {
        parent::__construct(Type::C_SVC, $kernel, $cost, 0.5, $degree, $gamma, $coef0, 0.1, $tolerance, $cacheSize, $shrinking, $probabilityEstimates);
    }
}
