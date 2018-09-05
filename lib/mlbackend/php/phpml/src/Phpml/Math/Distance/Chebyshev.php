<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance;

class Chebyshev implements Distance
{
    /**
     * @param array $a
     * @param array $b
     *
     * @return float
     *
     * @throws InvalidArgumentException
     */
    public function distance(array $a, array $b): float
    {
        if (count($a) !== count($b)) {
            throw InvalidArgumentException::arraySizeNotMatch();
        }

        $differences = [];
        $count = count($a);

        for ($i = 0; $i < $count; ++$i) {
            $differences[] = abs($a[$i] - $b[$i]);
        }

        return max($differences);
    }
}
