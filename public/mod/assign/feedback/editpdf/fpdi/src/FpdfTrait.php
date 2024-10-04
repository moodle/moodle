<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use setasign\Fpdi\PdfParser\Type\PdfNull;
use setasign\Fpdi\PdfParser\Type\PdfType;

/**
 * This trait is used for the implementation of FPDI in FPDF and tFPDF.
 */
trait FpdfTrait
{
    protected function _enddoc()
    {
        parent::_enddoc();
        $this->cleanUp();
    }

    /**
     * Draws an imported page or a template onto the page or another template.
     *
     * Give only one of the size parameters (width, height) to calculate the other one automatically in view to the
     * aspect ratio.
     *
     * @param mixed $tpl The template id
     * @param float|int|array $x The abscissa of upper-left corner. Alternatively you could use an assoc array
     *                           with the keys "x", "y", "width", "height", "adjustPageSize".
     * @param float|int $y The ordinate of upper-left corner.
     * @param float|int|null $width The width.
     * @param float|int|null $height The height.
     * @param bool $adjustPageSize
     * @return array The size
     * @see Fpdi::getTemplateSize()
     */
    public function useTemplate($tpl, $x = 0, $y = 0, $width = null, $height = null, $adjustPageSize = false)
    {
        if (isset($this->importedPages[$tpl])) {
            $size = $this->useImportedPage($tpl, $x, $y, $width, $height, $adjustPageSize);
            if ($this->currentTemplateId !== null) {
                $this->templates[$this->currentTemplateId]['resources']['templates']['importedPages'][$tpl] = $tpl;
            }
            return $size;
        }

        return parent::useTemplate($tpl, $x, $y, $width, $height, $adjustPageSize);
    }

    /**
     * Get the size of an imported page or template.
     *
     * Give only one of the size parameters (width, height) to calculate the other one automatically in view to the
     * aspect ratio.
     *
     * @param mixed $tpl The template id
     * @param float|int|null $width The width.
     * @param float|int|null $height The height.
     * @return array|bool An array with following keys: width, height, 0 (=width), 1 (=height), orientation (L or P)
     */
    public function getTemplateSize($tpl, $width = null, $height = null)
    {
        $size = parent::getTemplateSize($tpl, $width, $height);
        if ($size === false) {
            return $this->getImportedPageSize($tpl, $width, $height);
        }

        return $size;
    }

    /**
     * @throws CrossReferenceException
     * @throws PdfParserException
     */
    protected function _putimages()
    {
        $this->currentReaderId = null;
        parent::_putimages();

        foreach ($this->importedPages as $key => $pageData) {
            $this->_newobj();
            $this->importedPages[$key]['objectNumber'] = $this->n;
            $this->currentReaderId = $pageData['readerId'];
            $this->writePdfType($pageData['stream']);
            $this->_put('endobj');
        }

        foreach (\array_keys($this->readers) as $readerId) {
            $parser = $this->getPdfReader($readerId)->getParser();
            $this->currentReaderId = $readerId;

            while (($objectNumber = \array_pop($this->objectsToCopy[$readerId])) !== null) {
                try {
                    $object = $parser->getIndirectObject($objectNumber);
                } catch (CrossReferenceException $e) {
                    if ($e->getCode() === CrossReferenceException::OBJECT_NOT_FOUND) {
                        $object = PdfIndirectObject::create($objectNumber, 0, new PdfNull());
                    } else {
                        throw $e;
                    }
                }

                $this->writePdfType($object);
            }
        }

        $this->currentReaderId = null;
    }

    /**
     * @inheritdoc
     */
    protected function _putxobjectdict()
    {
        foreach ($this->importedPages as $pageData) {
            $this->_put('/' . $pageData['id'] . ' ' . $pageData['objectNumber'] . ' 0 R');
        }

        parent::_putxobjectdict();
    }

    /**
     * @param int $n
     * @return void
     * @throws PdfParser\Type\PdfTypeException
     */
    protected function _putlinks($n)
    {
        foreach ($this->PageLinks[$n] as $pl) {
            $this->_newobj();
            $rect = sprintf('%.2F %.2F %.2F %.2F', $pl[0], $pl[1], $pl[0] + $pl[2], $pl[1] - $pl[3]);
            $this->_put('<</Type /Annot /Subtype /Link /Rect [' . $rect . ']', false);
            if (is_string($pl[4])) {
                if (isset($pl['importedLink'])) {
                    $this->_put('/A <</S /URI /URI (' . $this->_escape($pl[4]) . ')>>');
                    $values = $pl['importedLink']['pdfObject']->value;

                    foreach ($values as $name => $entry) {
                        $this->_put('/' . $name . ' ', false);
                        $this->writePdfType($entry);
                    }

                    if (isset($pl['quadPoints'])) {
                        $s = '/QuadPoints[';
                        foreach ($pl['quadPoints'] as $value) {
                            $s .= sprintf('%.2F ', $value);
                        }
                        $s .= ']';
                        $this->_put($s);
                    }
                } else {
                    $this->_put('/A <</S /URI /URI ' . $this->_textstring($pl[4]) . '>>');
                    $this->_put('/Border [0 0 0]', false);
                }
                $this->_put('>>');
            } else {
                $this->_put('/Border [0 0 0] ', false);
                $l = $this->links[$pl[4]];
                if (isset($this->PageInfo[$l[0]]['size'])) {
                    $h = $this->PageInfo[$l[0]]['size'][1];
                } else {
                    $h = ($this->DefOrientation === 'P')
                        ? $this->DefPageSize[1] * $this->k
                        : $this->DefPageSize[0] * $this->k;
                }
                $this->_put(sprintf(
                    '/Dest [%d 0 R /XYZ 0 %.2F null]>>',
                    $this->PageInfo[$l[0]]['n'],
                    $h - $l[1] * $this->k
                ));
            }
            $this->_put('endobj');
        }
    }

    protected function _put($s, $newLine = true)
    {
        if ($newLine) {
            $this->buffer .= $s . "\n";
        } else {
            $this->buffer .= $s;
        }
    }
}
