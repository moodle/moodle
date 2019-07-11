<?php

declare(strict_types=1);

namespace Phpml\FeatureSelection;

interface ScoringFunction
{
    public function score(array $samples, array $targets): array;
}
