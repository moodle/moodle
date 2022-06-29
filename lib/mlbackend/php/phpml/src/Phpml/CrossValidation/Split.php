<?php

declare(strict_types=1);

namespace Phpml\CrossValidation;

use Phpml\Dataset\Dataset;
use Phpml\Exception\InvalidArgumentException;

abstract class Split
{
    /**
     * @var array
     */
    protected $trainSamples = [];

    /**
     * @var array
     */
    protected $testSamples = [];

    /**
     * @var array
     */
    protected $trainLabels = [];

    /**
     * @var array
     */
    protected $testLabels = [];

    public function __construct(Dataset $dataset, float $testSize = 0.3, ?int $seed = null)
    {
        if ($testSize <= 0 || $testSize >= 1) {
            throw new InvalidArgumentException('testsize must be between 0.0 and 1.0');
        }

        $this->seedGenerator($seed);

        $this->splitDataset($dataset, $testSize);
    }

    public function getTrainSamples(): array
    {
        return $this->trainSamples;
    }

    public function getTestSamples(): array
    {
        return $this->testSamples;
    }

    public function getTrainLabels(): array
    {
        return $this->trainLabels;
    }

    public function getTestLabels(): array
    {
        return $this->testLabels;
    }

    abstract protected function splitDataset(Dataset $dataset, float $testSize): void;

    protected function seedGenerator(?int $seed = null): void
    {
        if ($seed === null) {
            mt_srand();
        } else {
            mt_srand($seed);
        }
    }
}
