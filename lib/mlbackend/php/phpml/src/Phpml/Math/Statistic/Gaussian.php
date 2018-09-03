<?php

declare(strict_types=1);

namespace Phpml\Math\Statistic;

class Gaussian
{
    /**
     * @var float
     */
    protected $mean;

    /**
     * @var float
     */
    protected $std;

    /**
     * @param float $mean
     * @param float $std
     */
    public function __construct(float $mean, float $std)
    {
        $this->mean = $mean;
        $this->std = $std;
    }

    /**
     * Returns probability density of the given <i>$value</i>
     *
     * @param float $value
     *
     * @return float|int
     */
    public function pdf(float $value)
    {
        // Calculate the probability density by use of normal/Gaussian distribution
        // Ref: https://en.wikipedia.org/wiki/Normal_distribution
        $std2 = $this->std ** 2;
        $mean = $this->mean;
        return exp(- (($value - $mean) ** 2) / (2 * $std2)) / sqrt(2 * $std2 * pi());
    }

    /**
     * Returns probability density value of the given <i>$value</i> based on
     * given standard deviation and the mean
     *
     * @param float $mean
     * @param float $std
     * @param float $value
     *
     * @return float
     */
    public static function distributionPdf(float $mean, float $std, float $value)
    {
        $normal = new self($mean, $std);
        return $normal->pdf($value);
    }
}
