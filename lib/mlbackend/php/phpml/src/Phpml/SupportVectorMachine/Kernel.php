<?php

declare(strict_types=1);

namespace Phpml\SupportVectorMachine;

abstract class Kernel
{
    /**
     * u'*v.
     */
    public const LINEAR = 0;

    /**
     * (gamma*u'*v + coef0)^degree.
     */
    public const POLYNOMIAL = 1;

    /**
     * exp(-gamma*|u-v|^2).
     */
    public const RBF = 2;

    /**
     * tanh(gamma*u'*v + coef0).
     */
    public const SIGMOID = 3;
}
