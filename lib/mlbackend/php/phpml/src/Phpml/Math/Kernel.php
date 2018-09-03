<?php

declare(strict_types=1);

namespace Phpml\Math;

interface Kernel
{
    /**
     * @param float $a
     * @param float $b
     *
     * @return float
     */
    public function compute($a, $b);
}
