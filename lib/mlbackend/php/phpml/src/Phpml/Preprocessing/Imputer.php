<?php

declare(strict_types=1);

namespace Phpml\Preprocessing;

use Phpml\Exception\InvalidOperationException;
use Phpml\Preprocessing\Imputer\Strategy;

class Imputer implements Preprocessor
{
    public const AXIS_COLUMN = 0;

    public const AXIS_ROW = 1;

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
     * @var mixed[]
     */
    private $samples = [];

    /**
     * @param mixed $missingValue
     */
    public function __construct($missingValue, Strategy $strategy, int $axis = self::AXIS_COLUMN, array $samples = [])
    {
        $this->missingValue = $missingValue;
        $this->strategy = $strategy;
        $this->axis = $axis;
        $this->samples = $samples;
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        $this->samples = $samples;
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        if ($this->samples === []) {
            throw new InvalidOperationException('Missing training samples for Imputer.');
        }

        foreach ($samples as &$sample) {
            $this->preprocessSample($sample);
        }
    }

    private function preprocessSample(array &$sample): void
    {
        foreach ($sample as $column => &$value) {
            if ($value === $this->missingValue) {
                $value = $this->strategy->replaceValue($this->getAxis($column, $sample));
            }
        }
    }

    private function getAxis(int $column, array $currentSample): array
    {
        if ($this->axis === self::AXIS_ROW) {
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
