<?php

declare(strict_types=1);

namespace Phpml\Clustering;

use Phpml\Math\Distance;
use Phpml\Math\Distance\Euclidean;

class DBSCAN implements Clusterer
{
    private const NOISE = -1;

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

    public function __construct(float $epsilon = 0.5, int $minSamples = 3, ?Distance $distanceMetric = null)
    {
        if ($distanceMetric === null) {
            $distanceMetric = new Euclidean();
        }

        $this->epsilon = $epsilon;
        $this->minSamples = $minSamples;
        $this->distanceMetric = $distanceMetric;
    }

    public function cluster(array $samples): array
    {
        $labels = [];
        $n = 0;

        foreach ($samples as $index => $sample) {
            if (isset($labels[$index])) {
                continue;
            }

            $neighborIndices = $this->getIndicesInRegion($sample, $samples);

            if (count($neighborIndices) < $this->minSamples) {
                $labels[$index] = self::NOISE;

                continue;
            }

            $labels[$index] = $n;

            $this->expandCluster($samples, $neighborIndices, $labels, $n);

            ++$n;
        }

        return $this->groupByCluster($samples, $labels, $n);
    }

    private function expandCluster(array $samples, array $seeds, array &$labels, int $n): void
    {
        while (($index = array_pop($seeds)) !== null) {
            if (isset($labels[$index])) {
                if ($labels[$index] === self::NOISE) {
                    $labels[$index] = $n;
                }

                continue;
            }

            $labels[$index] = $n;

            $sample = $samples[$index];
            $neighborIndices = $this->getIndicesInRegion($sample, $samples);

            if (count($neighborIndices) >= $this->minSamples) {
                $seeds = array_unique(array_merge($seeds, $neighborIndices));
            }
        }
    }

    private function getIndicesInRegion(array $center, array $samples): array
    {
        $indices = [];

        foreach ($samples as $index => $sample) {
            if ($this->distanceMetric->distance($center, $sample) < $this->epsilon) {
                $indices[] = $index;
            }
        }

        return $indices;
    }

    private function groupByCluster(array $samples, array $labels, int $n): array
    {
        $clusters = array_fill(0, $n, []);

        foreach ($samples as $index => $sample) {
            if ($labels[$index] !== self::NOISE) {
                $clusters[$labels[$index]][$index] = $sample;
            }
        }

        // Reindex (i.e. to 0, 1, 2, ...) integer indices for backword compatibility
        foreach ($clusters as $index => $cluster) {
            $clusters[$index] = array_merge($cluster, []);
        }

        return $clusters;
    }
}
