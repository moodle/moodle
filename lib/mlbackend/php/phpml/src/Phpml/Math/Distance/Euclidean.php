<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

/**
 * Class Euclidean
 *
 * L^2 Metric.
 */
class Euclidean extends Distance
{
    /**
     * Euclidean constructor.
     */
    public function __construct()
    {
        parent::__construct(2.0);
    }

    /**
     * Square of Euclidean distance
     *
     * @throws \Phpml\Exception\InvalidArgumentException
     */
    public function sqDistance(array $a, array $b): float
    {
        return $this->distance($a, $b) ** 2;
    }
}
