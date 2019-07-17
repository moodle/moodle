<?php

declare(strict_types=1);

namespace Phpml;

class Pipeline implements Estimator
{
    /**
     * @var Transformer[]
     */
    private $transformers = [];

    /**
     * @var Estimator
     */
    private $estimator;

    /**
     * @param Transformer[] $transformers
     */
    public function __construct(array $transformers, Estimator $estimator)
    {
        foreach ($transformers as $transformer) {
            $this->addTransformer($transformer);
        }

        $this->estimator = $estimator;
    }

    public function addTransformer(Transformer $transformer): void
    {
        $this->transformers[] = $transformer;
    }

    public function setEstimator(Estimator $estimator): void
    {
        $this->estimator = $estimator;
    }

    /**
     * @return Transformer[]
     */
    public function getTransformers(): array
    {
        return $this->transformers;
    }

    public function getEstimator(): Estimator
    {
        return $this->estimator;
    }

    public function train(array $samples, array $targets): void
    {
        foreach ($this->transformers as $transformer) {
            $transformer->fit($samples, $targets);
            $transformer->transform($samples);
        }

        $this->estimator->train($samples, $targets);
    }

    /**
     * @return mixed
     */
    public function predict(array $samples)
    {
        $this->transformSamples($samples);

        return $this->estimator->predict($samples);
    }

    private function transformSamples(array &$samples): void
    {
        foreach ($this->transformers as $transformer) {
            $transformer->transform($samples);
        }
    }
}
