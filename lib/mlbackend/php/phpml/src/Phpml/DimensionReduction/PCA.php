<?php

declare(strict_types=1);

namespace Phpml\DimensionReduction;

use Phpml\Math\Statistic\Covariance;
use Phpml\Math\Statistic\Mean;

class PCA extends EigenTransformerBase
{
    /**
     * Temporary storage for mean values for each dimension in given data
     *
     * @var array
     */
    protected $means = [];

    /**
     * @var bool
     */
    protected $fit = false;

    /**
     * PCA (Principal Component Analysis) used to explain given
     * data with lower number of dimensions. This analysis transforms the
     * data to a lower dimensional version of it by conserving a proportion of total variance
     * within the data. It is a lossy data compression technique.<br>
     *
     * @param float $totalVariance Total explained variance to be preserved
     * @param int $numFeatures Number of features to be preserved
     *
     * @throws \Exception
     */
    public function __construct($totalVariance = null, $numFeatures = null)
    {
        if ($totalVariance !== null && ($totalVariance < 0.1 || $totalVariance > 0.99)) {
            throw new \Exception("Total variance can be a value between 0.1 and 0.99");
        }
        if ($numFeatures !== null && $numFeatures <= 0) {
            throw new \Exception("Number of features to be preserved should be greater than 0");
        }
        if ($totalVariance !== null && $numFeatures !== null) {
            throw new \Exception("Either totalVariance or numFeatures should be specified in order to run the algorithm");
        }

        if ($numFeatures !== null) {
            $this->numFeatures = $numFeatures;
        }
        if ($totalVariance !== null) {
            $this->totalVariance = $totalVariance;
        }
    }

    /**
     * Takes a data and returns a lower dimensional version
     * of this data while preserving $totalVariance or $numFeatures. <br>
     * $data is an n-by-m matrix and returned array is
     * n-by-k matrix where k <= m
     *
     * @param array $data
     *
     * @return array
     */
    public function fit(array $data)
    {
        $n = count($data[0]);

        $data = $this->normalize($data, $n);

        $covMatrix = Covariance::covarianceMatrix($data, array_fill(0, $n, 0));

        $this->eigenDecomposition($covMatrix);

        $this->fit = true;

        return $this->reduce($data);
    }

    /**
     * @param array $data
     * @param int $n
     */
    protected function calculateMeans(array $data, int $n)
    {
        // Calculate means for each dimension
        $this->means = [];
        for ($i = 0; $i < $n; ++$i) {
            $column = array_column($data, $i);
            $this->means[] = Mean::arithmetic($column);
        }
    }

    /**
     * Normalization of the data includes subtracting mean from
     * each dimension therefore dimensions will be centered to zero
     *
     * @param array $data
     * @param int   $n
     *
     * @return array
     */
    protected function normalize(array $data, int $n)
    {
        if (empty($this->means)) {
            $this->calculateMeans($data, $n);
        }

        // Normalize data
        foreach ($data as $i => $row) {
            for ($k = 0; $k < $n; ++$k) {
                $data[$i][$k] -= $this->means[$k];
            }
        }

        return $data;
    }

    /**
     * Transforms the given sample to a lower dimensional vector by using
     * the eigenVectors obtained in the last run of <code>fit</code>.
     *
     * @param array $sample
     *
     * @return array
     *
     * @throws \Exception
     */
    public function transform(array $sample)
    {
        if (!$this->fit) {
            throw new \Exception("PCA has not been fitted with respect to original dataset, please run PCA::fit() first");
        }

        if (!is_array($sample[0])) {
            $sample = [$sample];
        }

        $sample = $this->normalize($sample, count($sample[0]));

        return $this->reduce($sample);
    }
}
