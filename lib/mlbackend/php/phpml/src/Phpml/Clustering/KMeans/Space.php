<?php

declare(strict_types=1);

namespace Phpml\Clustering\KMeans;

use InvalidArgumentException;
use LogicException;
use Phpml\Clustering\KMeans;
use SplObjectStorage;

class Space extends SplObjectStorage
{
    /**
     * @var int
     */
    protected $dimension;

    public function __construct(int $dimension)
    {
        if ($dimension < 1) {
            throw new LogicException('a space dimension cannot be null or negative');
        }

        $this->dimension = $dimension;
    }

    public function toArray(): array
    {
        $points = [];

        /** @var Point $point */
        foreach ($this as $point) {
            $points[] = $point->toArray();
        }

        return ['points' => $points];
    }

    /**
     * @param mixed $label
     */
    public function newPoint(array $coordinates, $label = null): Point
    {
        if (count($coordinates) !== $this->dimension) {
            throw new LogicException('('.implode(',', $coordinates).') is not a point of this space');
        }

        return new Point($coordinates, $label);
    }

    /**
     * @param mixed $label
     * @param mixed $data
     */
    public function addPoint(array $coordinates, $label = null, $data = null): void
    {
        $this->attach($this->newPoint($coordinates, $label), $data);
    }

    /**
     * @param object $point
     * @param mixed  $data
     */
    public function attach($point, $data = null): void
    {
        if (!$point instanceof Point) {
            throw new InvalidArgumentException('can only attach points to spaces');
        }

        parent::attach($point, $data);
    }

    public function getDimension(): int
    {
        return $this->dimension;
    }

    /**
     * @return array|bool
     */
    public function getBoundaries()
    {
        if (count($this) === 0) {
            return false;
        }

        $min = $this->newPoint(array_fill(0, $this->dimension, null));
        $max = $this->newPoint(array_fill(0, $this->dimension, null));

        /** @var self $point */
        foreach ($this as $point) {
            for ($n = 0; $n < $this->dimension; ++$n) {
                if ($min[$n] === null || $min[$n] > $point[$n]) {
                    $min[$n] = $point[$n];
                }

                if ($max[$n] === null || $max[$n] < $point[$n]) {
                    $max[$n] = $point[$n];
                }
            }
        }

        return [$min, $max];
    }

    public function getRandomPoint(Point $min, Point $max): Point
    {
        $point = $this->newPoint(array_fill(0, $this->dimension, null));

        for ($n = 0; $n < $this->dimension; ++$n) {
            $point[$n] = random_int($min[$n], $max[$n]);
        }

        return $point;
    }

    /**
     * @return Cluster[]
     */
    public function cluster(int $clustersNumber, int $initMethod = KMeans::INIT_RANDOM): array
    {
        $clusters = $this->initializeClusters($clustersNumber, $initMethod);

        do {
        } while (!$this->iterate($clusters));

        return $clusters;
    }

    /**
     * @return Cluster[]
     */
    protected function initializeClusters(int $clustersNumber, int $initMethod): array
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
     * @param Cluster[] $clusters
     */
    protected function iterate(array $clusters): bool
    {
        $convergence = true;

        $attach = new SplObjectStorage();
        $detach = new SplObjectStorage();

        foreach ($clusters as $cluster) {
            foreach ($cluster as $point) {
                $closest = $point->getClosest($clusters);

                if ($closest !== $cluster) {
                    $attach[$closest] ?? $attach[$closest] = new SplObjectStorage();
                    $detach[$cluster] ?? $detach[$cluster] = new SplObjectStorage();

                    $attach[$closest]->attach($point);
                    $detach[$cluster]->attach($point);

                    $convergence = false;
                }
            }
        }

        /** @var Cluster $cluster */
        foreach ($attach as $cluster) {
            $cluster->attachAll($attach[$cluster]);
        }

        /** @var Cluster $cluster */
        foreach ($detach as $cluster) {
            $cluster->detachAll($detach[$cluster]);
        }

        foreach ($clusters as $cluster) {
            $cluster->updateCentroid();
        }

        return $convergence;
    }

    /**
     * @return Cluster[]
     */
    protected function initializeKMPPClusters(int $clustersNumber): array
    {
        $clusters = [];
        $this->rewind();

        /** @var Point $current */
        $current = $this->current();

        $clusters[] = new Cluster($this, $current->getCoordinates());

        $distances = new SplObjectStorage();

        for ($i = 1; $i < $clustersNumber; ++$i) {
            $sum = 0;
            /** @var Point $point */
            foreach ($this as $point) {
                $closest = $point->getClosest($clusters);
                if ($closest === null) {
                    continue;
                }

                $distance = $point->getDistanceWith($closest);
                $sum += $distances[$point] = $distance;
            }

            $sum = random_int(0, (int) $sum);
            /** @var Point $point */
            foreach ($this as $point) {
                $sum -= $distances[$point];

                if ($sum > 0) {
                    continue;
                }

                $clusters[] = new Cluster($this, $point->getCoordinates());

                break;
            }
        }

        return $clusters;
    }

    /**
     * @return Cluster[]
     */
    private function initializeRandomClusters(int $clustersNumber): array
    {
        $clusters = [];
        [$min, $max] = $this->getBoundaries();

        for ($n = 0; $n < $clustersNumber; ++$n) {
            $clusters[] = new Cluster($this, $this->getRandomPoint($min, $max)->getCoordinates());
        }

        return $clusters;
    }
}
