<?php

declare(strict_types=1);

namespace Phpml\Preprocessing;

use Phpml\Preprocessing\Imputer\Strategy;

class Imputer implements Preprocessor
{
    const AXIS_COLUMN = 0;
    const AXIS_ROW = 1;

    /**
     * @var mixed
     */
    private $missingValue;

    /**
     * @var Strategy
     */
    private $strategy;

    /**
     * @var int
     */
    private $axis;

    /**
     * @var
     */
    private $samples;

    /**
     * @param mixed      $missingValue
     * @param Strategy   $strategy
     * @param int        $axis
     * @param array|null $samples
     */
    public function __construct($missingValue, Strategy $strategy, int $axis = self::AXIS_COLUMN, array $samples = [])
    {
        $this->missingValue = $missingValue;
        $this->strategy = $strategy;
        $this->axis = $axis;
        $this->samples = $samples;
    }

    /**
     * @param array $samples
     */
    public function fit(array $samples)
    {
        $this->samples = $samples;
    }

    /**
     * @param array $samples
     */
    public function transform(array &$samples)
    {
        foreach ($samples as &$sample) {
            $this->preprocessSample($sample);
        }
    }

    /**
     * @param array $sample
     */
    private function preprocessSample(array &$sample)
    {
        foreach ($sample as $column => &$value) {
            if ($value === $this->missingValue) {
                $value = $this->strategy->replaceValue($this->getAxis($column, $sample));
            }
        }
    }

    /**
     * @param int   $column
     * @param array $currentSample
     *
     * @return array
     */
    private function getAxis(int $column, array $currentSample): array
    {
        if (self::AXIS_ROW === $this->axis) {
            return array_diff($currentSample, [$this->missingValue]);
        }

        $axis = [];
        foreach ($this->samples as $sample) {
            if ($sample[$column] !== $this->missingValue) {
                $axis[] = $sample[$column];
            }
        }

        return $axis;
    }
}
