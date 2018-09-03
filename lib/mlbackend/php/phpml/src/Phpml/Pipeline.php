<?php

declare(strict_types=1);

namespace Phpml;

class Pipeline implements Estimator
{
    /**
     * @var array|Transformer[]
     */
    private $transformers;

    /**
     * @var Estimator
     */
    private $estimator;

    /**
     * @param array|Transformer[] $transformers
     * @param Estimator           $estimator
     */
    public function __construct(array $transformers, Estimator $estimator)
    {
        foreach ($transformers as $transformer) {
            $this->addTransformer($transformer);
        }

        $this->estimator = $estimator;
    }

    /**
     * @param Transformer $transformer
     */
    public function addTransformer(Transformer $transformer)
    {
        $this->transformers[] = $transformer;
    }

    /**
     * @param Estimator $estimator
     */
    public function setEstimator(Estimator $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * @return array|Transformer[]
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * @return Estimator
     */
    public function getEstimator()
    {
        return $this->estimator;
    }

    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets)
    {
        $this->fitTransformers($samples);
        $this->transformSamples($samples);
        $this->estimator->train($samples, $targets);
    }

    /**
     * @param array $samples
     *
     * @return mixed
     */
    public function predict(array $samples)
    {
        $this->transformSamples($samples);

        return $this->estimator->predict($samples);
    }

    /**
     * @param array $samples
     */
    private function fitTransformers(array &$samples)
    {
        foreach ($this->transformers as $transformer) {
            $transformer->fit($samples);
        }
    }

    /**
     * @param array $samples
     */
    private function transformSamples(array &$samples)
    {
        foreach ($this->transformers as $transformer) {
            $transformer->transform($samples);
        }
    }
}
