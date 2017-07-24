<?php

declare(strict_types=1);

namespace Phpml\Math;

use Phpml\Math\LinearAlgebra\LUDecomposition;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Exception\MatrixException;

class Matrix
{
    /**
     * @var array
     */
    private $matrix;

    /**
     * @var int
     */
    private $rows;

    /**
     * @var int
     */
    private $columns;

    /**
     * @var float
     */
    private $determinant;

    /**
     * @param array $matrix
     * @param bool  $validate
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $matrix, bool $validate = true)
    {
        // When a row vector is given
        if (!is_array($matrix[0])) {
            $this->rows = 1;
            $this->columns = count($matrix);
            $matrix = [$matrix];
        } else {
            $this->rows = count($matrix);
            $this->columns = count($matrix[0]);
        }

        if ($validate) {
            for ($i = 0; $i < $this->rows; ++$i) {
                if (count($matrix[$i]) !== $this->columns) {
                    throw InvalidArgumentException::matrixDimensionsDidNotMatch();
                }
            }
        }

        $this->matrix = $matrix;
    }

    /**
     * @param array $array
     *
     * @return Matrix
     */
    public static function fromFlatArray(array $array)
    {
        $matrix = [];
        foreach ($array as $value) {
            $matrix[] = [$value];
        }

        return new self($matrix);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->matrix;
    }

    /**
     * @return float
     */
    public function toScalar()
    {
        return $this->matrix[0][0];
    }

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return int
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param $column
     *
     * @return array
     *
     * @throws MatrixException
     */
    public function getColumnValues($column)
    {
        if ($column >= $this->columns) {
            throw MatrixException::columnOutOfRange();
        }

        return array_column($this->matrix, $column);
    }


    /**
     * @return float|int
     *
     * @throws MatrixException
     */
    public function getDeterminant()
    {
        if ($this->determinant) {
            return $this->determinant;
        }

        if (!$this->isSquare()) {
            throw MatrixException::notSquareMatrix();
        }

        $lu = new LUDecomposition($this);

        return $this->determinant = $lu->det();
    }

    /**
     * @return bool
     */
    public function isSquare()
    {
        return $this->columns === $this->rows;
    }

    /**
     * @return Matrix
     */
    public function transpose()
    {
        if ($this->rows == 1) {
            $matrix = array_map(function ($el) {
                return [$el];
            }, $this->matrix[0]);
        } else {
            $matrix = array_map(null, ...$this->matrix);
        }

        return new self($matrix, false);
    }

    /**
     * @param Matrix $matrix
     *
     * @return Matrix
     *
     * @throws InvalidArgumentException
     */
    public function multiply(Matrix $matrix)
    {
        if ($this->columns != $matrix->getRows()) {
            throw InvalidArgumentException::inconsistentMatrixSupplied();
        }

        $product = [];
        $multiplier = $matrix->toArray();
        for ($i = 0; $i < $this->rows; ++$i) {
            $columns = $matrix->getColumns();
            for ($j = 0; $j < $columns; ++$j) {
                $product[$i][$j] = 0;
                for ($k = 0; $k < $this->columns; ++$k) {
                    $product[$i][$j] += $this->matrix[$i][$k] * $multiplier[$k][$j];
                }
            }
        }

        return new self($product, false);
    }

    /**
     * @param $value
     *
     * @return Matrix
     */
    public function divideByScalar($value)
    {
        $newMatrix = [];
        for ($i = 0; $i < $this->rows; ++$i) {
            for ($j = 0; $j < $this->columns; ++$j) {
                $newMatrix[$i][$j] = $this->matrix[$i][$j] / $value;
            }
        }

        return new self($newMatrix, false);
    }

    /**
     * @param $value
     *
     * @return Matrix
     */
    public function multiplyByScalar($value)
    {
        $newMatrix = [];
        for ($i = 0; $i < $this->rows; ++$i) {
            for ($j = 0; $j < $this->columns; ++$j) {
                $newMatrix[$i][$j] = $this->matrix[$i][$j] * $value;
            }
        }

        return new self($newMatrix, false);
    }

    /**
     * Element-wise addition of the matrix with another one
     *
     * @param Matrix $other
     *
     * @return Matrix
     */
    public function add(Matrix $other)
    {
        return $this->_add($other);
    }

    /**
     * Element-wise subtracting of another matrix from this one
     *
     * @param Matrix $other
     *
     * @return Matrix
     */
    public function subtract(Matrix $other)
    {
        return $this->_add($other, -1);
    }

    /**
     * Element-wise addition or substraction depending on the given sign parameter
     *
     * @param Matrix $other
     * @param int    $sign
     *
     * @return Matrix
     */
    protected function _add(Matrix $other, $sign = 1)
    {
        $a1 = $this->toArray();
        $a2 = $other->toArray();

        $newMatrix = [];
        for ($i = 0; $i < $this->rows; ++$i) {
            for ($k = 0; $k < $this->columns; ++$k) {
                $newMatrix[$i][$k] = $a1[$i][$k] + $sign * $a2[$i][$k];
            }
        }

        return new self($newMatrix, false);
    }

    /**
     * @return Matrix
     *
     * @throws MatrixException
     */
    public function inverse()
    {
        if (!$this->isSquare()) {
            throw MatrixException::notSquareMatrix();
        }

        $LU = new LUDecomposition($this);
        $identity = $this->getIdentity();
        $inverse = $LU->solve($identity);

        return new self($inverse, false);
    }

    /**
     * Returns diagonal identity matrix of the same size of this matrix
     *
     * @return Matrix
     */
    protected function getIdentity()
    {
        $array = array_fill(0, $this->rows, array_fill(0, $this->columns, 0));
        for ($i = 0; $i < $this->rows; ++$i) {
            $array[$i][$i] = 1;
        }

        return new self($array, false);
    }

    /**
     * @param int $row
     * @param int $column
     *
     * @return Matrix
     */
    public function crossOut(int $row, int $column)
    {
        $newMatrix = [];
        $r = 0;
        for ($i = 0; $i < $this->rows; ++$i) {
            $c = 0;
            if ($row != $i) {
                for ($j = 0; $j < $this->columns; ++$j) {
                    if ($column != $j) {
                        $newMatrix[$r][$c] = $this->matrix[$i][$j];
                        ++$c;
                    }
                }
                ++$r;
            }
        }

        return new self($newMatrix, false);
    }

    /**
     * @return bool
     */
    public function isSingular() : bool
    {
        return 0 == $this->getDeterminant();
    }

    /**
     * Returns the transpose of given array
     *
     * @param array $array
     *
     * @return array
     */
    public static function transposeArray(array $array)
    {
        return (new self($array, false))->transpose()->toArray();
    }

    /**
     * Returns the dot product of two arrays<br>
     * Matrix::dot(x, y) ==> x.y'
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function dot(array $array1, array $array2)
    {
        $m1 = new self($array1, false);
        $m2 = new self($array2, false);

        return $m1->multiply($m2->transpose())->toArray()[0];
    }
}
