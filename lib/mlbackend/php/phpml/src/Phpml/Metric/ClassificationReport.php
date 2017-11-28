<?php

declare(strict_types=1);

namespace Phpml\Metric;

class ClassificationReport
{
    /**
     * @var array
     */
    private $precision = [];

    /**
     * @var array
     */
    private $recall = [];

    /**
     * @var array
     */
    private $f1score = [];

    /**
     * @var array
     */
    private $support = [];

    /**
     * @var array
     */
    private $average = [];

    /**
     * @param array $actualLabels
     * @param array $predictedLabels
     */
    public function __construct(array $actualLabels, array $predictedLabels)
    {
        $truePositive = $falsePositive = $falseNegative = $this->support = self::getLabelIndexedArray($actualLabels, $predictedLabels);

        foreach ($actualLabels as $index => $actual) {
            $predicted = $predictedLabels[$index];
            ++$this->support[$actual];

            if ($actual === $predicted) {
                ++$truePositive[$actual];
            } else {
                ++$falsePositive[$predicted];
                ++$falseNegative[$actual];
            }
        }

        $this->computeMetrics($truePositive, $falsePositive, $falseNegative);
        $this->computeAverage();
    }

    /**
     * @return array
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return array
     */
    public function getRecall()
    {
        return $this->recall;
    }

    /**
     * @return array
     */
    public function getF1score()
    {
        return $this->f1score;
    }

    /**
     * @return array
     */
    public function getSupport()
    {
        return $this->support;
    }

    /**
     * @return array
     */
    public function getAverage()
    {
        return $this->average;
    }

    /**
     * @param array $truePositive
     * @param array $falsePositive
     * @param array $falseNegative
     */
    private function computeMetrics(array $truePositive, array $falsePositive, array $falseNegative)
    {
        foreach ($truePositive as $label => $tp) {
            $this->precision[$label] = $this->computePrecision($tp, $falsePositive[$label]);
            $this->recall[$label] = $this->computeRecall($tp, $falseNegative[$label]);
            $this->f1score[$label] = $this->computeF1Score((float) $this->precision[$label], (float) $this->recall[$label]);
        }
    }

    private function computeAverage()
    {
        foreach (['precision', 'recall', 'f1score'] as $metric) {
            $values = array_filter($this->{$metric});
            if (empty($values)) {
                $this->average[$metric] = 0.0;
                continue;
            }
            $this->average[$metric] = array_sum($values) / count($values);
        }
    }

    /**
     * @param int $truePositive
     * @param int $falsePositive
     *
     * @return float|string
     */
    private function computePrecision(int $truePositive, int $falsePositive)
    {
        if (0 == ($divider = $truePositive + $falsePositive)) {
            return 0.0;
        }

        return $truePositive / $divider;
    }

    /**
     * @param int $truePositive
     * @param int $falseNegative
     *
     * @return float|string
     */
    private function computeRecall(int $truePositive, int $falseNegative)
    {
        if (0 == ($divider = $truePositive + $falseNegative)) {
            return 0.0;
        }

        return $truePositive / $divider;
    }

    /**
     * @param float $precision
     * @param float $recall
     *
     * @return float
     */
    private function computeF1Score(float $precision, float $recall): float
    {
        if (0 == ($divider = $precision + $recall)) {
            return 0.0;
        }

        return 2.0 * (($precision * $recall) / $divider);
    }

    /**
     * @param array $actualLabels
     * @param array $predictedLabels
     *
     * @return array
     */
    private static function getLabelIndexedArray(array $actualLabels, array $predictedLabels): array
    {
        $labels = array_values(array_unique(array_merge($actualLabels, $predictedLabels)));
        sort($labels);
        $labels = array_combine($labels, array_fill(0, count($labels), 0));

        return $labels;
    }
}
