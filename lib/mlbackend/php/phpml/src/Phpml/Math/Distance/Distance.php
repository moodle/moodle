<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance as DistanceInterface;

/**
 * Class Distance
 */
abstract class Distance implements DistanceInterface
{
    /**
     * @var float|int
     */
    public $norm;

    /**
     * Distance constructor.
     */
    public function __construct(float $norm = 3.0)
    {
        $this->norm = $norm;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function distance(array $a, array $b): float
    {
        $distance = 0;

        foreach ($this->deltas($a, $b) as $delta) {
            $distance += $delta ** $this->norm;
        }

        return $distance ** (1 / $this->norm);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function deltas(array $a, array $b): array
    {
        $count = count($a);

        if ($count !== count($b)) {
            throw new InvalidArgumentException('Size of given arrays does not match');
        }

        $deltas = [];

        for ($i = 0; $i < $count; $i++) {
            $deltas[] = abs($a[$i] - $b[$i]);
        }

        return $deltas;
    }
}
