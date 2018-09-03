<?php

declare(strict_types=1);

namespace Phpml\Preprocessing\Imputer\Strategy;

use Phpml\Preprocessing\Imputer\Strategy;
use Phpml\Math\Statistic\Mean;

class MeanStrategy implements Strategy
{
    /**
     * @param array $currentAxis
     *
     * @return float
     */
    public function replaceValue(array $currentAxis)
    {
        return Mean::arithmetic($currentAxis);
    }
}
