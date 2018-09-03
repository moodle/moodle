<?php

declare(strict_types=1);

namespace Phpml;

interface Estimator
{
    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets);

    /**
     * @param array $samples
     *
     * @return mixed
     */
    public function predict(array $samples);
}
