<?php

declare(strict_types=1);

namespace Phpml\DimensionReduction;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Exception\InvalidOperationException;
use Phpml\Math\Matrix;

class LDA extends EigenTransformerBase
{
    /**
     * @var bool
     */
    public $fit = false;

    /**
     * @var array
     */
    public $labels = [];

    /**
     * @var array
     */
    public $means = [];

    /**
     * @var array
     */
    public $counts = [];

    /**
     * @var float[]
     */
    public $overallMean = [];

    /**
     * Linear Discriminant Analysis (LDA) is used to reduce the dimensionality
     * of the data. Unlike Principal Component Analysis (PCA), it is a supervised
     * technique that requires the class labels in order to fit the data to a
     * lower dimensional space. <br><br>
     * The algorithm can be initialized by speciyfing
     * either with the totalVariance(a value between 0.1 and 0.99)
     * or numFeatures (number of features in the dataset) to be preserved.
     *
     * @param float|null $totalVariance Total explained variance to be preserved
     * @param int|null   $numFeatures   Number of features to be preserved
     *
     * @throws InvalidArgumentException
     */
    public function __construct(?float $totalVariance = null, ?int $numFeatures = null)
    {
        if ($totalVariance !== null && ($totalVariance < 0.1 || $totalVariance > 0.99)) {
            throw new InvalidArgumentException('Total variance can be a value between 0.1 and 0.99');
        }

        if ($numFeatures !== null && $numFeatures <= 0) {
            throw new InvalidArgumentException('Number of features to be preserved should be greater than 0');
        }

        if (($totalVariance !== null) === ($numFeatures !== null)) {
            throw new InvalidArgumentException('Either totalVariance or numFeatures should be specified in order to run the algorithm');
        }

        if ($numFeatures !== null) {
            $this->numFeatures = $numFeatures;
        }

        if ($totalVariance !== null) {
            $this->totalVariance = $totalVariance;
        }
    }

    /**
     * Trains the algorithm to transform the given data to a lower dimensional space.
     */
    public function fit(array $data, array $classes): array
    {
        $this->labels = $this->getLabels($classes);
        $this->means = $this->calculateMeans($data, $classes);

        $sW = $this->calculateClassVar($data, $classes);
        $sB = $this->calculateClassCov();

        $S = $sW->inverse()->multiply($sB);
        $this->eigenDecomposition($S->toArray());

        $this->fit = true;

        return $this->reduce($data);
    }

    /**
     * Transforms the given sample to a lower dimensional vector by using
     * the eigenVectors obtained in the last run of <code>fit</code>.
     *
     * @throws InvalidOperationException
     */
    public function transform(array $sample): array
    {
        if (!$this->fit) {
            throw new InvalidOperationException('LDA has not been fitted with respect to original dataset, please run LDA::fit() first');
        }

        if (!is_array($sample[0])) {
            $sample = [$sample];
        }

        return $this->reduce($sample);
    }

    /**
     * Returns unique labels in the dataset
     */
    protected function getLabels(array $classes): array
    {
        $counts = array_count_values($classes);

        return array_keys($counts);
    }

    /**
     * Calculates mean of each column for each class and returns
     * n by m matrix where n is number of labels and m is number of columns
     */
    protected function calculateMeans(array $data, array $classes): array
    {
        $means = [];
        $counts = [];
        $overallMean = array_fill(0, count($data[0]), 0.0);

        foreach ($data as $index => $row) {
            $label = array_search($classes[$index], $this->labels, true);

            foreach ($row as $col => $val) {
                if (!isset($means[$label][$col])) {
                    $means[$label][$col] = 0.0;
                }

                $means[$label][$col] += $val;
                $overallMean[$col] += $val;
            }

            if (!isset($counts[$label])) {
                $counts[$label] = 0;
            }

            ++$counts[$label];
        }

        foreach ($means as $index => $row) {
            foreach ($row as $col => $sum) {
                $means[$index][$col] = $sum / $counts[$index];
            }
        }

        // Calculate overall mean of the dataset for each column
        $numElements = array_sum($counts);
        $map = function ($el) use ($numElements) {
            return $el / $numElements;
        };
        $this->overallMean = array_map($map, $overallMean);
        $this->counts = $counts;

        return $means;
    }

    /**
     * Returns in-class scatter matrix for each class, which
     * is a n by m matrix where n is number of classes and
     * m is number of columns
     */
    protected function calculateClassVar(array $data, array $classes): Matrix
    {
        // s is an n (number of classes) by m (number of column) matrix
        $s = array_fill(0, count($data[0]), array_fill(0, count($data[0]), 0));
        $sW = new Matrix($s, false);

        foreach ($data as $index => $row) {
            $label = array_search($classes[$index], $this->labels, true);
            $means = $this->means[$label];

            $row = $this->calculateVar($row, $means);

            $sW = $sW->add($row);
        }

        return $sW;
    }

    /**
     * Returns between-class scatter matrix for each class, which
     * is an n by m matrix where n is number of classes and
     * m is number of columns
     */
    protected function calculateClassCov(): Matrix
    {
        // s is an n (number of classes) by m (number of column) matrix
        $s = array_fill(0, count($this->overallMean), array_fill(0, count($this->overallMean), 0));
        $sB = new Matrix($s, false);

        foreach ($this->means as $index => $classMeans) {
            $row = $this->calculateVar($classMeans, $this->overallMean);
            $N = $this->counts[$index];
            $sB = $sB->add($row->multiplyByScalar($N));
        }

        return $sB;
    }

    /**
     * Returns the result of the calculation (x - m)T.(x - m)
     */
    protected function calculateVar(array $row, array $means): Matrix
    {
        $x = new Matrix($row, false);
        $m = new Matrix($means, false);
        $diff = $x->subtract($m);

        return $diff->transpose()->multiply($diff);
    }
}
