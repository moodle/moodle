<?php

declare(strict_types=1);

namespace Phpml\Clustering\KMeans;

use Phpml\Clustering\KMeans;
use SplObjectStorage;
use LogicException;
use InvalidArgumentException;

class Space extends SplObjectStorage
{
    /**
     * @var int
     */
    protected $dimension;

    /**
     * @param $dimension
     */
    public function __construct($dimension)
    {
        if ($dimension < 1) {
            throw new LogicException('a space dimension cannot be null or negative');
        }

        $this->dimension = $dimension;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $points = [];
        foreach ($this as $point) {
            $points[] = $point->toArray();
        }

        return ['points' => $points];
    }

    /**
     * @param array $coordinates
     *
     * @return Point
     */
    public function newPoint(array $coordinates)
    {
        if (count($coordinates) != $this->dimension) {
            throw new LogicException('('.implode(',', $coordinates).') is not a point of this space');
        }

        return new Point($coordinates);
    }

    /**
     * @param array $coordinates
     * @param null  $data
     */
    public function addPoint(array $coordinates, $data = null)
    {
        $this->attach($this->newPoint($coordinates), $data);
    }

    /**
     * @param Point $point
     * @param null   $data
     */
    public function attach($point, $data = null)
    {
        if (!$point instanceof Point) {
            throw new InvalidArgumentException('can only attach points to spaces');
        }

        parent::attach($point, $data);
    }

    /**
     * @return int
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * @return array|bool
     */
    public function getBoundaries()
    {
        if (!count($this)) {
            return false;
        }

        $min = $this->newPoint(array_fill(0, $this->dimension, null));
        $max = $this->newPoint(array_fill(0, $this->dimension, null));

        foreach ($this as $point) {
            for ($n = 0; $n < $this->dimension; ++$n) {
                ($min[$n] > $point[$n] || $min[$n] === null) && $min[$n] = $point[$n];
                ($max[$n] < $point[$n] || $max[$n] === null) && $max[$n] = $point[$n];
            }
        }

        return [$min, $max];
    }

    /**
     * @param Point $min
     * @param Point $max
     *
     * @return Point
     */
    public function getRandomPoint(Point $min, Point $max)
    {
        $point = $this->newPoint(array_fill(0, $this->dimension, null));

        for ($n = 0; $n < $this->dimension; ++$n) {
            $point[$n] = random_int($min[$n], $max[$n]);
        }

        return $point;
    }

    /**
     * @param int $clustersNumber
     * @param int $initMethod
     *
     * @return array|Cluster[]
     */
    public function cluster(int $clustersNumber, int $initMethod = KMeans::INIT_RANDOM)
    {
        $clusters = $this->initializeClusters($clustersNumber, $initMethod);

        do {
        } while (!$this->iterate($clusters));

        return $clusters;
    }

    /**
     * @param $clustersNumber
     * @param $initMethod
     *
     * @return array|Cluster[]
     */
    protected function initializeClusters(int $clustersNumber, int $initMethod)
    {
        switch ($initMethod) {
            case KMeans::INIT_RANDOM:
                $clusters = $this->initializeRandomClusters($clustersNumber);
                break;

            case KMeans::INIT_KMEANS_PLUS_PLUS:
                $clusters = $this->initializeKMPPClusters($clustersNumber);
                break;

            default:
                return [];
        }

        $clusters[0]->attachAll($this);

        return $clusters;
    }

    /**
     * @param $clusters
     *
     * @return bool
     */
    protected function iterate($clusters)
    {
        $convergence = true;

        $attach = new SplObjectStorage();
        $detach = new SplObjectStorage();

        foreach ($clusters as $cluster) {
            foreach ($cluster as $point) {
                $closest = $point->getClosest($clusters);

                if ($closest !== $cluster) {
                    isset($attach[$closest]) || $attach[$closest] = new SplObjectStorage();
                    isset($detach[$cluster]) || $detach[$cluster] = new SplObjectStorage();

                    $attach[$closest]->attach($point);
                    $detach[$cluster]->attach($point);

                    $convergence = false;
                }
            }
        }

        foreach ($attach as $cluster) {
            $cluster->attachAll($attach[$cluster]);
        }

        foreach ($detach as $cluster) {
            $cluster->detachAll($detach[$cluster]);
        }

        foreach ($clusters as $cluster) {
            $cluster->updateCentroid();
        }

        return $convergence;
    }

    /**
     * @param int $clustersNumber
     *
     * @return array
     */
    private function initializeRandomClusters(int $clustersNumber)
    {
        $clusters = [];
        list($min, $max) = $this->getBoundaries();

        for ($n = 0; $n < $clustersNumber; ++$n) {
            $clusters[] = new Cluster($this, $this->getRandomPoint($min, $max)->getCoordinates());
        }

        return $clusters;
    }

    /**
     * @param int $clustersNumber
     *
     * @return array
     */
    protected function initializeKMPPClusters(int $clustersNumber)
    {
        $clusters = [];
        $this->rewind();

        $clusters[] = new Cluster($this, $this->current()->getCoordinates());

        $distances = new SplObjectStorage();

        for ($i = 1; $i < $clustersNumber; ++$i) {
            $sum = 0;
            foreach ($this as $point) {
                $distance = $point->getDistanceWith($point->getClosest($clusters));
                $sum += $distances[$point] = $distance;
            }

            $sum = random_int(0, (int) $sum);
            foreach ($this as $point) {
                if (($sum -= $distances[$point]) > 0) {
                    continue;
                }

                $clusters[] = new Cluster($this, $point->getCoordinates());
                break;
            }
        }

        return $clusters;
    }
}
