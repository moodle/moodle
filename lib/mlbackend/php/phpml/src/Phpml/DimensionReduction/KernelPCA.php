<?php

declare(strict_types=1);

namespace Phpml\DimensionReduction;

use Closure;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Exception\InvalidOperationException;
use Phpml\Math\Distance\Euclidean;
use Phpml\Math\Distance\Manhattan;
use Phpml\Math\Matrix;

class KernelPCA extends PCA
{
    public const KERNEL_RBF = 1;

    public const KERNEL_SIGMOID = 2;

    public const KERNEL_LAPLACIAN = 3;

    public const KERNEL_LINEAR = 4;

    /**
     * Selected kernel function
     *
     * @var int
     */
    protected $kernel;

    /**
     * Gamma value used by the kernel
     *
     * @var float|null
     */
    protected $gamma;

    /**
     * Original dataset used to fit KernelPCA
     *
     * @var array
     */
    protected $data = [];

    /**
     * Kernel principal component analysis (KernelPCA) is an extension of PCA using
     * techniques of kernel methods. It is more suitable for data that involves
     * vectors that are not linearly separable<br><br>
     * Example: <b>$kpca = new KernelPCA(KernelPCA::KERNEL_RBF, null, 2, 15.0);</b>
     * will initialize the algorithm with an RBF kernel having the gamma parameter as 15,0. <br>
     * This transformation will return the same number of rows with only <i>2</i> columns.
     *
     * @param float $totalVariance Total variance to be preserved if numFeatures is not given
     * @param int   $numFeatures   Number of columns to be returned
     * @param float $gamma         Gamma parameter is used with RBF and Sigmoid kernels
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $kernel = self::KERNEL_RBF, ?float $totalVariance = null, ?int $numFeatures = null, ?float $gamma = null)
    {
        if (!in_array($kernel, [self::KERNEL_RBF, self::KERNEL_SIGMOID, self::KERNEL_LAPLACIAN, self::KERNEL_LINEAR], true)) {
            throw new InvalidArgumentException('KernelPCA can be initialized with the following kernels only: Linear, RBF, Sigmoid and Laplacian');
        }

        parent::__construct($totalVariance, $numFeatures);

        $this->kernel = $kernel;
        $this->gamma = $gamma;
    }

    /**
     * Takes a data and returns a lower dimensional version
     * of this data while preserving $totalVariance or $numFeatures. <br>
     * $data is an n-by-m matrix and returned array is
     * n-by-k matrix where k <= m
     */
    public function fit(array $data): array
    {
        $numRows = count($data);
        $this->data = $data;

        if ($this->gamma === null) {
            $this->gamma = 1.0 / $numRows;
        }

        $matrix = $this->calculateKernelMatrix($this->data, $numRows);
        $matrix = $this->centerMatrix($matrix, $numRows);

        $this->eigenDecomposition($matrix);

        $this->fit = true;

        return Matrix::transposeArray($this->eigVectors);
    }

    /**
     * Transforms the given sample to a lower dimensional vector by using
     * the variables obtained during the last run of <code>fit</code>.
     *
     * @throws InvalidArgumentException
     * @throws InvalidOperationException
     */
    public function transform(array $sample): array
    {
        if (!$this->fit) {
            throw new InvalidOperationException('KernelPCA has not been fitted with respect to original dataset, please run KernelPCA::fit() first');
        }

        if (is_array($sample[0])) {
            throw new InvalidArgumentException('KernelPCA::transform() accepts only one-dimensional arrays');
        }

        $pairs = $this->getDistancePairs($sample);

        return $this->projectSample($pairs);
    }

    /**
     * Calculates similarity matrix by use of selected kernel function<br>
     * An n-by-m matrix is given and an n-by-n matrix is returned
     */
    protected function calculateKernelMatrix(array $data, int $numRows): array
    {
        $kernelFunc = $this->getKernel();

        $matrix = [];
        for ($i = 0; $i < $numRows; ++$i) {
            for ($k = 0; $k < $numRows; ++$k) {
                if ($i <= $k) {
                    $matrix[$i][$k] = $kernelFunc($data[$i], $data[$k]);
                } else {
                    $matrix[$i][$k] = $matrix[$k][$i];
                }
            }
        }

        return $matrix;
    }

    /**
     * Kernel matrix is centered in its original space by using the following
     * conversion:
     *
     * K′ = K − N.K −  K.N + N.K.N where N is n-by-n matrix filled with 1/n
     */
    protected function centerMatrix(array $matrix, int $n): array
    {
        $N = array_fill(0, $n, array_fill(0, $n, 1.0 / $n));
        $N = new Matrix($N, false);
        $K = new Matrix($matrix, false);

        // K.N (This term is repeated so we cache it once)
        $K_N = $K->multiply($N);
        // N.K
        $N_K = $N->multiply($K);
        // N.K.N
        $N_K_N = $N->multiply($K_N);

        return $K->subtract($N_K)
            ->subtract($K_N)
            ->add($N_K_N)
            ->toArray();
    }

    /**
     * Returns the callable kernel function
     *
     * @throws \Exception
     */
    protected function getKernel(): Closure
    {
        switch ($this->kernel) {
            case self::KERNEL_LINEAR:
                // k(x,y) = xT.y
                return function ($x, $y) {
                    return Matrix::dot($x, $y)[0];
                };
            case self::KERNEL_RBF:
                // k(x,y)=exp(-γ.|x-y|) where |..| is Euclidean distance
                $dist = new Euclidean();

                return function ($x, $y) use ($dist): float {
                    return exp(-$this->gamma * $dist->sqDistance($x, $y));
                };

            case self::KERNEL_SIGMOID:
                // k(x,y)=tanh(γ.xT.y+c0) where c0=1
                return function ($x, $y): float {
                    $res = Matrix::dot($x, $y)[0] + 1.0;

                    return tanh((float) $this->gamma * $res);
                };

            case self::KERNEL_LAPLACIAN:
                // k(x,y)=exp(-γ.|x-y|) where |..| is Manhattan distance
                $dist = new Manhattan();

                return function ($x, $y) use ($dist): float {
                    return exp(-$this->gamma * $dist->distance($x, $y));
                };

            default:
                // Not reached
                throw new InvalidArgumentException(sprintf('KernelPCA initialized with invalid kernel: %d', $this->kernel));
        }
    }

    protected function getDistancePairs(array $sample): array
    {
        $kernel = $this->getKernel();

        $pairs = [];
        foreach ($this->data as $row) {
            $pairs[] = $kernel($row, $sample);
        }

        return $pairs;
    }

    protected function projectSample(array $pairs): array
    {
        // Normalize eigenvectors by eig = eigVectors / eigValues
        $func = function ($eigVal, $eigVect) {
            $m = new Matrix($eigVect, false);
            $a = $m->divideByScalar($eigVal)->toArray();

            return $a[0];
        };
        $eig = array_map($func, $this->eigValues, $this->eigVectors);

        // return k.dot(eig)
        return Matrix::dot($pairs, $eig);
    }
}
