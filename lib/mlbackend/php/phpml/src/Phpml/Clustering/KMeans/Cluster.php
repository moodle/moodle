<?php

declare(strict_types=1);

namespace Phpml\Clustering\KMeans;

use IteratorAggregate;
use LogicException;
use SplObjectStorage;

class Cluster extends Point implements IteratorAggregate
{
    /**
     * @var Space
     */
    protected $space;

    /**
     * @var SplObjectStorage|Point[]
     */
    protected $points;

    public function __construct(Space $space, array $coordinates)
    {
        parent::__construct($coordinates);
        $this->space = $space;
        $this->points = new SplObjectStorage();
    }

    public function getPoints(): array
    {
        $points = [];
        foreach ($this->points as $point) {
            if ($point->label === null) {
                $points[] = $point->toArray();
            } else {
                $points[$point->label] = $point->toArray();
            }
        }

        return $points;
    }

    public function toArray(): array
    {
        return [
            'centroid' => parent::toArray(),
            'points' => $this->getPoints(),
        ];
    }

    public function attach(Point $point): Point
    {
        if ($point instanceof self) {
            throw new LogicException('Cannot attach a cluster to another');
        }

        $this->points->attach($point);

        return $point;
    }

    public function detach(Point $point): Point
    {
        $this->points->detach($point);

        return $point;
    }

    public function attachAll(SplObjectStorage $points): void
    {
        $this->points->addAll($points);
    }

    public function detachAll(SplObjectStorage $points): void
    {
        $this->points->removeAll($points);
    }

    public function updateCentroid(): void
    {
        $count = count($this->points);
        if ($count === 0) {
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

    public function count(): int
    {
        return count($this->points);
    }

    public function setCoordinates(array $newCoordinates): void
    {
        $this->coordinates = $newCoordinates;
    }
}
