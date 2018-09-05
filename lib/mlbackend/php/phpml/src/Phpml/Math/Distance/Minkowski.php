<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance;

class Minkowski implements Distance
{
    /**
     * @var float
     */
    private $lambda;

    /**
     * @param float $lambda
     */
    public function __construct(float $lambda = 3.0)
    {
        $this->lambda = $lambda;
    }

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

        $distance = 0;
        $count = count($a);

        for ($i = 0; $i < $count; ++$i) {
            $distance += pow(abs($a[$i] - $b[$i]), $this->lambda);
        }

        return (float)pow($distance, 1 / $this->lambda);
    }
}
