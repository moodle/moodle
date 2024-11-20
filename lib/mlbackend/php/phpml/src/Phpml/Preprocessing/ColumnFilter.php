<?php

declare(strict_types=1);

namespace Phpml\Preprocessing;

final class ColumnFilter implements Preprocessor
{
    /**
     * @var string[]
     */
    private $datasetColumns = [];

    /**
     * @var string[]
     */
    private $filterColumns = [];

    public function __construct(array $datasetColumns, array $filterColumns)
    {
        $this->datasetColumns = array_map(static function (string $column): string {
            return $column;
        }, $datasetColumns);
        $this->filterColumns = array_map(static function (string $column): string {
            return $column;
        }, $filterColumns);
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        //nothing to do
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        $keys = array_intersect($this->datasetColumns, $this->filterColumns);

        foreach ($samples as &$sample) {
            $sample = array_values(array_intersect_key($sample, $keys));
        }
    }
}
