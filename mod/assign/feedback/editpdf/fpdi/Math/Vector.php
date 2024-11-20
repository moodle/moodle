<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\Math;

/**
 * A simple 2D-Vector class
 */
class Vector
{
    /**
     * @var float
     */
    protected $x;

    /**
     * @var float
     */
    protected $y;

    /**
     * @param int|float $x
     * @param int|float $y
     */
    public function __construct($x = .0, $y = .0)
    {
        $this->x = (float)$x;
        $this->y = (float)$y;
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param Matrix $matrix
     * @return Vector
     */
    public function multiplyWithMatrix(Matrix $matrix)
    {
        list($a, $b, $c, $d, $e, $f) = $matrix->getValues();
        $x = $a * $this->x + $c * $this->y + $e;
        $y = $b * $this->x + $d * $this->y + $f;

        return new self($x, $y);
    }
}
