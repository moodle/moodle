<?php

declare(strict_types=1);

namespace Phpml\Clustering;

use Phpml\Clustering\KMeans\Cluster;
use Phpml\Clustering\KMeans\Point;
use Phpml\Clustering\KMeans\Space;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance\Euclidean;

class FuzzyCMeans implements Clusterer
{
    /**
     * @var int
     */
    private $clustersNumber;

    /**
     * @var Cluster[]
     */
    private $clusters = [];

    /**
     * @var Space
     */
    private $space;

    /**
     * @var float[][]
     */
    private $membership = [];

    /**
     * @var float
     */
    private $fuzziness;

    /**
     * @var float
     */
    private $epsilon;

    /**
     * @var int
     */
    private $maxIterations;

    /**
     * @var int
     */
    private $sampleCount;

    /**
     * @var array
     */
    private $samples = [];

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(int $clustersNumber, float $fuzziness = 2.0, float $epsilon = 1e-2, int $maxIterations = 100)
    {
        if ($clustersNumber <= 0) {
            throw new InvalidArgumentException('Invalid clusters number');
        }

        $this->clustersNumber = $clustersNumber;
        $this->fuzziness = $fuzziness;
        $this->epsilon = $epsilon;
        $this->maxIterations = $maxIterations;
    }

    public function getMembershipMatrix(): array
    {
        return $this->membership;
    }

    public function cluster(array $samples): array
    {
        // Initialize variables, clusters and membership matrix
        $this->sampleCount = count($samples);
        $this->samples = &$samples;
        $this->space = new Space(count($samples[0]));
        $this->initClusters();

        // Our goal is minimizing the objective value while
        // executing the clustering steps at a maximum number of iterations
        $lastObjective = 0.0;
        $iterations = 0;
        do {
            // Update the membership matrix and cluster centers, respectively
            $this->updateMembershipMatrix();
            $this->updateClusters();

            // Calculate the new value of the objective function
            $objectiveVal = $this->getObjective();
            $difference = abs($lastObjective - $objectiveVal);
            $lastObjective = $objectiveVal;
        } while ($difference > $this->epsilon && $iterations++ <= $this->maxIterations);

        // Attach (hard cluster) each data point to the nearest cluster
        for ($k = 0; $k < $this->sampleCount; ++$k) {
            $column = array_column($this->membership, $k);
            arsort($column);
            reset($column);
            $cluster = $this->clusters[key($column)];
            $cluster->attach(new Point($this->samples[$k]));
        }

        // Return grouped samples
        $grouped = [];
        foreach ($this->clusters as $cluster) {
            $grouped[] = $cluster->getPoints();
        }

        return $grouped;
    }

    protected function initClusters(): void
    {
        // Membership array is a matrix of cluster number by sample counts
        // We initilize the membership array with random values
        $dim = $this->space->getDimension();
        $this->generateRandomMembership($dim, $this->sampleCount);
        $this->updateClusters();
    }

    protected function generateRandomMembership(int $rows, int $cols): void
    {
        $this->membership = [];
        for ($i = 0; $i < $rows; ++$i) {
            $row = [];
            $total = 0.0;
            for ($k = 0; $k < $cols; ++$k) {
                $val = random_int(1, 5) / 10.0;
                $row[] = $val;
                $total += $val;
            }

            $this->membership[] = array_map(static function ($val) use ($total): float {
                return $val / $total;
            }, $row);
        }
    }

    protected function updateClusters(): void
    {
        $dim = $this->space->getDimension();
        if (count($this->clusters) === 0) {
            for ($i = 0; $i < $this->clustersNumber; ++$i) {
                $this->clusters[] = new Cluster($this->space, array_fill(0, $dim, 0.0));
            }
        }

        for ($i = 0; $i < $this->clustersNumber; ++$i) {
            $cluster = $this->clusters[$i];
            $center = $cluster->getCoordinates();
            for ($k = 0; $k < $dim; ++$k) {
                $a = $this->getMembershipRowTotal($i, $k, true);
                $b = $this->getMembershipRowTotal($i, $k, false);
                $center[$k] = $a / $b;
            }

            $cluster->setCoordinates($center);
        }
    }

    protected function getMembershipRowTotal(int $row, int $col, bool $multiply): float
    {
        $sum = 0.0;
        for ($k = 0; $k < $this->sampleCount; ++$k) {
            $val = $this->membership[$row][$k] ** $this->fuzziness;
            if ($multiply) {
                $val *= $this->samples[$k][$col];
            }

            $sum += $val;
        }

        return $sum;
    }

    protected function updateMembershipMatrix(): void
    {
        for ($i = 0; $i < $this->clustersNumber; ++$i) {
            for ($k = 0; $k < $this->sampleCount; ++$k) {
                $distCalc = $this->getDistanceCalc($i, $k);
                $this->membership[$i][$k] = 1.0 / $distCalc;
            }
        }
    }

    protected function getDistanceCalc(int $row, int $col): float
    {
        $sum = 0.0;
        $distance = new Euclidean();
        $dist1 = $distance->distance(
            $this->clusters[$row]->getCoordinates(),
            $this->samples[$col]
        );

        for ($j = 0; $j < $this->clustersNumber; ++$j) {
            $dist2 = $distance->distance(
                $this->clusters[$j]->getCoordinates(),
                $this->samples[$col]
            );

            $val = (($dist1 / $dist2) ** 2.0) / ($this->fuzziness - 1);
            $sum += $val;
        }

        return $sum;
    }

    /**
     * The objective is to minimize the distance between all data points
     * and all cluster centers. This method returns the summation of all
     * these distances
     */
    protected function getObjective(): float
    {
        $sum = 0.0;
        $distance = new Euclidean();
        for ($i = 0; $i < $this->clustersNumber; ++$i) {
            $clust = $this->clusters[$i]->getCoordinates();
            for ($k = 0; $k < $this->sampleCount; ++$k) {
                $point = $this->samples[$k];
                $sum += $distance->distance($clust, $point);
            }
        }

        return $sum;
    }
}
