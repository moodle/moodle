<?php

declare(strict_types=1);

namespace Phpml\Preprocessing;

final class LambdaTransformer implements Preprocessor
{
    /**
     * @var callable
     */
    private $lambda;

    public function __construct(callable $lambda)
    {
        $this->lambda = $lambda;
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        // nothing to do
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        foreach ($samples as &$sample) {
            $sample = call_user_func($this->lambda, $sample);
        }
    }
}
