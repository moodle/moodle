<?php

declare(strict_types=1);

namespace Phpml\Preprocessing;

final class LabelEncoder implements Preprocessor
{
    /**
     * @var int[]
     */
    private $classes = [];

    public function fit(array $samples, ?array $targets = null): void
    {
        $this->classes = [];

        foreach ($samples as $sample) {
            if (!isset($this->classes[(string) $sample])) {
                $this->classes[(string) $sample] = count($this->classes);
            }
        }
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        foreach ($samples as &$sample) {
            $sample = $this->classes[(string) $sample];
        }
    }

    public function inverseTransform(array &$samples): void
    {
        $classes = array_flip($this->classes);
        foreach ($samples as &$sample) {
            $sample = $classes[$sample];
        }
    }

    /**
     * @return string[]
     */
    public function classes(): array
    {
        return array_keys($this->classes);
    }
}
