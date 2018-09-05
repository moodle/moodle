<?php

declare(strict_types=1);

namespace Phpml\Math;

interface Distance
{
    /**
     * @param array $a
     * @param array $b
     *
     * @return float
     */
    public function distance(array $a, array $b): float;
}
