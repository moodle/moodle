<?php

declare(strict_types=1);

namespace Phpml\Preprocessing;

final class NumberConverter implements Preprocessor
{
    /**
     * @var bool
     */
    private $transformTargets;

    /**
     * @var mixed
     */
    private $nonNumericPlaceholder;

    /**
     * @param mixed $nonNumericPlaceholder
     */
    public function __construct(bool $transformTargets = false, $nonNumericPlaceholder = null)
    {
        $this->transformTargets = $transformTargets;
        $this->nonNumericPlaceholder = $nonNumericPlaceholder;
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        //nothing to do
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        foreach ($samples as &$sample) {
            foreach ($sample as &$feature) {
                $feature = is_numeric($feature) ? (float) $feature : $this->nonNumericPlaceholder;
            }
        }

        if ($this->transformTargets && is_array($targets)) {
            foreach ($targets as &$target) {
                $target = is_numeric($target) ? (float) $target : $this->nonNumericPlaceholder;
            }
        }
    }
}
