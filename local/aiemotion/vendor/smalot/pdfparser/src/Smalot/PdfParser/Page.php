<?php

/**
 * @file
 *          This file is part of the PdfParser library.
 *
 * @author  Sébastien MALOT <sebastien@malot.fr>
 *
 * @date    2017-01-03
 *
 * @license LGPLv3
 *
 * @url     <https://github.com/smalot/pdfparser>
 *
 *  PdfParser is a pdf library written in PHP, extraction oriented.
 *  Copyright (C) 2017 - Sébastien MALOT <sebastien@malot.fr>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with this program.
 *  If not, see <http://www.pdfparser.org/sites/default/LICENSE.txt>.
 */

namespace Smalot\PdfParser;

use Smalot\PdfParser\Element\ElementArray;
use Smalot\PdfParser\Element\ElementMissing;
use Smalot\PdfParser\Element\ElementNull;
use Smalot\PdfParser\Element\ElementXRef;

class Page extends PDFObject
{
    /**
     * @var Font[]
     */
    protected $fonts;

    /**
     * @var PDFObject[]
     */
    protected $xobjects;

    /**
     * @var array
     */
    protected $dataTm;

    /**
     * @param array<\Smalot\PdfParser\Font> $fonts
     *
     * @internal
     */
    public function setFonts($fonts)
    {
        if (empty($this->fonts)) {
            $this->fonts = $fonts;
        }
    }

    /**
     * @return Font[]
     */
    public function getFonts()
    {
        if (null !== $this->fonts) {
            return $this->fonts;
        }

        $resources = $this->get('Resources');

        if (method_exists($resources, 'has') && $resources->has('Font')) {
            if ($resources->get('Font') instanceof ElementMissing) {
                return [];
            }

            if ($resources->get('Font') instanceof Header) {
                $fonts = $resources->get('Font')->getElements();
            } else {
                $fonts = $resources->get('Font')->getHeader()->getElements();
            }

            $table = [];

            foreach ($fonts as $id => $font) {
                if ($font instanceof Font) {
                    $table[$id] = $font;

                    // Store too on cleaned id value (only numeric)
                    $id = preg_replace('/[^0-9\.\-_]/', '', $id);
                    if ('' != $id) {
                        $table[$id] = $font;
                    }
                }
            }

            return $this->fonts = $table;
        }

        return [];
    }

    public function getFont(string $id): ?Font
    {
        $fonts = $this->getFonts();

        if (isset($fonts[$id])) {
            return $fonts[$id];
        }

        // According to the PDF specs (https://www.adobe.com/content/dam/acom/en/devnet/pdf/pdfs/PDF32000_2008.pdf, page 238)
        // "The font resource name presented to the Tf operator is arbitrary, as are the names for all kinds of resources"
        // Instead, we search for the unfiltered name first and then do this cleaning as a fallback, so all tests still pass.

        if (isset($fonts[$id])) {
            return $fonts[$id];
        } else {
            $id = preg_replace('/[^0-9\.\-_]/', '', $id);
            if (isset($fonts[$id])) {
                return $fonts[$id];
            }
        }

        return null;
    }

    /**
     * Support for XObject
     *
     * @return PDFObject[]
     */
    public function getXObjects()
    {
        if (null !== $this->xobjects) {
            return $this->xobjects;
        }

        $resources = $this->get('Resources');

        if (method_exists($resources, 'has') && $resources->has('XObject')) {
            if ($resources->get('XObject') instanceof Header) {
                $xobjects = $resources->get('XObject')->getElements();
            } else {
                $xobjects = $resources->get('XObject')->getHeader()->getElements();
            }

            $table = [];

            foreach ($xobjects as $id => $xobject) {
                $table[$id] = $xobject;

                // Store too on cleaned id value (only numeric)
                $id = preg_replace('/[^0-9\.\-_]/', '', $id);
                if ('' != $id) {
                    $table[$id] = $xobject;
                }
            }

            return $this->xobjects = $table;
        }

        return [];
    }

    public function getXObject(string $id): ?PDFObject
    {
        $xobjects = $this->getXObjects();

        if (isset($xobjects[$id])) {
            return $xobjects[$id];
        }

        return null;
        /*$id = preg_replace('/[^0-9\.\-_]/', '', $id);

        if (isset($xobjects[$id])) {
            return $xobjects[$id];
        } else {
            return null;
        }*/
    }

    public function getText(?self $page = null): string
    {
        if ($contents = $this->get('Contents')) {
            if ($contents instanceof ElementMissing) {
                return '';
            } elseif ($contents instanceof ElementNull) {
                return '';
            } elseif ($contents instanceof PDFObject) {
                $elements = $contents->getHeader()->getElements();

                if (is_numeric(key($elements))) {
                    $new_content = '';

                    foreach ($elements as $element) {
                        if ($element instanceof ElementXRef) {
                            $new_content .= $element->getObject()->getContent();
                        } else {
                            $new_content .= $element->getContent();
                        }
                    }

                    $header = new Header([], $this->document);
                    $contents = new PDFObject($this->document, $header, $new_content, $this->config);
                }
            } elseif ($contents instanceof ElementArray) {
                // Create a virtual global content.
                $new_content = '';

                foreach ($contents->getContent() as $content) {
                    $new_content .= $content->getContent()."\n";
                }

                $header = new Header([], $this->document);
                $contents = new PDFObject($this->document, $header, $new_content, $this->config);
            }

            /*
             * Elements referencing each other on the same page can cause endless loops during text parsing.
             * To combat this we keep a recursionStack containing already parsed elements on the page.
             * The stack is only emptied here after getting text from a page.
             */
            $contentsText = $contents->getText($this);
            PDFObject::$recursionStack = [];

            return $contentsText;
        }

        return '';
    }

    /**
     * Return true if the current page is a (setasign\Fpdi\Fpdi) FPDI/FPDF document
     *
     * The metadata 'Producer' should have the value of "FPDF" . FPDF_VERSION if the
     * pdf file was generated by FPDF/Fpfi.
     *
     * @return bool true is the current page is a FPDI/FPDF document
     */
    public function isFpdf(): bool
    {
        if (\array_key_exists('Producer', $this->document->getDetails())
            && \is_string($this->document->getDetails()['Producer'])
            && 0 === strncmp($this->document->getDetails()['Producer'], 'FPDF', 4)) {
            return true;
        }

        return false;
    }

    /**
     * Return the page number of the PDF document of the page object
     *
     * @return int the page number
     */
    public function getPageNumber(): int
    {
        $pages = $this->document->getPages();
        $numOfPages = \count($pages);
        for ($pageNum = 0; $pageNum < $numOfPages; ++$pageNum) {
            if ($pages[$pageNum] === $this) {
                break;
            }
        }

        return $pageNum;
    }

    /**
     * Return the Object of the page if the document is a FPDF/FPDI document
     *
     * If the document was generated by FPDF/FPDI it returns the
     * PDFObject of the given page
     *
     * @return PDFObject The PDFObject for the page
     */
    public function getPDFObjectForFpdf(): PDFObject
    {
        $pageNum = $this->getPageNumber();
        $xObjects = $this->getXObjects();

        return $xObjects[$pageNum];
    }

    /**
     * Return a new PDFObject of the document created with FPDF/FPDI
     *
     * For a document generated by FPDF/FPDI, it generates a
     * new PDFObject for that document
     *
     * @return PDFObject The PDFObject
     */
    public function createPDFObjectForFpdf(): PDFObject
    {
        $pdfObject = $this->getPDFObjectForFpdf();
        $new_content = $pdfObject->getContent();
        $header = $pdfObject->getHeader();
        $config = $pdfObject->config;

        return new PDFObject($pdfObject->document, $header, $new_content, $config);
    }

    /**
     * Return page if document is a FPDF/FPDI document
     *
     * @return Page The page
     */
    public function createPageForFpdf(): self
    {
        $pdfObject = $this->getPDFObjectForFpdf();
        $new_content = $pdfObject->getContent();
        $header = $pdfObject->getHeader();
        $config = $pdfObject->config;

        return new self($pdfObject->document, $header, $new_content, $config);
    }

    public function getTextArray(?self $page = null): array
    {
        if ($this->isFpdf()) {
            $pdfObject = $this->getPDFObjectForFpdf();
            $newPdfObject = $this->createPDFObjectForFpdf();

            return $newPdfObject->getTextArray($pdfObject);
        } else {
            if ($contents = $this->get('Contents')) {
                if ($contents instanceof ElementMissing) {
                    return [];
                } elseif ($contents instanceof ElementNull) {
                    return [];
                } elseif ($contents instanceof PDFObject) {
                    $elements = $contents->getHeader()->getElements();

                    if (is_numeric(key($elements))) {
                        $new_content = '';

                        /** @var PDFObject $element */
                        foreach ($elements as $element) {
                            if ($element instanceof ElementXRef) {
                                $new_content .= $element->getObject()->getContent();
                            } else {
                                $new_content .= $element->getContent();
                            }
                        }

                        $header = new Header([], $this->document);
                        $contents = new PDFObject($this->document, $header, $new_content, $this->config);
                    } else {
                        try {
                            $contents->getTextArray($this);
                        } catch (\Throwable $e) {
                            return $contents->getTextArray();
                        }
                    }
                } elseif ($contents instanceof ElementArray) {
                    // Create a virtual global content.
                    $new_content = '';

                    /** @var PDFObject $content */
                    foreach ($contents->getContent() as $content) {
                        $new_content .= $content->getContent()."\n";
                    }

                    $header = new Header([], $this->document);
                    $contents = new PDFObject($this->document, $header, $new_content, $this->config);
                }

                return $contents->getTextArray($this);
            }

            return [];
        }
    }

    /**
     * Gets all the text data with its internal representation of the page.
     *
     * Returns an array with the data and the internal representation
     */
    public function extractRawData(): array
    {
        /*
         * Now you can get the complete content of the object with the text on it
         */
        $extractedData = [];
        $content = $this->get('Contents');
        $values = $content->getContent();
        if (isset($values) && \is_array($values)) {
            $text = '';
            foreach ($values as $section) {
                $text .= $section->getContent();
            }
            $sectionsText = $this->getSectionsText($text);
            foreach ($sectionsText as $sectionText) {
                $commandsText = $this->getCommandsText($sectionText);
                foreach ($commandsText as $command) {
                    $extractedData[] = $command;
                }
            }
        } else {
            if ($this->isFpdf()) {
                $content = $this->getPDFObjectForFpdf();
            }
            $sectionsText = $content->getSectionsText($content->getContent());
            foreach ($sectionsText as $sectionText) {
                $commandsText = $content->getCommandsText($sectionText);
                foreach ($commandsText as $command) {
                    $extractedData[] = $command;
                }
            }
        }

        return $extractedData;
    }

    /**
     * Gets all the decoded text data with it internal representation from a page.
     *
     * @param array $extractedRawData the extracted data return by extractRawData or
     *                                null if extractRawData should be called
     *
     * @return array An array with the data and the internal representation
     */
    public function extractDecodedRawData(?array $extractedRawData = null): array
    {
        if (!isset($extractedRawData) || !$extractedRawData) {
            $extractedRawData = $this->extractRawData();
        }
        $currentFont = null; /** @var Font $currentFont */
        $clippedFont = null;
        $fpdfPage = null;
        if ($this->isFpdf()) {
            $fpdfPage = $this->createPageForFpdf();
        }
        foreach ($extractedRawData as &$command) {
            if ('Tj' == $command['o'] || 'TJ' == $command['o']) {
                $data = $command['c'];
                if (!\is_array($data)) {
                    $tmpText = '';
                    if (isset($currentFont)) {
                        $tmpText = $currentFont->decodeOctal($data);
                        // $tmpText = $currentFont->decodeHexadecimal($tmpText, false);
                    }
                    $tmpText = str_replace(
                        ['\\\\', '\(', '\)', '\n', '\r', '\t', '\ '],
                        ['\\', '(', ')', "\n", "\r", "\t", ' '],
                        $tmpText
                    );
                    $tmpText = mb_convert_encoding($tmpText, 'UTF-8', 'ISO-8859-1');
                    if (isset($currentFont)) {
                        $tmpText = $currentFont->decodeContent($tmpText);
                    }
                    $command['c'] = $tmpText;
                    continue;
                }
                $numText = \count($data);
                for ($i = 0; $i < $numText; ++$i) {
                    if (0 != ($i % 2)) {
                        continue;
                    }
                    $tmpText = $data[$i]['c'];
                    $decodedText = isset($currentFont) ? $currentFont->decodeOctal($tmpText) : $tmpText;
                    $decodedText = str_replace(
                        ['\\\\', '\(', '\)', '\n', '\r', '\t', '\ '],
                        ['\\', '(', ')', "\n", "\r", "\t", ' '],
                        $decodedText
                    );

                    $decodedText = mb_convert_encoding($decodedText, 'UTF-8', 'ISO-8859-1');

                    if (isset($currentFont)) {
                        $decodedText = $currentFont->decodeContent($decodedText);
                    }
                    $command['c'][$i]['c'] = $decodedText;
                    continue;
                }
            } elseif ('Tf' == $command['o'] || 'TF' == $command['o']) {
                $fontId = explode(' ', $command['c'])[0];
                // If document is a FPDI/FPDF the $page has the correct font
                $currentFont = isset($fpdfPage) ? $fpdfPage->getFont($fontId) : $this->getFont($fontId);
                continue;
            } elseif ('Q' == $command['o']) {
                $currentFont = $clippedFont;
            } elseif ('q' == $command['o']) {
                $clippedFont = $currentFont;
            }
        }

        return $extractedRawData;
    }

    /**
     * Gets just the Text commands that are involved in text positions and
     * Text Matrix (Tm)
     *
     * It extract just the PDF commands that are involved with text positions, and
     * the Text Matrix (Tm). These are: BT, ET, TL, Td, TD, Tm, T*, Tj, ', ", and TJ
     *
     * @param array $extractedDecodedRawData The data extracted by extractDecodeRawData.
     *                                       If it is null, the method extractDecodeRawData is called.
     *
     * @return array An array with the text command of the page
     */
    public function getDataCommands(?array $extractedDecodedRawData = null): array
    {
        if (!isset($extractedDecodedRawData) || !$extractedDecodedRawData) {
            $extractedDecodedRawData = $this->extractDecodedRawData();
        }
        $extractedData = [];
        foreach ($extractedDecodedRawData as $command) {
            switch ($command['o']) {
                /*
                 * BT
                 * Begin a text object, inicializind the Tm and Tlm to identity matrix
                 */
                case 'BT':
                    $extractedData[] = $command;
                    break;
                    /*
                     * cm
                     * Concatenation Matrix that will transform all following Tm
                     */
                case 'cm':
                    $extractedData[] = $command;
                    break;
                    /*
                     * ET
                     * End a text object, discarding the text matrix
                     */
                case 'ET':
                    $extractedData[] = $command;
                    break;

                    /*
                     * leading TL
                     * Set the text leading, Tl, to leading. Tl is used by the T*, ' and " operators.
                     * Initial value: 0
                     */
                case 'TL':
                    $extractedData[] = $command;
                    break;

                    /*
                     * tx ty Td
                     * Move to the start of the next line, offset form the start of the
                     * current line by tx, ty.
                     */
                case 'Td':
                    $extractedData[] = $command;
                    break;

                    /*
                     * tx ty TD
                     * Move to the start of the next line, offset form the start of the
                     * current line by tx, ty. As a side effect, this operator set the leading
                     * parameter in the text state. This operator has the same effect as the
                     * code:
                     * -ty TL
                     * tx ty Td
                     */
                case 'TD':
                    $extractedData[] = $command;
                    break;

                    /*
                     * a b c d e f Tm
                     * Set the text matrix, Tm, and the text line matrix, Tlm. The operands are
                     * all numbers, and the initial value for Tm and Tlm is the identity matrix
                     * [1 0 0 1 0 0]
                     */
                case 'Tm':
                    $extractedData[] = $command;
                    break;

                    /*
                     * T*
                     * Move to the start of the next line. This operator has the same effect
                     * as the code:
                     * 0 Tl Td
                     * Where Tl is the current leading parameter in the text state.
                     */
                case 'T*':
                    $extractedData[] = $command;
                    break;

                    /*
                     * string Tj
                     * Show a Text String
                     */
                case 'Tj':
                    $extractedData[] = $command;
                    break;

                    /*
                     * string '
                     * Move to the next line and show a text string. This operator has the
                     * same effect as the code:
                     * T*
                     * string Tj
                     */
                case "'":
                    $extractedData[] = $command;
                    break;

                    /*
                     * aw ac string "
                     * Move to the next lkine and show a text string, using aw as the word
                     * spacing and ac as the character spacing. This operator has the same
                     * effect as the code:
                     * aw Tw
                     * ac Tc
                     * string '
                     * Tw set the word spacing, Tw, to wordSpace.
                     * Tc Set the character spacing, Tc, to charsSpace.
                     */
                case '"':
                    $extractedData[] = $command;
                    break;

                case 'Tf':
                case 'TF':
                    $extractedData[] = $command;
                    break;

                    /*
                     * array TJ
                     * Show one or more text strings allow individual glyph positioning.
                     * Each lement of array con be a string or a number. If the element is
                     * a string, this operator shows the string. If it is a number, the
                     * operator adjust the text position by that amount; that is, it translates
                     * the text matrix, Tm. This amount is substracted form the current
                     * horizontal or vertical coordinate, depending on the writing mode.
                     * in the default coordinate system, a positive adjustment has the effect
                     * of moving the next glyph painted either to the left or down by the given
                     * amount.
                     */
                case 'TJ':
                    $extractedData[] = $command;
                    break;
                    /*
                     * q
                     * Save current graphics state to stack
                     */
                case 'q':
                    /*
                     * Q
                     * Load last saved graphics state from stack
                     */
                case 'Q':
                    $extractedData[] = $command;
                    break;
                default:
            }
        }

        return $extractedData;
    }

    /**
     * Gets the Text Matrix of the text in the page
     *
     * Return an array where every item is an array where the first item is the
     * Text Matrix (Tm) and the second is a string with the text data.  The Text matrix
     * is an array of 6 numbers. The last 2 numbers are the coordinates X and Y of the
     * text. The first 4 numbers has to be with Scalation, Rotation and Skew of the text.
     *
     * @param array $dataCommands the data extracted by getDataCommands
     *                            if null getDataCommands is called
     *
     * @return array an array with the data of the page including the Tm information
     *               of any text in the page
     */
    public function getDataTm(?array $dataCommands = null): array
    {
        if (!isset($dataCommands) || !$dataCommands) {
            $dataCommands = $this->getDataCommands();
        }

        /*
         * At the beginning of a text object Tm is the identity matrix
         */
        $defaultTm = ['1', '0', '0', '1', '0', '0'];
        $concatTm = ['1', '0', '0', '1', '0', '0'];
        $graphicsStatesStack = [];
        /*
         *  Set the text leading used by T*, ' and " operators
         */
        $defaultTl = 0;

        /*
         *  Set default values for font data
         */
        $defaultFontId = -1;
        $defaultFontSize = 1;

        /*
         * Indexes of horizontal/vertical scaling and X,Y-coordinates in the matrix (Tm)
         */
        $hSc = 0; // horizontal scaling
        /**
         * index of vertical scaling in the array that encodes the text matrix.
         * for more information: https://github.com/smalot/pdfparser/pull/559#discussion_r1053415500
         */
        $vSc = 3;
        $x = 4;
        $y = 5;

        /*
         * x,y-coordinates of text space origin in user units
         *
         * These will be assigned the value of the currently printed string
         */
        $Tx = 0;
        $Ty = 0;

        $Tm = $defaultTm;
        $Tl = $defaultTl;
        $fontId = $defaultFontId;
        $fontSize = $defaultFontSize; // reflects fontSize set by Tf or Tfs

        $extractedTexts = $this->getTextArray();
        $extractedData = [];
        foreach ($dataCommands as $command) {
            // If we've used up all the texts from getTextArray(), exit
            // so we aren't accessing non-existent array indices
            // Fixes 'undefined array key' errors in Issues #575, #576
            if (\count($extractedTexts) <= \count($extractedData)) {
                break;
            }
            $currentText = $extractedTexts[\count($extractedData)];
            switch ($command['o']) {
                /*
                 * BT
                 * Begin a text object, initializing the Tm and Tlm to identity matrix
                 */
                case 'BT':
                    $Tm = $defaultTm;
                    $Tl = $defaultTl;
                    $Tx = 0;
                    $Ty = 0;
                    break;

                case 'cm':
                    $newConcatTm = (array) explode(' ', $command['c']);
                    $TempMatrix = [];
                    // Multiply with previous concatTm
                    $TempMatrix[0] = (float) $concatTm[0] * (float) $newConcatTm[0] + (float) $concatTm[1] * (float) $newConcatTm[2];
                    $TempMatrix[1] = (float) $concatTm[0] * (float) $newConcatTm[1] + (float) $concatTm[1] * (float) $newConcatTm[3];
                    $TempMatrix[2] = (float) $concatTm[2] * (float) $newConcatTm[0] + (float) $concatTm[3] * (float) $newConcatTm[2];
                    $TempMatrix[3] = (float) $concatTm[2] * (float) $newConcatTm[1] + (float) $concatTm[3] * (float) $newConcatTm[3];
                    $TempMatrix[4] = (float) $concatTm[4] * (float) $newConcatTm[0] + (float) $concatTm[5] * (float) $newConcatTm[2] + (float) $newConcatTm[4];
                    $TempMatrix[5] = (float) $concatTm[4] * (float) $newConcatTm[1] + (float) $concatTm[5] * (float) $newConcatTm[3] + (float) $newConcatTm[5];
                    $concatTm = $TempMatrix;
                    break;
                    /*
                     * ET
                     * End a text object
                     */
                case 'ET':
                    break;

                    /*
                     * text leading TL
                     * Set the text leading, Tl, to leading. Tl is used by the T*, ' and " operators.
                     * Initial value: 0
                     */
                case 'TL':
                    // scaled text leading
                    $Tl = (float) $command['c'] * (float) $Tm[$vSc];
                    break;

                    /*
                     * tx ty Td
                     * Move to the start of the next line, offset from the start of the
                     * current line by tx, ty.
                     */
                case 'Td':
                    $coord = explode(' ', $command['c']);
                    $Tx += (float) $coord[0] * (float) $Tm[$hSc];
                    $Ty += (float) $coord[1] * (float) $Tm[$vSc];
                    $Tm[$x] = (string) $Tx;
                    $Tm[$y] = (string) $Ty;
                    break;

                    /*
                     * tx ty TD
                     * Move to the start of the next line, offset form the start of the
                     * current line by tx, ty. As a side effect, this operator set the leading
                     * parameter in the text state. This operator has the same effect as the
                     * code:
                     * -ty TL
                     * tx ty Td
                     */
                case 'TD':
                    $coord = explode(' ', $command['c']);
                    $Tl = -((float) $coord[1] * (float) $Tm[$vSc]);
                    $Tx += (float) $coord[0] * (float) $Tm[$hSc];
                    $Ty += (float) $coord[1] * (float) $Tm[$vSc];
                    $Tm[$x] = (string) $Tx;
                    $Tm[$y] = (string) $Ty;
                    break;

                    /*
                     * a b c d e f Tm
                     * Set the text matrix, Tm, and the text line matrix, Tlm. The operands are
                     * all numbers, and the initial value for Tm and Tlm is the identity matrix
                     * [1 0 0 1 0 0]
                     */
                case 'Tm':
                    $Tm = explode(' ', $command['c']);
                    $TempMatrix = [];
                    $TempMatrix[0] = (float) $Tm[0] * (float) $concatTm[0] + (float) $Tm[1] * (float) $concatTm[2];
                    $TempMatrix[1] = (float) $Tm[0] * (float) $concatTm[1] + (float) $Tm[1] * (float) $concatTm[3];
                    $TempMatrix[2] = (float) $Tm[2] * (float) $concatTm[0] + (float) $Tm[3] * (float) $concatTm[2];
                    $TempMatrix[3] = (float) $Tm[2] * (float) $concatTm[1] + (float) $Tm[3] * (float) $concatTm[3];
                    $TempMatrix[4] = (float) $Tm[4] * (float) $concatTm[0] + (float) $Tm[5] * (float) $concatTm[2] + (float) $concatTm[4];
                    $TempMatrix[5] = (float) $Tm[4] * (float) $concatTm[1] + (float) $Tm[5] * (float) $concatTm[3] + (float) $concatTm[5];
                    $Tm = $TempMatrix;
                    $Tx = (float) $Tm[$x];
                    $Ty = (float) $Tm[$y];
                    break;

                    /*
                     * T*
                     * Move to the start of the next line. This operator has the same effect
                     * as the code:
                     * 0 Tl Td
                     * Where Tl is the current leading parameter in the text state.
                     */
                case 'T*':
                    $Ty -= $Tl;
                    $Tm[$y] = (string) $Ty;
                    break;

                    /*
                     * string Tj
                     * Show a Text String
                     */
                case 'Tj':
                    $data = [$Tm, $currentText];
                    if ($this->config->getDataTmFontInfoHasToBeIncluded()) {
                        $data[] = $fontId;
                        $data[] = $fontSize;
                    }
                    $extractedData[] = $data;
                    break;

                    /*
                     * string '
                     * Move to the next line and show a text string. This operator has the
                     * same effect as the code:
                     * T*
                     * string Tj
                     */
                case "'":
                    $Ty -= $Tl;
                    $Tm[$y] = (string) $Ty;
                    $extractedData[] = [$Tm, $currentText];
                    break;

                    /*
                     * aw ac string "
                     * Move to the next line and show a text string, using aw as the word
                     * spacing and ac as the character spacing. This operator has the same
                     * effect as the code:
                     * aw Tw
                     * ac Tc
                     * string '
                     * Tw set the word spacing, Tw, to wordSpace.
                     * Tc Set the character spacing, Tc, to charsSpace.
                     */
                case '"':
                    $data = explode(' ', $currentText);
                    $Ty -= $Tl;
                    $Tm[$y] = (string) $Ty;
                    $extractedData[] = [$Tm, $data[2]]; // Verify
                    break;

                case 'Tf':
                    /*
                     * From PDF 1.0 specification, page 106:
                     *     fontname size Tf Set font and size
                     *     Sets the text font and text size in the graphics state. There is no default value for
                     *     either fontname or size; they must be selected using Tf before drawing any text.
                     *     fontname is a resource name. size is a number expressed in text space units.
                     *
                     * Source: https://ia902503.us.archive.org/10/items/pdfy-0vt8s-egqFwDl7L2/PDF%20Reference%201.0.pdf
                     * Introduced with https://github.com/smalot/pdfparser/pull/516
                     */
                    list($fontId, $fontSize) = explode(' ', $command['c'], 2);
                    break;

                    /*
                     * array TJ
                     * Show one or more text strings allow individual glyph positioning.
                     * Each lement of array con be a string or a number. If the element is
                     * a string, this operator shows the string. If it is a number, the
                     * operator adjust the text position by that amount; that is, it translates
                     * the text matrix, Tm. This amount is substracted form the current
                     * horizontal or vertical coordinate, depending on the writing mode.
                     * in the default coordinate system, a positive adjustment has the effect
                     * of moving the next glyph painted either to the left or down by the given
                     * amount.
                     */
                case 'TJ':
                    $data = [$Tm, $currentText];
                    if ($this->config->getDataTmFontInfoHasToBeIncluded()) {
                        $data[] = $fontId;
                        $data[] = $fontSize;
                    }
                    $extractedData[] = $data;
                    break;
                    /*
                     * q
                     * Save current graphics state to stack
                     */
                case 'q':
                    $graphicsStatesStack[] = $concatTm;
                    break;
                    /*
                     * Q
                     * Load last saved graphics state from stack
                     */
                case 'Q':
                    $concatTm = array_pop($graphicsStatesStack);
                    break;
                default:
            }
        }
        $this->dataTm = $extractedData;

        return $extractedData;
    }

    /**
     * Gets text data that are around the given coordinates (X,Y)
     *
     * If the text is in near the given coordinates (X,Y) (or the TM info),
     * the text is returned.  The extractedData return by getDataTm, could be use to see
     * where is the coordinates of a given text, using the TM info for it.
     *
     * @param float $x      The X value of the coordinate to search for. if null
     *                      just the Y value is considered (same Row)
     * @param float $y      The Y value of the coordinate to search for
     *                      just the X value is considered (same column)
     * @param float $xError The value less or more to consider an X to be "near"
     * @param float $yError The value less or more to consider an Y to be "near"
     *
     * @return array An array of text that are near the given coordinates. If no text
     *               "near" the x,y coordinate, an empty array is returned. If Both, x
     *               and y coordinates are null, null is returned.
     */
    public function getTextXY(?float $x = null, ?float $y = null, float $xError = 0, float $yError = 0): array
    {
        if (!isset($this->dataTm) || !$this->dataTm) {
            $this->getDataTm();
        }

        if (null !== $x) {
            $x = (float) $x;
        }

        if (null !== $y) {
            $y = (float) $y;
        }

        if (null === $x && null === $y) {
            return [];
        }

        $xError = (float) $xError;
        $yError = (float) $yError;

        $extractedData = [];
        foreach ($this->dataTm as $item) {
            $tm = $item[0];
            $xTm = (float) $tm[4];
            $yTm = (float) $tm[5];
            $text = $item[1];
            if (null === $y) {
                if (($xTm >= ($x - $xError))
                    && ($xTm <= ($x + $xError))) {
                    $extractedData[] = [$tm, $text];
                    continue;
                }
            }
            if (null === $x) {
                if (($yTm >= ($y - $yError))
                    && ($yTm <= ($y + $yError))) {
                    $extractedData[] = [$tm, $text];
                    continue;
                }
            }
            if (($xTm >= ($x - $xError))
                && ($xTm <= ($x + $xError))
                && ($yTm >= ($y - $yError))
                && ($yTm <= ($y + $yError))) {
                $extractedData[] = [$tm, $text];
                continue;
            }
        }

        return $extractedData;
    }
}
