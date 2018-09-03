<?php

declare(strict_types=1);

namespace Phpml\Clustering\KMeans;

use ArrayAccess;

class Point implements ArrayAccess
{
    /**
     * @var int
     */
    protected $dimension;

    /**
     * @var array
     */
    protected $coordinates;

    /**
     * @param array $coordinates
     */
    public function __construct(array $coordinates)
    {
        $this->dimension = count($coordinates);
        $this->coordinates = $coordinates;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->coordinates;
    }

    /**
     * @param Point $point
     * @param bool  $precise
     *
     * @return int|mixed
     */
    public function getDistanceWith(self $point, $precise = true)
    {
        $distance = 0;
        for ($n = 0; $n < $this->dimension; ++$n) {
            $difference = $this->coordinates[$n] - $point->coordinates[$n];
            $distance += $difference * $difference;
        }

        return $precise ? sqrt((float) $distance) : $distance;
    }

    /**
     * @param array $points
     *
     * @return mixed
     */
    public function getClosest(array $points)
    {
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

    /**
     * @return array
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
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
    public function offsetSet($offset, $value)
    {
        $this->coordinates[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->coordinates[$offset]);
    }
}
