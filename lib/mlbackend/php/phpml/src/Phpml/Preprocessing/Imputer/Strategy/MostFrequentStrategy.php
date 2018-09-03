<?php

declare(strict_types=1);

namespace Phpml\Preprocessing\Imputer\Strategy;

use Phpml\Preprocessing\Imputer\Strategy;
use Phpml\Math\Statistic\Mean;

class MostFrequentStrategy implements Strategy
{
    /**
     * @param array $currentAxis
     *
     * @return float|mixed
     */
    public function replaceValue(array $currentAxis)
    {
        return Mean::mode($currentAxis);
    }
}
