<?php

declare(strict_types=1);

namespace Phpml\Clustering\KMeans;

use IteratorAggregate;
use Countable;
use SplObjectStorage;
use LogicException;

class Cluster extends Point implements IteratorAggregate, Countable
{
    /**
     * @var Space
     */
    protected $space;

    /**
     * @var SplObjectStorage|Point[]
     */
    protected $points;

    /**
     * @param Space $space
     * @param array $coordinates
     */
    public function __construct(Space $space, array $coordinates)
    {
        parent::__construct($coordinates);
        $this->space = $space;
        $this->points = new SplObjectStorage();
    }

    /**
     * @return array
     */
    public function getPoints()
    {
        $points = [];
        foreach ($this->points as $point) {
            $points[] = $point->toArray();
        }

        return $points;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'centroid' => parent::toArray(),
            'points' => $this->getPoints(),
        ];
    }

    /**
     * @param Point $point
     *
     * @return Point
     *
     * @throws \LogicException
     */
    public function attach(Point $point)
    {
        if ($point instanceof self) {
            throw new LogicException('cannot attach a cluster to another');
        }

        $this->points->attach($point);

        return $point;
    }

    /**
     * @param Point $point
     *
     * @return Point
     */
    public function detach(Point $point)
    {
        $this->points->detach($point);

        return $point;
    }

    /**
     * @param SplObjectStorage $points
     */
    public function attachAll(SplObjectStorage $points)
    {
        $this->points->addAll($points);
    }

    /**
     * @param SplObjectStorage $points
     */
    public function detachAll(SplObjectStorage $points)
    {
        $this->points->removeAll($points);
    }

    public function updateCentroid()
    {
        if (!$count = count($this->points)) {
            return;
        }

        $centroid = $this->space->newPoint(array_fill(0, $this->dimension, 0));

        foreach ($this->points as $point) {
            for ($n = 0; $n < $this->dimension; ++$n) {
                $centroid->coordinates[$n] += $point->coordinates[$n];
            }
        }

        for ($n = 0; $n < $this->dimension; ++$n) {
            $this->coordinates[$n] = $centroid->coordinates[$n] / $count;
        }
    }

    /**
     * @return Point[]|SplObjectStorage
     */
    public function getIterator()
    {
        return $this->points;
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return count($this->points);
    }
    
    /**
    * @param array $newCoordinates
    */
    public function setCoordinates(array $newCoordinates)
    {
        $this->coordinates = $newCoordinates;
    }
}
