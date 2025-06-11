<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfReader\DataStructure;

use setasign\Fpdi\Math\Vector;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfArray;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;
use setasign\Fpdi\PdfParser\Type\PdfType;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;

/**
 * Class representing a rectangle
 */
class Rectangle
{
    /**
     * @var int|float
     */
    protected $llx;

    /**
     * @var int|float
     */
    protected $lly;

    /**
     * @var int|float
     */
    protected $urx;

    /**
     * @var int|float
     */
    protected $ury;

    /**
     * Create a rectangle instance by a PdfArray.
     *
     * @param PdfArray|mixed $array
     * @param PdfParser $parser
     * @return Rectangle
     * @throws PdfTypeException
     * @throws CrossReferenceException
     * @throws PdfParserException
     */
    public static function byPdfArray($array, PdfParser $parser)
    {
        $array = PdfArray::ensure(PdfType::resolve($array, $parser), 4)->value;
        $ax = PdfNumeric::ensure(PdfType::resolve($array[0], $parser))->value;
        $ay = PdfNumeric::ensure(PdfType::resolve($array[1], $parser))->value;
        $bx = PdfNumeric::ensure(PdfType::resolve($array[2], $parser))->value;
        $by = PdfNumeric::ensure(PdfType::resolve($array[3], $parser))->value;

        return new self($ax, $ay, $bx, $by);
    }

    public static function byVectors(Vector $ll, Vector $ur)
    {
        return new self($ll->getX(), $ll->getY(), $ur->getX(), $ur->getY());
    }

    /**
     * Rectangle constructor.
     *
     * @param float|int $ax
     * @param float|int $ay
     * @param float|int $bx
     * @param float|int $by
     */
    public function __construct($ax, $ay, $bx, $by)
    {
        $this->llx = \min($ax, $bx);
        $this->lly = \min($ay, $by);
        $this->urx = \max($ax, $bx);
        $this->ury = \max($ay, $by);
    }

    /**
     * Get the width of the rectangle.
     *
     * @return float|int
     */
    public function getWidth()
    {
        return $this->urx - $this->llx;
    }

    /**
     * Get the height of the rectangle.
     *
     * @return float|int
     */
    public function getHeight()
    {
        return $this->ury - $this->lly;
    }

    /**
     * Get the lower left abscissa.
     *
     * @return float|int
     */
    public function getLlx()
    {
        return $this->llx;
    }

    /**
     * Get the lower left ordinate.
     *
     * @return float|int
     */
    public function getLly()
    {
        return $this->lly;
    }

    /**
     * Get the upper right abscissa.
     *
     * @return float|int
     */
    public function getUrx()
    {
        return $this->urx;
    }

    /**
     * Get the upper right ordinate.
     *
     * @return float|int
     */
    public function getUry()
    {
        return $this->ury;
    }

    /**
     * Get the rectangle as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            $this->llx,
            $this->lly,
            $this->urx,
            $this->ury
        ];
    }

    /**
     * Get the rectangle as a PdfArray.
     *
     * @return PdfArray
     */
    public function toPdfArray()
    {
        $array = new PdfArray();
        $array->value[] = PdfNumeric::create($this->llx);
        $array->value[] = PdfNumeric::create($this->lly);
        $array->value[] = PdfNumeric::create($this->urx);
        $array->value[] = PdfNumeric::create($this->ury);

        return $array;
    }
}
