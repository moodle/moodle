<?php

declare(strict_types=1);

namespace Phpml;

interface IncrementalEstimator
{
    public function partialTrain(array $samples, array $targets, array $labels = []): void;
}
