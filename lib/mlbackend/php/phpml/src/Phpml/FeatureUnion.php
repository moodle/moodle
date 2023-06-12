<?php

declare(strict_types=1);

namespace Phpml;

use Phpml\Exception\InvalidArgumentException;

final class FeatureUnion implements Transformer
{
    /**
     * @var Pipeline[]
     */
    private $pipelines = [];

    /**
     * @param Pipeline[] $pipelines
     */
    public function __construct(array $pipelines)
    {
        if ($pipelines === []) {
            throw new InvalidArgumentException('At least one pipeline is required');
        }

        $this->pipelines = array_map(static function (Pipeline $pipeline): Pipeline {
            return $pipeline;
        }, $pipelines);
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        $originSamples = $samples;
        foreach ($this->pipelines as $pipeline) {
            foreach ($pipeline->getTransformers() as $transformer) {
                $transformer->fit($samples, $targets);
                $transformer->transform($samples, $targets);
            }
            $samples = $originSamples;
        }
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        $this->transformSamples($samples, $targets);
    }

    public function fitAndTransform(array &$samples, ?array &$targets = null): void
    {
        $this->transformSamples($samples, $targets, true);
    }

    private function transformSamples(array &$samples, ?array &$targets = null, bool $fit = false): void
    {
        $union = [];
        $originSamples = $samples;
        foreach ($this->pipelines as $pipeline) {
            foreach ($pipeline->getTransformers() as $transformer) {
                if ($fit) {
                    $transformer->fit($samples, $targets);
                }
                $transformer->transform($samples, $targets);
            }

            foreach ($samples as $index => $sample) {
                $union[$index] = array_merge($union[$index] ?? [], is_array($sample) ? $sample : [$sample]);
            }
            $samples = $originSamples;
        }

        $samples = $union;
    }
}
