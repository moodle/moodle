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

    /**
     * @param Dataset $dataset
     * @param float   $testSize
     * @param int     $seed
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Dataset $dataset, float $testSize = 0.3, int $seed = null)
    {
        if (0 >= $testSize || 1 <= $testSize) {
            throw InvalidArgumentException::percentNotInRange('testSize');
        }
        $this->seedGenerator($seed);

        $this->splitDataset($dataset, $testSize);
    }

    abstract protected function splitDataset(Dataset $dataset, float $testSize);

    /**
     * @return array
     */
    public function getTrainSamples()
    {
        return $this->trainSamples;
    }

    /**
     * @return array
     */
    public function getTestSamples()
    {
        return $this->testSamples;
    }

    /**
     * @return array
     */
    public function getTrainLabels()
    {
        return $this->trainLabels;
    }

    /**
     * @return array
     */
    public function getTestLabels()
    {
        return $this->testLabels;
    }

    /**
     * @param int|null $seed
     */
    protected function seedGenerator(int $seed = null)
    {
        if (null === $seed) {
            mt_srand();
        } else {
            mt_srand($seed);
        }
    }
}
