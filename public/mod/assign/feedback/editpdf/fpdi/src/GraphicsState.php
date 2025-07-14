<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi;

use setasign\Fpdi\Math\Matrix;
use setasign\Fpdi\Math\Vector;

/**
 * A simple graphic state class which holds the current transformation matrix.
 */
class GraphicsState
{
    /**
     * @var Matrix
     */
    protected $ctm;

    /**
     * @param Matrix|null $ctm
     */
    public function __construct(?Matrix $ctm = null)
    {
        if ($ctm === null) {
            $ctm = new Matrix();
        }

        $this->ctm = $ctm;
    }

    /**
     * @param Matrix $matrix
     * @return $this
     */
    public function add(Matrix $matrix)
    {
        $this->ctm = $matrix->multiply($this->ctm);
        return $this;
    }

    /**
     * @param int|float $x
     * @param int|float $y
     * @param int|float $angle
     * @return $this
     */
    public function rotate($x, $y, $angle)
    {
        if (abs($angle) < 1e-5) {
            return  $this;
        }

        $angle = deg2rad($angle);
        $c = cos($angle);
        $s = sin($angle);

        $this->add(new Matrix($c, $s, -$s, $c, $x, $y));

        return $this->translate(-$x, -$y);
    }

    /**
     * @param int|float $shiftX
     * @param int|float $shiftY
     * @return $this
     */
    public function translate($shiftX, $shiftY)
    {
        return $this->add(new Matrix(1, 0, 0, 1, $shiftX, $shiftY));
    }

    /**
     * @param int|float $scaleX
     * @param int|float $scaleY
     * @return $this
     */
    public function scale($scaleX, $scaleY)
    {
        return $this->add(new Matrix($scaleX, 0, 0, $scaleY, 0, 0));
    }

    /**
     * @param Vector $vector
     * @return Vector
     */
    public function toUserSpace(Vector $vector)
    {
        return $vector->multiplyWithMatrix($this->ctm);
    }
}
