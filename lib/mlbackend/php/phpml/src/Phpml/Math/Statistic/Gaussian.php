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

    public function __construct(float $mean, float $std)
    {
        $this->mean = $mean;
        $this->std = $std;
    }

    /**
     * Returns probability density of the given <i>$value</i>
     *
     * @return float|int
     */
    public function pdf(float $value)
    {
        // Calculate the probability density by use of normal/Gaussian distribution
        // Ref: https://en.wikipedia.org/wiki/Normal_distribution
        $std2 = $this->std ** 2;
        $mean = $this->mean;

        return exp(-(($value - $mean) ** 2) / (2 * $std2)) / ((2 * $std2 * M_PI) ** .5);
    }

    /**
     * Returns probability density value of the given <i>$value</i> based on
     * given standard deviation and the mean
     */
    public static function distributionPdf(float $mean, float $std, float $value): float
    {
        $normal = new self($mean, $std);

        return $normal->pdf($value);
    }
}
