<?php

declare(strict_types=1);

namespace Phpml\Clustering\KMeans;

use ArrayAccess;

class Point implements ArrayAccess, \Countable
{
    /**
     * @var int
     */
    protected $dimension;

    /**
     * @var array
     */
    protected $coordinates = [];

    /**
     * @var mixed
     */
    protected $label;

    /**
     * @param mixed $label
     */
    public function __construct(array $coordinates, $label = null)
    {
        $this->dimension = count($coordinates);
        $this->coordinates = $coordinates;
        $this->label = $label;
    }

    public function toArray(): array
    {
        return $this->coordinates;
    }

    /**
     * @return float|int
     */
    public function getDistanceWith(self $point, bool $precise = true)
    {
        $distance = 0;
        for ($n = 0; $n < $this->dimension; ++$n) {
            $difference = $this->coordinates[$n] - $point->coordinates[$n];
            $distance += $difference * $difference;
        }

        return $precise ? $distance ** .5 : $distance;
    }

    /**
     * @param Point[] $points
     */
    public function getClosest(array $points): ?self
    {
        $minPoint = null;

        foreach ($points as $point) {
            $distance = $this->getDistanceWith($point, false);

            if (!isset($minDistance)) {
                $minDistance = $distance;
                $minPoint = $point;

                continue;
            }

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $minPoint = $point;
            }
        }

        return $minPoint;
    }

    public function getCoordinates(): array
    {
        return $this->coordinates;
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->coordinates[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->coordinates[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->coordinates[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->coordinates[$offset]);
    }

    public function count(): int
    {
        return count($this->coordinates);
    }
}
