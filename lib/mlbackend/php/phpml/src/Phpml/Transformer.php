<?php

declare(strict_types=1);

namespace Phpml;

interface Transformer
{
    /**
     * most transformers don't require targets to train so null allow to use fit method without setting targets
     */
    public function fit(array $samples, ?array $targets = null): void;

    public function transform(array &$samples, ?array &$targets = null): void;
}
