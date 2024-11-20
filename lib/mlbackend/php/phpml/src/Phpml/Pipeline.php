<?php

declare(strict_types=1);

namespace Phpml;

use Phpml\Exception\InvalidOperationException;

class Pipeline implements Estimator, Transformer
{
    /**
     * @var Transformer[]
     */
    private $transformers = [];

    /**
     * @var Estimator|null
     */
    private $estimator;

    /**
     * @param Transformer[] $transformers
     */
    public function __construct(array $transformers, ?Estimator $estimator = null)
    {
        $this->transformers = array_map(static function (Transformer $transformer): Transformer {
            return $transformer;
        }, $transformers);
        $this->estimator = $estimator;
    }

    /**
     * @return Transformer[]
     */
    public function getTransformers(): array
    {
        return $this->transformers;
    }

    public function getEstimator(): ?Estimator
    {
        return $this->estimator;
    }

    public function train(array $samples, array $targets): void
    {
        if ($this->estimator === null) {
            throw new InvalidOperationException('Pipeline without estimator can\'t use train method');
        }

        foreach ($this->transformers as $transformer) {
            $transformer->fit($samples, $targets);
            $transformer->transform($samples, $targets);
        }

        $this->estimator->train($samples, $targets);
    }

    /**
     * @return mixed
     */
    public function predict(array $samples)
    {
        $this->transform($samples);

        if ($this->estimator === null) {
            throw new InvalidOperationException('Pipeline without estimator can\'t use predict method');
        }

        return $this->estimator->predict($samples);
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        foreach ($this->transformers as $transformer) {
            $transformer->fit($samples, $targets);
            $transformer->transform($samples, $targets);
        }
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        foreach ($this->transformers as $transformer) {
            $transformer->transform($samples, $targets);
        }
    }
}
