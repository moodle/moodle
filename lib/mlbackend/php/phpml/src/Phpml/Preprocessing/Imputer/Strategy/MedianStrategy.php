<?php

declare(strict_types=1);

namespace Phpml\Preprocessing\Imputer\Strategy;

use Phpml\Preprocessing\Imputer\Strategy;
use Phpml\Math\Statistic\Mean;

class MedianStrategy implements Strategy
{
    /**
     * @param array $currentAxis
     *
     * @return float
     */
    public function replaceValue(array $currentAxis)
    {
        return Mean::median($currentAxis);
    }
}
