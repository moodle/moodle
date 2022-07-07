<?php

declare(strict_types=1);

/**
 * @package JAMA
 *
 * For an m-by-n matrix A with m >= n, the LU decomposition is an m-by-n
 * unit lower triangular matrix L, an n-by-n upper triangular matrix U,
 * and a permutation vector piv of length m so that A(piv,:) = L*U.
 * If m < n, then L is m-by-m and U is m-by-n.
 *
 * The LU decompostion with pivoting always exists, even if the matrix is
 * singular, so the constructor will never fail. The primary use of the
 * LU decomposition is in the solution of square systems of simultaneous
 * linear equations. This will fail if isNonsingular() returns false.
 *
 * @author Paul Meagher
 * @author Bartosz Matosiuk
 * @author Michael Bommarito
 *
 * @version 1.1
 *
 * @license PHP v3.0
 *
 *  Slightly changed to adapt the original code to PHP-ML library
 *  @date 2017/04/24
 *
 *  @author Mustafa Karabulut
 */

namespace Phpml\Math\LinearAlgebra;

use Phpml\Exception\MatrixException;
use Phpml\Math\Matrix;

class LUDecomposition
{
    /**
     * Decomposition storage
     *
     * @var array
     */
    private $LU = [];

    /**
     * Row dimension.
     *
     * @var int
     */
    private $m;

    /**
     * Column dimension.
     *
     * @var int
     */
    private $n;

    /**
     * Pivot sign.
     *
     * @var int
     */
    private $pivsign;

    /**
     * Internal storage of pivot vector.
     *
     * @var array
     */
    private $piv = [];

    /**
     * Constructs Structure to access L, U and piv.
     *
     * @param Matrix $A Rectangular matrix
     *
     * @throws MatrixException
     */
    public function __construct(Matrix $A)
    {
        if ($A->getRows() !== $A->getColumns()) {
            throw new MatrixException('Matrix is not square matrix');
        }

        // Use a "left-looking", dot-product, Crout/Doolittle algorithm.
        $this->LU = $A->toArray();
        $this->m = $A->getRows();
        $this->n = $A->getColumns();
        for ($i = 0; $i < $this->m; ++$i) {
            $this->piv[$i] = $i;
        }

        $this->pivsign = 1;
        $LUcolj = [];

        // Outer loop.
        for ($j = 0; $j < $this->n; ++$j) {
            // Make a copy of the j-th column to localize references.
            for ($i = 0; $i < $this->m; ++$i) {
                $LUcolj[$i] = &$this->LU[$i][$j];
            }

            // Apply previous transformations.
            for ($i = 0; $i < $this->m; ++$i) {
                $LUrowi = $this->LU[$i];
                // Most of the time is spent in the following dot product.
                $kmax = min($i, $j);
                $s = 0.0;
                for ($k = 0; $k < $kmax; ++$k) {
                    $s += $LUrowi[$k] * $LUcolj[$k];
                }

                $LUrowi[$j] = $LUcolj[$i] -= $s;
            }

            // Find pivot and exchange if necessary.
            $p = $j;
            for ($i = $j + 1; $i < $this->m; ++$i) {
                if (abs($LUcolj[$i] ?? 0) > abs($LUcolj[$p] ?? 0)) {
                    $p = $i;
                }
            }

            if ($p != $j) {
                for ($k = 0; $k < $this->n; ++$k) {
                    $t = $this->LU[$p][$k];
                    $this->LU[$p][$k] = $this->LU[$j][$k];
                    $this->LU[$j][$k] = $t;
                }

                $k = $this->piv[$p];
                $this->piv[$p] = $this->piv[$j];
                $this->piv[$j] = $k;
                $this->pivsign *= -1;
            }

            // Compute multipliers.
            if (($j < $this->m) && ($this->LU[$j][$j] != 0.0)) {
                for ($i = $j + 1; $i < $this->m; ++$i) {
                    $this->LU[$i][$j] /= $this->LU[$j][$j];
                }
            }
        }
    }

    /**
     * Get lower triangular factor.
     *
     * @return Matrix Lower triangular factor
     */
    public function getL(): Matrix
    {
        $L = [];
        for ($i = 0; $i < $this->m; ++$i) {
            for ($j = 0; $j < $this->n; ++$j) {
                if ($i > $j) {
                    $L[$i][$j] = $this->LU[$i][$j];
                } elseif ($i == $j) {
                    $L[$i][$j] = 1.0;
                } else {
                    $L[$i][$j] = 0.0;
                }
            }
        }

        return new Matrix($L);
    }

    /**
     * Get upper triangular factor.
     *
     * @return Matrix Upper triangular factor
     */
    public function getU(): Matrix
    {
        $U = [];
        for ($i = 0; $i < $this->n; ++$i) {
            for ($j = 0; $j < $this->n; ++$j) {
                if ($i <= $j) {
                    $U[$i][$j] = $this->LU[$i][$j];
                } else {
                    $U[$i][$j] = 0.0;
                }
            }
        }

        return new Matrix($U);
    }

    /**
     * Return pivot permutation vector.
     *
     * @return array Pivot vector
     */
    public function getPivot(): array
    {
        return $this->piv;
    }

    /**
     * Alias for getPivot
     *
     * @see getPivot
     */
    public function getDoublePivot(): array
    {
        return $this->getPivot();
    }

    /**
     * Is the matrix nonsingular?
     *
     * @return bool true if U, and hence A, is nonsingular.
     */
    public function isNonsingular(): bool
    {
        for ($j = 0; $j < $this->n; ++$j) {
            if ($this->LU[$j][$j] == 0) {
                return false;
            }
        }

        return true;
    }

    public function det(): float
    {
        $d = $this->pivsign;
        for ($j = 0; $j < $this->n; ++$j) {
            $d *= $this->LU[$j][$j];
        }

        return (float) $d;
    }

    /**
     * Solve A*X = B
     *
     * @param Matrix $B A Matrix with as many rows as A and any number of columns.
     *
     * @return array X so that L*U*X = B(piv,:)
     *
     * @throws MatrixException
     */
    public function solve(Matrix $B): array
    {
        if ($B->getRows() != $this->m) {
            throw new MatrixException('Matrix is not square matrix');
        }

        if (!$this->isNonsingular()) {
            throw new MatrixException('Matrix is singular');
        }

        // Copy right hand side with pivoting
        $nx = $B->getColumns();
        $X = $this->getSubMatrix($B->toArray(), $this->piv, 0, $nx - 1);
        // Solve L*Y = B(piv,:)
        for ($k = 0; $k < $this->n; ++$k) {
            for ($i = $k + 1; $i < $this->n; ++$i) {
                for ($j = 0; $j < $nx; ++$j) {
                    $X[$i][$j] -= $X[$k][$j] * $this->LU[$i][$k];
                }
            }
        }

        // Solve U*X = Y;
        for ($k = $this->n - 1; $k >= 0; --$k) {
            for ($j = 0; $j < $nx; ++$j) {
                $X[$k][$j] /= $this->LU[$k][$k];
            }

            for ($i = 0; $i < $k; ++$i) {
                for ($j = 0; $j < $nx; ++$j) {
                    $X[$i][$j] -= $X[$k][$j] * $this->LU[$i][$k];
                }
            }
        }

        return $X;
    }

    protected function getSubMatrix(array $matrix, array $RL, int $j0, int $jF): array
    {
        $m = count($RL);
        $n = $jF - $j0;
        $R = array_fill(0, $m, array_fill(0, $n + 1, 0.0));

        for ($i = 0; $i < $m; ++$i) {
            for ($j = $j0; $j <= $jF; ++$j) {
                $R[$i][$j - $j0] = $matrix[$RL[$i]][$j];
            }
        }

        return $R;
    }
}
