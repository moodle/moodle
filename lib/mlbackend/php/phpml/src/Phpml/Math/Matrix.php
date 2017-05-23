<?php

declare(strict_types=1);

namespace Phpml\Math;

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

        return $this->determinant = $this->calculateDeterminant();
    }

    /**
     * @return float|int
     *
     * @throws MatrixException
     */
    private function calculateDeterminant()
    {
        $determinant = 0;
        if ($this->rows == 1 && $this->columns == 1) {
            $determinant = $this->matrix[0][0];
        } elseif ($this->rows == 2 && $this->columns == 2) {
            $determinant =
                $this->matrix[0][0] * $this->matrix[1][1] -
                $this->matrix[0][1] * $this->matrix[1][0];
        } else {
            for ($j = 0; $j < $this->columns; ++$j) {
                $subMatrix = $this->crossOut(0, $j);
                $minor = $this->matrix[0][$j] * $subMatrix->getDeterminant();
                $determinant += fmod((float) $j, 2.0) == 0 ? $minor : -$minor;
            }
        }

        return $determinant;
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
     */
    public function add(Matrix $other)
    {
        return $this->_add($other);
    }

    /**
     * Element-wise subtracting of another matrix from this one
     *
     * @param Matrix $other
     */
    public function subtract(Matrix $other)
    {
        return $this->_add($other, -1);
    }

    /**
     * Element-wise addition or substraction depending on the given sign parameter
     *
     * @param Matrix $other
     * @param type $sign
     */
    protected function _add(Matrix $other, $sign = 1)
    {
        $a1 = $this->toArray();
        $a2 = $other->toArray();

        $newMatrix = [];
        for ($i=0; $i < $this->rows; $i++) {
            for ($k=0; $k < $this->columns; $k++) {
                $newMatrix[$i][$k] = $a1[$i][$k] + $sign * $a2[$i][$k];
            }
        }

        return new Matrix($newMatrix, false);
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

        if ($this->isSingular()) {
            throw MatrixException::singularMatrix();
        }

        $newMatrix = [];
        for ($i = 0; $i < $this->rows; ++$i) {
            for ($j = 0; $j < $this->columns; ++$j) {
                $minor = $this->crossOut($i, $j)->getDeterminant();
                $newMatrix[$i][$j] = fmod((float) ($i + $j), 2.0) == 0 ? $minor : -$minor;
            }
        }

        $cofactorMatrix = new self($newMatrix, false);

        return $cofactorMatrix->transpose()->divideByScalar($this->getDeterminant());
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
        return (new Matrix($array, false))->transpose()->toArray();
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
        $m1 = new Matrix($array1, false);
        $m2 = new Matrix($array2, false);

        return $m1->multiply($m2->transpose())->toArray()[0];
    }
}
