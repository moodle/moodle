<?php

declare(strict_types=1);

namespace Phpml\Helper\Optimizer;

abstract class Optimizer
{
    /**
     * Unknown variables to be found
     *
     * @var array
     */
    protected $theta;

    /**
     * Number of dimensions
     *
     * @var int
     */
    protected $dimensions;

    /**
     * Inits a new instance of Optimizer for the given number of dimensions
     *
     * @param int $dimensions
     */
    public function __construct(int $dimensions)
    {
        $this->dimensions = $dimensions;

        // Inits the weights randomly
        $this->theta = [];
        for ($i = 0; $i < $this->dimensions; ++$i) {
            $this->theta[] = rand() / (float) getrandmax();
        }
    }

    /**
     * Sets the weights manually
     *
     * @param array $theta
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setInitialTheta(array $theta)
    {
        if (count($theta) != $this->dimensions) {
            throw new \Exception("Number of values in the weights array should be $this->dimensions");
        }

        $this->theta = $theta;

        return $this;
    }

    /**
     * Executes the optimization with the given samples & targets
     * and returns the weights
     *
     * @param array    $samples
     * @param array    $targets
     * @param \Closure $gradientCb
     */
    abstract protected function runOptimization(array $samples, array $targets, \Closure $gradientCb);
}
