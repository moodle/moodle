<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

/**
 * Class Manhattan
 *
 * L^1 Metric.
 */
class Manhattan extends Distance
{
    /**
     * Manhattan constructor.
     */
    public function __construct()
    {
        parent::__construct(1.0);
    }
}
