<?php

declare(strict_types=1);

namespace Phpml\CrossValidation;

use Phpml\Dataset\ArrayDataset;
use Phpml\Dataset\Dataset;

class StratifiedRandomSplit extends RandomSplit
{
    /**
     * @param Dataset $dataset
     * @param float   $testSize
     */
    protected function splitDataset(Dataset $dataset, float $testSize)
    {
        $datasets = $this->splitByTarget($dataset);

        foreach ($datasets as $targetSet) {
            parent::splitDataset($targetSet, $testSize);
        }
    }

    /**
     * @param Dataset $dataset
     *
     * @return Dataset[]|array
     */
    private function splitByTarget(Dataset $dataset): array
    {
        $targets = $dataset->getTargets();
        $samples = $dataset->getSamples();

        $uniqueTargets = array_unique($targets);
        $split = array_combine($uniqueTargets, array_fill(0, count($uniqueTargets), []));

        foreach ($samples as $key => $sample) {
            $split[$targets[$key]][] = $sample;
        }

        $datasets = $this->createDatasets($uniqueTargets, $split);

        return $datasets;
    }

    /**
     * @param array $uniqueTargets
     * @param array $split
     *
     * @return array
     */
    private function createDatasets(array $uniqueTargets, array $split): array
    {
        $datasets = [];
        foreach ($uniqueTargets as $target) {
            $datasets[$target] = new ArrayDataset($split[$target], array_fill(0, count($split[$target]), $target));
        }

        return $datasets;
    }
}
