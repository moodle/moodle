<?php

declare(strict_types=1);

namespace Phpml\DimensionReduction;

use Phpml\Math\LinearAlgebra\EigenvalueDecomposition;
use Phpml\Math\Matrix;

/**
 * Class to compute eigen pairs (values & vectors) of a given matrix
 * with the consideration of numFeatures or totalVariance to be preserved
 *
 * @author hp
 */
abstract class EigenTransformerBase
{
    /**
     * Total variance to be conserved after the reduction
     *
     * @var float
     */
    public $totalVariance = 0.9;

    /**
     * Number of features to be preserved after the reduction
     *
     * @var int
     */
    public $numFeatures = null;

    /**
     * Top eigenvectors of the matrix
     *
     * @var array
     */
    protected $eigVectors = [];

    /**
     * Top eigenValues of the matrix
     *
     * @var array
     */
    protected $eigValues = [];

    /**
     * Calculates eigenValues and eigenVectors of the given matrix. Returns
     * top eigenVectors along with the largest eigenValues. The total explained variance
     * of these eigenVectors will be no less than desired $totalVariance value
     *
     * @param array $matrix
     */
    protected function eigenDecomposition(array $matrix)
    {
        $eig = new EigenvalueDecomposition($matrix);
        $eigVals = $eig->getRealEigenvalues();
        $eigVects= $eig->getEigenvectors();

        $totalEigVal = array_sum($eigVals);
        // Sort eigenvalues in descending order
        arsort($eigVals);

        $explainedVar = 0.0;
        $vectors = [];
        $values = [];
        foreach ($eigVals as $i => $eigVal) {
            $explainedVar += $eigVal / $totalEigVal;
            $vectors[] = $eigVects[$i];
            $values[] = $eigVal;

            if ($this->numFeatures !== null) {
                if (count($vectors) == $this->numFeatures) {
                    break;
                }
            } else {
                if ($explainedVar >= $this->totalVariance) {
                    break;
                }
            }
        }

        $this->eigValues = $values;
        $this->eigVectors = $vectors;
    }

    /**
     * Returns the reduced data
     *
     * @param array $data
     *
     * @return array
     */
    protected function reduce(array $data)
    {
        $m1 = new Matrix($data);
        $m2 = new Matrix($this->eigVectors);

        return $m1->multiply($m2->transpose())->toArray();
    }
}
