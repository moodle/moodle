<?php

declare(strict_types=1);

namespace Phpml\SupportVectorMachine;

abstract class Type
{
    /**
     * classification.
     */
    const C_SVC = 0;

    /**
     * classification.
     */
    const NU_SVC = 1;

    /**
     * distribution estimation.
     */
    const ONE_CLASS_SVM = 2;

    /**
     * regression.
     */
    const EPSILON_SVR = 3;

    /**
     * regression.
     */
    const NU_SVR = 4;
}
