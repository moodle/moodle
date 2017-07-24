<?php

declare(strict_types=1);

namespace Phpml\Classification;

abstract class WeightedClassifier implements Classifier
{
    /**
     * @var array
     */
    protected $weights;

    /**
     * Sets the array including a weight for each sample
     *
     * @param array $weights
     */
    public function setSampleWeights(array $weights)
    {
        $this->weights = $weights;
    }
}
