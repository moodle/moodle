<?php

declare(strict_types=1);

namespace Phpml\Preprocessing\Imputer\Strategy;

use Phpml\Math\Statistic\Mean;
use Phpml\Preprocessing\Imputer\Strategy;

class MedianStrategy implements Strategy
{
    public function replaceValue(array $currentAxis): float
    {
        return Mean::median($currentAxis);
    }
}
