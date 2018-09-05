<?php

declare(strict_types=1);

namespace Phpml\Clustering;

use Phpml\Clustering\KMeans\Space;
use Phpml\Exception\InvalidArgumentException;

class KMeans implements Clusterer
{
    const INIT_RANDOM = 1;
    const INIT_KMEANS_PLUS_PLUS = 2;

    /**
     * @var int
     */
    private $clustersNumber;

    /**
     * @var int
     */
    private $initialization;

    /**
     * @param int $clustersNumber
     * @param int $initialization
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $clustersNumber, int $initialization = self::INIT_KMEANS_PLUS_PLUS)
    {
        if ($clustersNumber <= 0) {
            throw InvalidArgumentException::invalidClustersNumber();
        }

        $this->clustersNumber = $clustersNumber;
        $this->initialization = $initialization;
    }

    /**
     * @param array $samples
     *
     * @return array
     */
    public function cluster(array $samples)
    {
        $space = new Space(count($samples[0]));
        foreach ($samples as $sample) {
            $space->addPoint($sample);
        }

        $clusters = [];
        foreach ($space->cluster($this->clustersNumber, $this->initialization) as $cluster) {
            $clusters[] = $cluster->getPoints();
        }

        return $clusters;
    }
}
