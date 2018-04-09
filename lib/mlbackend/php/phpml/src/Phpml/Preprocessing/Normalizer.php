<?php

declare(strict_types=1);

namespace Phpml\Preprocessing;

use Phpml\Exception\NormalizerException;
use Phpml\Math\Statistic\StandardDeviation;
use Phpml\Math\Statistic\Mean;

class Normalizer implements Preprocessor
{
    const NORM_L1 = 1;
    const NORM_L2 = 2;
    const NORM_STD= 3;

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
    private $std;

    /**
     * @var array
     */
    private $mean;

    /**
     * @param int $norm
     *
     * @throws NormalizerException
     */
    public function __construct(int $norm = self::NORM_L2)
    {
        if (!in_array($norm, [self::NORM_L1, self::NORM_L2, self::NORM_STD])) {
            throw NormalizerException::unknownNorm();
        }

        $this->norm = $norm;
    }

    /**
     * @param array $samples
     */
    public function fit(array $samples)
    {
        if ($this->fitted) {
            return;
        }

        if ($this->norm == self::NORM_STD) {
            $features = range(0, count($samples[0]) - 1);
            foreach ($features as $i) {
                $values = array_column($samples, $i);
                $this->std[$i] = StandardDeviation::population($values);
                $this->mean[$i] = Mean::arithmetic($values);
            }
        }

        $this->fitted = true;
    }

    /**
     * @param array $samples
     */
    public function transform(array &$samples)
    {
        $methods = [
            self::NORM_L1 => 'normalizeL1',
            self::NORM_L2 => 'normalizeL2',
            self::NORM_STD=> 'normalizeSTD'
        ];
        $method = $methods[$this->norm];

        $this->fit($samples);

        foreach ($samples as &$sample) {
            $this->{$method}($sample);
        }
    }

    /**
     * @param array $sample
     */
    private function normalizeL1(array &$sample)
    {
        $norm1 = 0;
        foreach ($sample as $feature) {
            $norm1 += abs($feature);
        }

        if (0 == $norm1) {
            $count = count($sample);
            $sample = array_fill(0, $count, 1.0 / $count);
        } else {
            foreach ($sample as &$feature) {
                $feature /= $norm1;
            }
        }
    }

    /**
     * @param array $sample
     */
    private function normalizeL2(array &$sample)
    {
        $norm2 = 0;
        foreach ($sample as $feature) {
            $norm2 += $feature * $feature;
        }
        $norm2 = sqrt((float)$norm2);

        if (0 == $norm2) {
            $sample = array_fill(0, count($sample), 1);
        } else {
            foreach ($sample as &$feature) {
                $feature /= $norm2;
            }
        }
    }

    /**
     * @param array $sample
     */
    private function normalizeSTD(array &$sample)
    {
        foreach ($sample as $i => $val) {
            if ($this->std[$i] != 0) {
                $sample[$i] = ($sample[$i] - $this->mean[$i]) / $this->std[$i];
            } else {
                // Same value for all samples.
                $sample[$i] = 0;
            }
        }
    }
}
