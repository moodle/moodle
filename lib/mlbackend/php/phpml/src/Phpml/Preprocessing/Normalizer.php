<?php

declare(strict_types=1);

namespace Phpml\Preprocessing;

use Phpml\Exception\NormalizerException;
use Phpml\Math\Statistic\Mean;
use Phpml\Math\Statistic\StandardDeviation;

class Normalizer implements Preprocessor
{
    public const NORM_L1 = 1;

    public const NORM_L2 = 2;

    public const NORM_STD = 3;

    /**
     * @var int
     */
    private $norm;

    /**
     * @var bool
     */
    private $fitted = false;

    /**
     * @var array
     */
    private $std = [];

    /**
     * @var array
     */
    private $mean = [];

    /**
     * @throws NormalizerException
     */
    public function __construct(int $norm = self::NORM_L2)
    {
        if (!in_array($norm, [self::NORM_L1, self::NORM_L2, self::NORM_STD], true)) {
            throw new NormalizerException('Unknown norm supplied.');
        }

        $this->norm = $norm;
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        if ($this->fitted) {
            return;
        }

        if ($this->norm === self::NORM_STD) {
            $features = range(0, count($samples[0]) - 1);
            foreach ($features as $i) {
                $values = array_column($samples, $i);
                $this->std[$i] = StandardDeviation::population($values);
                $this->mean[$i] = Mean::arithmetic($values);
            }
        }

        $this->fitted = true;
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        $methods = [
            self::NORM_L1 => 'normalizeL1',
            self::NORM_L2 => 'normalizeL2',
            self::NORM_STD => 'normalizeSTD',
        ];
        $method = $methods[$this->norm];

        $this->fit($samples);

        foreach ($samples as &$sample) {
            $this->{$method}($sample);
        }
    }

    private function normalizeL1(array &$sample): void
    {
        $norm1 = 0;
        foreach ($sample as $feature) {
            $norm1 += abs($feature);
        }

        if ($norm1 == 0) {
            $count = count($sample);
            $sample = array_fill(0, $count, 1.0 / $count);
        } else {
            array_walk($sample, function (&$feature) use ($norm1): void {
                $feature /= $norm1;
            });
        }
    }

    private function normalizeL2(array &$sample): void
    {
        $norm2 = 0;
        foreach ($sample as $feature) {
            $norm2 += $feature * $feature;
        }

        $norm2 **= .5;

        if ($norm2 == 0) {
            $sample = array_fill(0, count($sample), 1);
        } else {
            array_walk($sample, function (&$feature) use ($norm2): void {
                $feature /= $norm2;
            });
        }
    }

    private function normalizeSTD(array &$sample): void
    {
        foreach (array_keys($sample) as $i) {
            if ($this->std[$i] != 0) {
                $sample[$i] = ($sample[$i] - $this->mean[$i]) / $this->std[$i];
            } else {
                // Same value for all samples.
                $sample[$i] = 0;
            }
        }
    }
}
