<?php

declare(strict_types=1);

namespace Phpml\Preprocessing;

use Phpml\Exception\InvalidArgumentException;

final class OneHotEncoder implements Preprocessor
{
    /**
     * @var bool
     */
    private $ignoreUnknown;

    /**
     * @var array
     */
    private $categories = [];

    public function __construct(bool $ignoreUnknown = false)
    {
        $this->ignoreUnknown = $ignoreUnknown;
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        foreach (array_keys(array_values(current($samples))) as $column) {
            $this->fitColumn($column, array_values(array_unique(array_column($samples, $column))));
        }
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        foreach ($samples as &$sample) {
            $sample = $this->transformSample(array_values($sample));
        }
    }

    private function fitColumn(int $column, array $values): void
    {
        $count = count($values);
        foreach ($values as $index => $value) {
            $map = array_fill(0, $count, 0);
            $map[$index] = 1;
            $this->categories[$column][$value] = $map;
        }
    }

    private function transformSample(array $sample): array
    {
        $encoded = [];
        foreach ($sample as $column => $feature) {
            if (!isset($this->categories[$column][$feature]) && !$this->ignoreUnknown) {
                throw new InvalidArgumentException(sprintf('Missing category "%s" for column %s in trained encoder', $feature, $column));
            }

            $encoded = array_merge(
                $encoded,
                $this->categories[$column][$feature] ?? array_fill(0, count($this->categories[$column]), 0)
            );
        }

        return $encoded;
    }
}
