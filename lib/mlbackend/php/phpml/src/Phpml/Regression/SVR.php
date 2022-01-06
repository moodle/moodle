<?php

declare(strict_types=1);

namespace Phpml\Regression;

use Phpml\SupportVectorMachine\Kernel;
use Phpml\SupportVectorMachine\SupportVectorMachine;
use Phpml\SupportVectorMachine\Type;

class SVR extends SupportVectorMachine implements Regression
{
    public function __construct(
        int $kernel = Kernel::RBF,
        int $degree = 3,
        float $epsilon = 0.1,
        float $cost = 1.0,
        ?float $gamma = null,
        float $coef0 = 0.0,
        float $tolerance = 0.001,
        int $cacheSize = 100,
        bool $shrinking = true
    ) {
        parent::__construct(Type::EPSILON_SVR, $kernel, $cost, 0.5, $degree, $gamma, $coef0, $epsilon, $tolerance, $cacheSize, $shrinking, false);
    }
}
