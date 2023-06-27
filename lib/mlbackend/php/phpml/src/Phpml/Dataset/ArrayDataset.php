<?php

declare(strict_types=1);

namespace Phpml\Dataset;

use Phpml\Exception\InvalidArgumentException;

class ArrayDataset implements Dataset
{
    /**
     * @var array
     */
    protected $samples = [];

    /**
     * @var array
     */
    protected $targets = [];

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(array $samples, array $targets)
    {
        if (count($samples) !== count($targets)) {
            throw new InvalidArgumentException('Size of given arrays does not match');
        }

        $this->samples = $samples;
        $this->targets = $targets;
    }

    public function getSamples(): array
    {
        return $this->samples;
    }

    public function getTargets(): array
    {
        return $this->targets;
    }

    /**
     * @param int[] $columns
     */
    public function removeColumns(array $columns): void
    {
        foreach ($this->samples as &$sample) {
            $this->removeColumnsFromSample($sample, $columns);
        }
    }

    private function removeColumnsFromSample(array &$sample, array $columns): void
    {
        foreach ($columns as $index) {
            unset($sample[$index]);
        }

        $sample = array_values($sample);
    }
}
