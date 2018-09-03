<?php

declare(strict_types=1);

namespace Phpml\Regression;

use Phpml\Helper\Predictable;
use Phpml\Math\Matrix;

class LeastSquares implements Regression
{
    use Predictable;
    /**
     * @var array
     */
    private $samples = [];

    /**
     * @var array
     */
    private $targets = [];

    /**
     * @var float
     */
    private $intercept;

    /**
     * @var array
     */
    private $coefficients;

    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets)
    {
        $this->samples = array_merge($this->samples, $samples);
        $this->targets = array_merge($this->targets, $targets);

        $this->computeCoefficients();
    }

    /**
     * @param array $sample
     *
     * @return mixed
     */
    public function predictSample(array $sample)
    {
        $result = $this->intercept;
        foreach ($this->coefficients as $index => $coefficient) {
            $result += $coefficient * $sample[$index];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCoefficients()
    {
        return $this->coefficients;
    }

    /**
     * @return float
     */
    public function getIntercept()
    {
        return $this->intercept;
    }

    /**
     * coefficient(b) = (X'X)-1X'Y.
     */
    private function computeCoefficients()
    {
        $samplesMatrix = $this->getSamplesMatrix();
        $targetsMatrix = $this->getTargetsMatrix();

        $ts = $samplesMatrix->transpose()->multiply($samplesMatrix)->inverse();
        $tf = $samplesMatrix->transpose()->multiply($targetsMatrix);

        $this->coefficients = $ts->multiply($tf)->getColumnValues(0);
        $this->intercept = array_shift($this->coefficients);
    }

    /**
     * Add one dimension for intercept calculation.
     *
     * @return Matrix
     */
    private function getSamplesMatrix()
    {
        $samples = [];
        foreach ($this->samples as $sample) {
            array_unshift($sample, 1);
            $samples[] = $sample;
        }

        return new Matrix($samples);
    }

    /**
     * @return Matrix
     */
    private function getTargetsMatrix()
    {
        if (is_array($this->targets[0])) {
            return new Matrix($this->targets);
        }

        return Matrix::fromFlatArray($this->targets);
    }
}
