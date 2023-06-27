<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2019 Setasign - Jan Slabon (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\Tfpdf;

use setasign\Fpdi\FpdfTplTrait;

/**
 * Class FpdfTpl
 *
 * We need to change some access levels and implement the setPageFormat() method to bring back compatibility to tFPDF.
 *
 * @package setasign\Fpdi\Tfpdf
 */
class FpdfTpl extends \tFPDF
{
    use FpdfTplTrait {
        _putimages as _protectedPutimages;
        _putxobjectdict as _protectedPutxobjectdict;
    }

    /**
     * Make the method public as in tFPDF.
     */
    public function _putimages()
    {
        $this->_protectedPutimages();
    }

    /**
     * Make the method public as in tFPDF.
     */
    public function _putxobjectdict()
    {
        $this->_protectedPutxobjectdict();
    }

    /**
     * Set the page format of the current page.
     *
     * @param array $size An array with two values defining the size.
     * @param string $orientation "L" for landscape, "P" for portrait.
     * @throws \BadMethodCallException
     */
    public function setPageFormat($size, $orientation)
    {
        if ($this->currentTemplateId !== null) {
            throw new \BadMethodCallException('The page format cannot be changed when writing to a template.');
        }

        if (!\in_array($orientation, ['P', 'L'], true)) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid page orientation "%s"! Only "P" and "L" are allowed!',
                $orientation
            ));
        }

        $size = $this->_getpagesize($size);

        if ($orientation != $this->CurOrientation
            || $size[0] != $this->CurPageSize[0]
            || $size[1] != $this->CurPageSize[1]
        ) {
            // New size or orientation
            if ($orientation === 'P') {
                $this->w = $size[0];
                $this->h = $size[1];
            } else {
                $this->w = $size[1];
                $this->h = $size[0];
            }
            $this->wPt = $this->w * $this->k;
            $this->hPt = $this->h * $this->k;
            $this->PageBreakTrigger = $this->h - $this->bMargin;
            $this->CurOrientation = $orientation;
            $this->CurPageSize = $size;

            $this->PageSizes[$this->page] = array($this->wPt, $this->hPt);
        }
    }

    /**
     * @inheritdoc
     */
    protected function _put($s, $newLine = true)
    {
        if ($newLine) {
            $this->buffer .= $s . "\n";
        } else {
            $this->buffer .= $s;
        }
    }
}