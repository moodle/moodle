<?php

declare(strict_types=1);

namespace Phpml\SupportVectorMachine;

abstract class Kernel
{
    /**
     * u'*v.
     */
    const LINEAR = 0;

    /**
     * (gamma*u'*v + coef0)^degree.
     */
    const POLYNOMIAL = 1;

    /**
     * exp(-gamma*|u-v|^2).
     */
    const RBF = 2;

    /**
     * tanh(gamma*u'*v + coef0).
     */
    const SIGMOID = 3;
}
