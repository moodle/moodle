<?php

declare(strict_types=1);

namespace Phpml\Preprocessing\Imputer\Strategy;

use Phpml\Math\Statistic\Mean;
use Phpml\Preprocessing\Imputer\Strategy;

class MostFrequentStrategy implements Strategy
{
    /**
     * @return float|mixed
     */
    public function replaceValue(array $currentAxis)
    {
        return Mean::mode($currentAxis);
    }
}
