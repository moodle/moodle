<?php

declare(strict_types=1);

namespace Phpml\Clustering;

use Phpml\Math\Distance;
use Phpml\Math\Distance\Euclidean;

class DBSCAN implements Clusterer
{
    /**
     * @var float
     */
    private $epsilon;

    /**
     * @var int
     */
    private $minSamples;

    /**
     * @var Distance
     */
    private $distanceMetric;

    /**
     * @param float    $epsilon
     * @param int      $minSamples
     * @param Distance $distanceMetric
     */
    public function __construct($epsilon = 0.5, $minSamples = 3, Distance $distanceMetric = null)
    {
        if (null === $distanceMetric) {
            $distanceMetric = new Euclidean();
        }

        $this->epsilon = $epsilon;
        $this->minSamples = $minSamples;
        $this->distanceMetric = $distanceMetric;
    }

    /**
     * @param array $samples
     *
     * @return array
     */
    public function cluster(array $samples)
    {
        $clusters = [];
        $visited = [];

        foreach ($samples as $index => $sample) {
            if (isset($visited[$index])) {
                continue;
            }
            $visited[$index] = true;

            $regionSamples = $this->getSamplesInRegion($sample, $samples);
            if (count($regionSamples) >= $this->minSamples) {
                $clusters[] = $this->expandCluster($regionSamples, $visited);
            }
        }

        return $clusters;
    }

    /**
     * @param array $localSample
     * @param array $samples
     *
     * @return array
     */
    private function getSamplesInRegion($localSample, $samples)
    {
        $region = [];

        foreach ($samples as $index => $sample) {
            if ($this->distanceMetric->distance($localSample, $sample) < $this->epsilon) {
                $region[$index] = $sample;
            }
        }

        return $region;
    }

    /**
     * @param array $samples
     * @param array $visited
     *
     * @return array
     */
    private function expandCluster($samples, &$visited)
    {
        $cluster = [];

        foreach ($samples as $index => $sample) {
            if (!isset($visited[$index])) {
                $visited[$index] = true;
                $regionSamples = $this->getSamplesInRegion($sample, $samples);
                if (count($regionSamples) > $this->minSamples) {
                    $cluster = array_merge($regionSamples, $cluster);
                }
            }

            $cluster[] = $sample;
        }

        return $cluster;
    }
}
