<?php

declare(strict_types=1);

namespace Phpml\Clustering;

use Phpml\Clustering\KMeans\Point;
use Phpml\Clustering\KMeans\Cluster;
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
     * @var array|Cluster[]
     */
    private $clusters = null;

    /**
     * @var Space
     */
    private $space;

    /**
     * @var array|float[][]
     */
    private $membership;

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
    private $samples;

    /**
     * @param int $clustersNumber
     * @param float $fuzziness
     * @param float $epsilon
     * @param int $maxIterations
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $clustersNumber, float $fuzziness = 2.0, float $epsilon = 1e-2, int $maxIterations = 100)
    {
        if ($clustersNumber <= 0) {
            throw InvalidArgumentException::invalidClustersNumber();
        }
        $this->clustersNumber = $clustersNumber;
        $this->fuzziness = $fuzziness;
        $this->epsilon = $epsilon;
        $this->maxIterations = $maxIterations;
    }

    protected function initClusters()
    {
        // Membership array is a matrix of cluster number by sample counts
        // We initilize the membership array with random values
        $dim = $this->space->getDimension();
        $this->generateRandomMembership($dim, $this->sampleCount);
        $this->updateClusters();
    }

    /**
     * @param int $rows
     * @param int $cols
     */
    protected function generateRandomMembership(int $rows, int $cols)
    {
        $this->membership = [];
        for ($i = 0; $i < $rows; ++$i) {
            $row = [];
            $total = 0.0;
            for ($k = 0; $k < $cols; ++$k) {
                $val = rand(1, 5) / 10.0;
                $row[] = $val;
                $total += $val;
            }

            $this->membership[] = array_map(function ($val) use ($total) {
                return $val / $total;
            }, $row);
        }
    }

    protected function updateClusters()
    {
        $dim = $this->space->getDimension();
        if (!$this->clusters) {
            $this->clusters = [];
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

    protected function getMembershipRowTotal(int $row, int $col, bool $multiply)
    {
        $sum = 0.0;
        for ($k = 0; $k < $this->sampleCount; ++$k) {
            $val = pow($this->membership[$row][$k], $this->fuzziness);
            if ($multiply) {
                $val *= $this->samples[$k][$col];
            }

            $sum += $val;
        }

        return $sum;
    }

    protected function updateMembershipMatrix()
    {
        for ($i = 0; $i < $this->clustersNumber; ++$i) {
            for ($k = 0; $k < $this->sampleCount; ++$k) {
                $distCalc = $this->getDistanceCalc($i, $k);
                $this->membership[$i][$k] = 1.0 / $distCalc;
            }
        }
    }

    /**
     *
     * @param int $row
     * @param int $col
     * @return float
     */
    protected function getDistanceCalc(int $row, int $col)
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

            $val = pow($dist1 / $dist2, 2.0 / ($this->fuzziness - 1));
            $sum += $val;
        }
        return $sum;
    }

    /**
     * The objective is to minimize the distance between all data points
     * and all cluster centers. This method returns the summation of all
     * these distances
     */
    protected function getObjective()
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

    /**
     * @return array
     */
    public function getMembershipMatrix()
    {
        return $this->membership;
    }

    /**
     * @param array|Point[] $samples
     * @return array
     */
    public function cluster(array $samples)
    {
        // Initialize variables, clusters and membership matrix
        $this->sampleCount = count($samples);
        $this->samples =& $samples;
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
            $i = key($column);
            $cluster = $this->clusters[$i];
            $cluster->attach(new Point($this->samples[$k]));
        }

        // Return grouped samples
        $grouped = [];
        foreach ($this->clusters as $cluster) {
            $grouped[] = $cluster->getPoints();
        }

        return $grouped;
    }
}
