<?php

declare(strict_types=1);

namespace Phpml\Exception;

class MatrixException extends \Exception
{
    /**
     * @return MatrixException
     */
    public static function notSquareMatrix()
    {
        return new self('Matrix is not square matrix');
    }

    /**
     * @return MatrixException
     */
    public static function columnOutOfRange()
    {
        return new self('Column out of range');
    }

    /**
     * @return MatrixException
     */
    public static function singularMatrix()
    {
        return new self('Matrix is singular');
    }
}
