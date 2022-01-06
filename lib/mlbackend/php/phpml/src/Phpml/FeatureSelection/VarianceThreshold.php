<?php

declare(strict_types=1);

namespace Phpml\FeatureSelection;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Matrix;
use Phpml\Math\Statistic\Variance;
use Phpml\Transformer;

final class VarianceThreshold implements Transformer
{
    /**
     * @var float
     */
    private $threshold;

    /**
     * @var array
     */
    private $variances = [];

    /**
     * @var array
     */
    private $keepColumns = [];

    public function __construct(float $threshold = 0.0)
    {
        if ($threshold < 0) {
            throw new InvalidArgumentException('Threshold can\'t be lower than zero');
        }

        $this->threshold = $threshold;
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        $this->variances = array_map(function (array $column) {
            return Variance::population($column);
        }, Matrix::transposeArray($samples));

        foreach ($this->variances as $column => $variance) {
            if ($variance > $this->threshold) {
                $this->keepColumns[$column] = true;
            }
        }
    }

    public function transform(array &$samples): void
    {
        foreach ($samples as &$sample) {
            $sample = array_values(array_intersect_key($sample, $this->keepColumns));
        }
    }
}
