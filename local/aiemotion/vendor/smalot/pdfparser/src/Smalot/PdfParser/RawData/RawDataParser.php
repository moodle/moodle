<?php

/**
 * This file is based on code of tecnickcom/TCPDF PDF library.
 *
 * Original author Nicola Asuni (info@tecnick.com) and
 * contributors (https://github.com/tecnickcom/TCPDF/graphs/contributors).
 *
 * @see https://github.com/tecnickcom/TCPDF
 *
 * Original code was licensed on the terms of the LGPL v3.
 *
 * ------------------------------------------------------------------------------
 *
 * @file This file is part of the PdfParser library.
 *
 * @author  Konrad Abicht <k.abicht@gmail.com>
 *
 * @date    2020-01-06
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

namespace Smalot\PdfParser\RawData;

use Smalot\PdfParser\Config;
use Smalot\PdfParser\Exception\EmptyPdfException;
use Smalot\PdfParser\Exception\MissingPdfHeaderException;

class RawDataParser
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Configuration array.
     *
     * @var array<string,bool>
     */
    protected $cfg = [
        // if `true` ignore filter decoding errors
        'ignore_filter_decoding_errors' => true,
        // if `true` ignore missing filter decoding errors
        'ignore_missing_filter_decoders' => true,
    ];

    protected $filterHelper;
    protected $objects;

    /**
     * @param array $cfg Configuration array, default is []
     */
    public function __construct($cfg = [], ?Config $config = null)
    {
        // merge given array with default values
        $this->cfg = array_merge($this->cfg, $cfg);

        $this->filterHelper = new FilterHelper();
        $this->config = $config ?: new Config();
    }

    /**
     * Decode the specified stream.
     *
     * @param string $pdfData PDF data
     * @param array  $sdic    Stream's dictionary array
     * @param string $stream  Stream to decode
     *
     * @return array containing decoded stream data and remaining filters
     *
     * @throws \Exception
     */
    protected function decodeStream(string $pdfData, array $xref, array $sdic, string $stream): array
    {
        // get stream length and filters
        $slength = \strlen($stream);
        if ($slength <= 0) {
            return ['', []];
        }
        $filters = [];
        foreach ($sdic as $k => $v) {
            if ('/' == $v[0]) {
                if (('Length' == $v[1]) && (isset($sdic[$k + 1])) && ('numeric' == $sdic[$k + 1][0])) {
                    // get declared stream length
                    $declength = (int) $sdic[$k + 1][1];
                    if ($declength < $slength) {
                        $stream = substr($stream, 0, $declength);
                        $slength = $declength;
                    }
                } elseif (('Filter' == $v[1]) && (isset($sdic[$k + 1]))) {
                    // resolve indirect object
                    $objval = $this->getObjectVal($pdfData, $xref, $sdic[$k + 1]);
                    if ('/' == $objval[0]) {
                        // single filter
                        $filters[] = $objval[1];
                    } elseif ('[' == $objval[0]) {
                        // array of filters
                        foreach ($objval[1] as $flt) {
                            if ('/' == $flt[0]) {
                                $filters[] = $flt[1];
                            }
                        }
                    }
                }
            }
        }

        // decode the stream
        $remaining_filters = [];
        foreach ($filters as $filter) {
            if (\in_array($filter, $this->filterHelper->getAvailableFilters(), true)) {
                try {
                    $stream = $this->filterHelper->decodeFilter($filter, $stream, $this->config->getDecodeMemoryLimit());
                } catch (\Exception $e) {
                    $emsg = $e->getMessage();
                    if ((('~' == $emsg[0]) && !$this->cfg['ignore_missing_filter_decoders'])
                        || (('~' != $emsg[0]) && !$this->cfg['ignore_filter_decoding_errors'])
                    ) {
                        throw new \Exception($e->getMessage());
                    }
                }
            } else {
                // add missing filter to array
                $remaining_filters[] = $filter;
            }
        }

        return [$stream, $remaining_filters];
    }

    /**
     * Decode the Cross-Reference section
     *
     * @param string     $pdfData        PDF data
     * @param int        $startxref      Offset at which the xref section starts (position of the 'xref' keyword)
     * @param array      $xref           Previous xref array (if any)
     * @param array<int> $visitedOffsets Array of visited offsets to prevent infinite loops
     *
     * @return array containing xref and trailer data
     *
     * @throws \Exception
     */
    protected function decodeXref(string $pdfData, int $startxref, array $xref = [], array $visitedOffsets = []): array
    {
        $startxref += 4; // 4 is the length of the word 'xref'
        // skip initial white space chars
        $offset = $startxref + strspn($pdfData, $this->config->getPdfWhitespaces(), $startxref);
        // initialize object number
        $obj_num = 0;
        // search for cross-reference entries or subsection
        while (preg_match('/([0-9]+)[\x20]([0-9]+)[\x20]?([nf]?)(\r\n|[\x20]?[\r\n])/', $pdfData, $matches, \PREG_OFFSET_CAPTURE, $offset) > 0) {
            if ($matches[0][1] != $offset) {
                // we are on another section
                break;
            }
            $offset += \strlen($matches[0][0]);
            if ('n' == $matches[3][0]) {
                // create unique object index: [object number]_[generation number]
                $index = $obj_num.'_'.(int) $matches[2][0];
                // check if object already exist
                if (!isset($xref['xref'][$index])) {
                    // store object offset position
                    $xref['xref'][$index] = (int) $matches[1][0];
                }
                ++$obj_num;
            } elseif ('f' == $matches[3][0]) {
                ++$obj_num;
            } else {
                // object number (index)
                $obj_num = (int) $matches[1][0];
            }
        }
        // get trailer data
        if (preg_match('/trailer[\s]*<<(.*)>>/isU', $pdfData, $matches, \PREG_OFFSET_CAPTURE, $offset) > 0) {
            $trailer_data = $matches[1][0];
            if (!isset($xref['trailer']) || empty($xref['trailer'])) {
                // get only the last updated version
                $xref['trailer'] = [];
                // parse trailer_data
                if (preg_match('/Size[\s]+([0-9]+)/i', $trailer_data, $matches) > 0) {
                    $xref['trailer']['size'] = (int) $matches[1];
                }
                if (preg_match('/Root[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer_data, $matches) > 0) {
                    $xref['trailer']['root'] = (int) $matches[1].'_'.(int) $matches[2];
                }
                if (preg_match('/Encrypt[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer_data, $matches) > 0) {
                    $xref['trailer']['encrypt'] = (int) $matches[1].'_'.(int) $matches[2];
                }
                if (preg_match('/Info[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer_data, $matches) > 0) {
                    $xref['trailer']['info'] = (int) $matches[1].'_'.(int) $matches[2];
                }
                if (preg_match('/ID[\s]*[\[][\s]*[<]([^>]*)[>][\s]*[<]([^>]*)[>]/i', $trailer_data, $matches) > 0) {
                    $xref['trailer']['id'] = [];
                    $xref['trailer']['id'][0] = $matches[1];
                    $xref['trailer']['id'][1] = $matches[2];
                }
            }
            if (preg_match('/Prev[\s]+([0-9]+)/i', $trailer_data, $matches) > 0) {
                $offset = (int) $matches[1];
                if (0 != $offset) {
                    // get previous xref
                    $xref = $this->getXrefData($pdfData, $offset, $xref, $visitedOffsets);
                }
            }
        } else {
            throw new \Exception('Unable to find trailer');
        }

        return $xref;
    }

    /**
     * Decode the Cross-Reference Stream section
     *
     * @param string     $pdfData        PDF data
     * @param int        $startxref      Offset at which the xref section starts
     * @param array      $xref           Previous xref array (if any)
     * @param array<int> $visitedOffsets Array of visited offsets to prevent infinite loops
     *
     * @return array containing xref and trailer data
     *
     * @throws \Exception if unknown PNG predictor detected
     */
    protected function decodeXrefStream(string $pdfData, int $startxref, array $xref = [], array $visitedOffsets = []): array
    {
        // try to read Cross-Reference Stream
        $xrefobj = $this->getRawObject($pdfData, $startxref);
        $xrefcrs = $this->getIndirectObject($pdfData, $xref, $xrefobj[1], $startxref, true);
        if (!isset($xref['trailer']) || empty($xref['trailer'])) {
            // get only the last updated version
            $xref['trailer'] = [];
            $filltrailer = true;
        } else {
            $filltrailer = false;
        }
        if (!isset($xref['xref'])) {
            $xref['xref'] = [];
        }
        $valid_crs = false;
        $columns = 0;
        $predictor = null;
        $sarr = $xrefcrs[0][1];
        if (!\is_array($sarr)) {
            $sarr = [];
        }

        $wb = [];

        foreach ($sarr as $k => $v) {
            if (
                ('/' == $v[0])
                && ('Type' == $v[1])
                && (
                    isset($sarr[$k + 1])
                    && '/' == $sarr[$k + 1][0]
                    && 'XRef' == $sarr[$k + 1][1]
                )
            ) {
                $valid_crs = true;
            } elseif (('/' == $v[0]) && ('Index' == $v[1]) && (isset($sarr[$k + 1]))) {
                // initialize list for: first object number in the subsection / number of objects
                $index_blocks = [];
                for ($m = 0; $m < \count($sarr[$k + 1][1]); $m += 2) {
                    $index_blocks[] = [$sarr[$k + 1][1][$m][1], $sarr[$k + 1][1][$m + 1][1]];
                }
            } elseif (('/' == $v[0]) && ('Prev' == $v[1]) && (isset($sarr[$k + 1]) && ('numeric' == $sarr[$k + 1][0]))) {
                // get previous xref offset
                $prevxref = (int) $sarr[$k + 1][1];
            } elseif (('/' == $v[0]) && ('W' == $v[1]) && (isset($sarr[$k + 1]))) {
                // number of bytes (in the decoded stream) of the corresponding field
                $wb[0] = (int) $sarr[$k + 1][1][0][1];
                $wb[1] = (int) $sarr[$k + 1][1][1][1];
                $wb[2] = (int) $sarr[$k + 1][1][2][1];
            } elseif (('/' == $v[0]) && ('DecodeParms' == $v[1]) && (isset($sarr[$k + 1][1]))) {
                $decpar = $sarr[$k + 1][1];
                foreach ($decpar as $kdc => $vdc) {
                    if (
                        '/' == $vdc[0]
                        && 'Columns' == $vdc[1]
                        && (
                            isset($decpar[$kdc + 1])
                            && 'numeric' == $decpar[$kdc + 1][0]
                        )
                    ) {
                        $columns = (int) $decpar[$kdc + 1][1];
                    } elseif (
                        '/' == $vdc[0]
                        && 'Predictor' == $vdc[1]
                        && (
                            isset($decpar[$kdc + 1])
                            && 'numeric' == $decpar[$kdc + 1][0]
                        )
                    ) {
                        $predictor = (int) $decpar[$kdc + 1][1];
                    }
                }
            } elseif ($filltrailer) {
                if (('/' == $v[0]) && ('Size' == $v[1]) && (isset($sarr[$k + 1]) && ('numeric' == $sarr[$k + 1][0]))) {
                    $xref['trailer']['size'] = $sarr[$k + 1][1];
                } elseif (('/' == $v[0]) && ('Root' == $v[1]) && (isset($sarr[$k + 1]) && ('objref' == $sarr[$k + 1][0]))) {
                    $xref['trailer']['root'] = $sarr[$k + 1][1];
                } elseif (('/' == $v[0]) && ('Info' == $v[1]) && (isset($sarr[$k + 1]) && ('objref' == $sarr[$k + 1][0]))) {
                    $xref['trailer']['info'] = $sarr[$k + 1][1];
                } elseif (('/' == $v[0]) && ('Encrypt' == $v[1]) && (isset($sarr[$k + 1]) && ('objref' == $sarr[$k + 1][0]))) {
                    $xref['trailer']['encrypt'] = $sarr[$k + 1][1];
                } elseif (('/' == $v[0]) && ('ID' == $v[1]) && (isset($sarr[$k + 1]))) {
                    $xref['trailer']['id'] = [];
                    $xref['trailer']['id'][0] = $sarr[$k + 1][1][0][1];
                    $xref['trailer']['id'][1] = $sarr[$k + 1][1][1][1];
                }
            }
        }

        // decode data
        if ($valid_crs && isset($xrefcrs[1][3][0])) {
            if (null !== $predictor) {
                // number of bytes in a row
                $rowlen = ($columns + 1);
                // convert the stream into an array of integers
                /** @var array<int> */
                $sdata = unpack('C*', $xrefcrs[1][3][0]);
                // TODO: Handle the case when unpack returns false

                // split the rows
                $sdata = array_chunk($sdata, $rowlen);

                // initialize decoded array
                $ddata = [];
                // initialize first row with zeros
                $prev_row = array_fill(0, $rowlen, 0);
                // for each row apply PNG unpredictor
                foreach ($sdata as $k => $row) {
                    // initialize new row
                    $ddata[$k] = [];
                    // get PNG predictor value
                    $predictor = (10 + $row[0]);
                    // for each byte on the row
                    for ($i = 1; $i <= $columns; ++$i) {
                        // new index
                        $j = ($i - 1);
                        $row_up = $prev_row[$j];
                        if (1 == $i) {
                            $row_left = 0;
                            $row_upleft = 0;
                        } else {
                            $row_left = $row[$i - 1];
                            $row_upleft = $prev_row[$j - 1];
                        }
                        switch ($predictor) {
                            case 10:  // PNG prediction (on encoding, PNG None on all rows)
                                $ddata[$k][$j] = $row[$i];
                                break;

                            case 11:  // PNG prediction (on encoding, PNG Sub on all rows)
                                $ddata[$k][$j] = (($row[$i] + $row_left) & 0xFF);
                                break;

                            case 12:  // PNG prediction (on encoding, PNG Up on all rows)
                                $ddata[$k][$j] = (($row[$i] + $row_up) & 0xFF);
                                break;

                            case 13:  // PNG prediction (on encoding, PNG Average on all rows)
                                $ddata[$k][$j] = (($row[$i] + (($row_left + $row_up) / 2)) & 0xFF);
                                break;

                            case 14:  // PNG prediction (on encoding, PNG Paeth on all rows)
                                // initial estimate
                                $p = ($row_left + $row_up - $row_upleft);
                                // distances
                                $pa = abs($p - $row_left);
                                $pb = abs($p - $row_up);
                                $pc = abs($p - $row_upleft);
                                $pmin = min($pa, $pb, $pc);
                                // return minimum distance
                                switch ($pmin) {
                                    case $pa:
                                        $ddata[$k][$j] = (($row[$i] + $row_left) & 0xFF);
                                        break;

                                    case $pb:
                                        $ddata[$k][$j] = (($row[$i] + $row_up) & 0xFF);
                                        break;

                                    case $pc:
                                        $ddata[$k][$j] = (($row[$i] + $row_upleft) & 0xFF);
                                        break;
                                }
                                break;

                            default:  // PNG prediction (on encoding, PNG optimum)
                                throw new \Exception('Unknown PNG predictor: '.$predictor);
                        }
                    }
                    $prev_row = $ddata[$k];
                } // end for each row
                // complete decoding
            } else {
                // number of bytes in a row
                $rowlen = array_sum($wb);
                if (0 < $rowlen) {
                    // convert the stream into an array of integers
                    $sdata = unpack('C*', $xrefcrs[1][3][0]);
                    // split the rows
                    $ddata = array_chunk($sdata, $rowlen);
                } else {
                    // if the row length is zero, $ddata should be an empty array as well
                    $ddata = [];
                }
            }

            $sdata = [];

            // for every row
            foreach ($ddata as $k => $row) {
                // initialize new row
                $sdata[$k] = [0, 0, 0];
                if (0 == $wb[0]) {
                    // default type field
                    $sdata[$k][0] = 1;
                }
                $i = 0; // count bytes in the row
                // for every column
                for ($c = 0; $c < 3; ++$c) {
                    // for every byte on the column
                    for ($b = 0; $b < $wb[$c]; ++$b) {
                        if (isset($row[$i])) {
                            $sdata[$k][$c] += ($row[$i] << (($wb[$c] - 1 - $b) * 8));
                        }
                        ++$i;
                    }
                }
            }

            // fill xref
            if (isset($index_blocks)) {
                // load the first object number of the first /Index entry
                $obj_num = $index_blocks[0][0];
            } else {
                $obj_num = 0;
            }
            foreach ($sdata as $k => $row) {
                switch ($row[0]) {
                    case 0:  // (f) linked list of free objects
                        break;

                    case 1:  // (n) objects that are in use but are not compressed
                        // create unique object index: [object number]_[generation number]
                        $index = $obj_num.'_'.$row[2];
                        // check if object already exist
                        if (!isset($xref['xref'][$index])) {
                            // store object offset position
                            $xref['xref'][$index] = $row[1];
                        }
                        break;

                    case 2:  // compressed objects
                        // $row[1] = object number of the object stream in which this object is stored
                        // $row[2] = index of this object within the object stream
                        $index = $row[1].'_0_'.$row[2];
                        $xref['xref'][$index] = -1;
                        break;

                    default:  // null objects
                        break;
                }
                ++$obj_num;
                if (isset($index_blocks)) {
                    // reduce the number of remaining objects
                    --$index_blocks[0][1];
                    if (0 == $index_blocks[0][1]) {
                        // remove the actual used /Index entry
                        array_shift($index_blocks);
                        if (0 < \count($index_blocks)) {
                            // load the first object number of the following /Index entry
                            $obj_num = $index_blocks[0][0];
                        } else {
                            // if there are no more entries, remove $index_blocks to avoid actions on an empty array
                            unset($index_blocks);
                        }
                    }
                }
            }
        } // end decoding data
        if (isset($prevxref)) {
            // get previous xref
            $xref = $this->getXrefData($pdfData, $prevxref, $xref, $visitedOffsets);
        }

        return $xref;
    }

    protected function getObjectHeaderPattern(array $objRefs): string
    {
        // consider all whitespace character (PDF specifications)
        return '/'.$objRefs[0].$this->config->getPdfWhitespacesRegex().$objRefs[1].$this->config->getPdfWhitespacesRegex().'obj/';
    }

    protected function getObjectHeaderLen(array $objRefs): int
    {
        // "4 0 obj"
        // 2 whitespaces + strlen("obj") = 5
        return 5 + \strlen($objRefs[0]) + \strlen($objRefs[1]);
    }

    /**
     * Get content of indirect object.
     *
     * @param string $pdfData  PDF data
     * @param string $objRef   Object number and generation number separated by underscore character
     * @param int    $offset   Object offset
     * @param bool   $decoding If true decode streams
     *
     * @return array containing object data
     *
     * @throws \Exception if invalid object reference found
     */
    protected function getIndirectObject(string $pdfData, array $xref, string $objRef, int $offset = 0, bool $decoding = true): array
    {
        /*
         * build indirect object header
         */
        // $objHeader = "[object number] [generation number] obj"
        $objRefArr = explode('_', $objRef);
        if (2 !== \count($objRefArr)) {
            throw new \Exception('Invalid object reference for $obj.');
        }

        $objHeaderLen = $this->getObjectHeaderLen($objRefArr);

        /*
         * check if we are in position
         */
        // ignore whitespace characters at offset
        $offset += strspn($pdfData, $this->config->getPdfWhitespaces(), $offset);
        // ignore leading zeros for object number
        $offset += strspn($pdfData, '0', $offset);
        if (0 == preg_match($this->getObjectHeaderPattern($objRefArr), substr($pdfData, $offset, $objHeaderLen))) {
            // an indirect reference to an undefined object shall be considered a reference to the null object
            return ['null', 'null', $offset];
        }

        /*
         * get content
         */
        // starting position of object content
        $offset += $objHeaderLen;
        $objContentArr = [];
        $i = 0; // object main index
        $header = null;
        do {
            $oldOffset = $offset;
            // get element
            $element = $this->getRawObject($pdfData, $offset, null != $header ? $header[1] : null);
            $offset = $element[2];
            // decode stream using stream's dictionary information
            if ($decoding && ('stream' === $element[0]) && null != $header) {
                $element[3] = $this->decodeStream($pdfData, $xref, $header[1], $element[1]);
            }
            $objContentArr[$i] = $element;
            $header = isset($element[0]) && '<<' === $element[0] ? $element : null;
            ++$i;
        } while (('endobj' !== $element[0]) && ($offset !== $oldOffset));
        // remove closing delimiter
        array_pop($objContentArr);

        /*
         * return raw object content
         */
        return $objContentArr;
    }

    /**
     * Get the content of object, resolving indirect object reference if necessary.
     *
     * @param string $pdfData PDF data
     * @param array  $obj     Object value
     *
     * @return array containing object data
     *
     * @throws \Exception
     */
    protected function getObjectVal(string $pdfData, $xref, array $obj): array
    {
        if ('objref' == $obj[0]) {
            // reference to indirect object
            if (isset($this->objects[$obj[1]])) {
                // this object has been already parsed
                return $this->objects[$obj[1]];
            } elseif (isset($xref[$obj[1]])) {
                // parse new object
                $this->objects[$obj[1]] = $this->getIndirectObject($pdfData, $xref, $obj[1], $xref[$obj[1]], false);

                return $this->objects[$obj[1]];
            }
        }

        return $obj;
    }

    /**
     * Get object type, raw value and offset to next object
     *
     * @param int        $offset    Object offset
     * @param array|null $headerDic obj header's dictionary, parsed by getRawObject. Used for stream parsing optimization
     *
     * @return array containing object type, raw value and offset to next object
     */
    protected function getRawObject(string $pdfData, int $offset = 0, ?array $headerDic = null): array
    {
        $objtype = ''; // object type to be returned
        $objval = ''; // object value to be returned

        // skip initial white space chars
        $offset += strspn($pdfData, $this->config->getPdfWhitespaces(), $offset);

        // get first char
        $char = $pdfData[$offset];
        // get object type
        switch ($char) {
            case '%':  // \x25 PERCENT SIGN
                // skip comment and search for next token
                $next = strcspn($pdfData, "\r\n", $offset);
                if ($next > 0) {
                    $offset += $next;

                    return $this->getRawObject($pdfData, $offset);
                }
                break;

            case '/':  // \x2F SOLIDUS
                // name object
                $objtype = $char;
                ++$offset;
                $span = strcspn($pdfData, "\x00\x09\x0a\x0c\x0d\x20\n\t\r\v\f\x28\x29\x3c\x3e\x5b\x5d\x7b\x7d\x2f\x25", $offset, 256);
                if ($span > 0) {
                    $objval = substr($pdfData, $offset, $span); // unescaped value
                    $offset += $span;
                }
                break;

            case '(':   // \x28 LEFT PARENTHESIS
            case ')':  // \x29 RIGHT PARENTHESIS
                // literal string object
                $objtype = $char;
                ++$offset;
                $strpos = $offset;
                if ('(' == $char) {
                    $open_bracket = 1;
                    while ($open_bracket > 0) {
                        if (!isset($pdfData[$strpos])) {
                            break;
                        }
                        $ch = $pdfData[$strpos];
                        switch ($ch) {
                            case '\\':  // REVERSE SOLIDUS (5Ch) (Backslash)
                                // skip next character
                                ++$strpos;
                                break;

                            case '(':  // LEFT PARENHESIS (28h)
                                ++$open_bracket;
                                break;

                            case ')':  // RIGHT PARENTHESIS (29h)
                                --$open_bracket;
                                break;
                        }
                        ++$strpos;
                    }
                    $objval = substr($pdfData, $offset, $strpos - $offset - 1);
                    $offset = $strpos;
                }
                break;

            case '[':   // \x5B LEFT SQUARE BRACKET
            case ']':  // \x5D RIGHT SQUARE BRACKET
                // array object
                $objtype = $char;
                ++$offset;
                if ('[' == $char) {
                    // get array content
                    $objval = [];
                    do {
                        $oldOffset = $offset;
                        // get element
                        $element = $this->getRawObject($pdfData, $offset);
                        $offset = $element[2];
                        $objval[] = $element;
                    } while ((']' != $element[0]) && ($offset != $oldOffset));
                    // remove closing delimiter
                    array_pop($objval);
                }
                break;

            case '<':  // \x3C LESS-THAN SIGN
            case '>':  // \x3E GREATER-THAN SIGN
                if (isset($pdfData[$offset + 1]) && ($pdfData[$offset + 1] == $char)) {
                    // dictionary object
                    $objtype = $char.$char;
                    $offset += 2;
                    if ('<' == $char) {
                        // get array content
                        $objval = [];
                        do {
                            $oldOffset = $offset;
                            // get element
                            $element = $this->getRawObject($pdfData, $offset);
                            $offset = $element[2];
                            $objval[] = $element;
                        } while (('>>' != $element[0]) && ($offset != $oldOffset));
                        // remove closing delimiter
                        array_pop($objval);
                    }
                } else {
                    // hexadecimal string object
                    $objtype = $char;
                    ++$offset;

                    $span = strspn($pdfData, "0123456789abcdefABCDEF\x09\x0a\x0c\x0d\x20", $offset);
                    $dataToCheck = $pdfData[$offset + $span] ?? null;
                    if ('<' == $char && $span > 0 && '>' == $dataToCheck) {
                        // remove white space characters
                        $objval = strtr(substr($pdfData, $offset, $span), $this->config->getPdfWhitespaces(), '');
                        $offset += $span + 1;
                    } elseif (false !== ($endpos = strpos($pdfData, '>', $offset))) {
                        $offset = $endpos + 1;
                    }
                }
                break;

            default:
                if ('endobj' == substr($pdfData, $offset, 6)) {
                    // indirect object
                    $objtype = 'endobj';
                    $offset += 6;
                } elseif ('null' == substr($pdfData, $offset, 4)) {
                    // null object
                    $objtype = 'null';
                    $offset += 4;
                    $objval = 'null';
                } elseif ('true' == substr($pdfData, $offset, 4)) {
                    // boolean true object
                    $objtype = 'boolean';
                    $offset += 4;
                    $objval = 'true';
                } elseif ('false' == substr($pdfData, $offset, 5)) {
                    // boolean false object
                    $objtype = 'boolean';
                    $offset += 5;
                    $objval = 'false';
                } elseif ('stream' == substr($pdfData, $offset, 6)) {
                    // start stream object
                    $objtype = 'stream';
                    $offset += 6;
                    if (1 == preg_match('/^( *[\r]?[\n])/isU', substr($pdfData, $offset, 4), $matches)) {
                        $offset += \strlen($matches[0]);

                        // we get stream length here to later help preg_match test less data
                        $streamLen = (int) $this->getHeaderValue($headerDic, 'Length', 'numeric', 0);
                        $skip = false === $this->config->getRetainImageContent() && 'XObject' == $this->getHeaderValue($headerDic, 'Type', '/') && 'Image' == $this->getHeaderValue($headerDic, 'Subtype', '/');

                        $pregResult = preg_match(
                            '/(endstream)[\x09\x0a\x0c\x0d\x20]/isU',
                            $pdfData,
                            $matches,
                            \PREG_OFFSET_CAPTURE,
                            $offset + $streamLen
                        );

                        if (1 == $pregResult) {
                            $objval = $skip ? '' : substr($pdfData, $offset, $matches[0][1] - $offset);
                            $offset = $matches[1][1];
                        }
                    }
                } elseif ('endstream' == substr($pdfData, $offset, 9)) {
                    // end stream object
                    $objtype = 'endstream';
                    $offset += 9;
                } elseif (1 == preg_match('/^([0-9]+)[\s]+([0-9]+)[\s]+R/iU', substr($pdfData, $offset, 33), $matches)) {
                    // indirect object reference
                    $objtype = 'objref';
                    $offset += \strlen($matches[0]);
                    $objval = (int) $matches[1].'_'.(int) $matches[2];
                } elseif (1 == preg_match('/^([0-9]+)[\s]+([0-9]+)[\s]+obj/iU', substr($pdfData, $offset, 33), $matches)) {
                    // object start
                    $objtype = 'obj';
                    $objval = (int) $matches[1].'_'.(int) $matches[2];
                    $offset += \strlen($matches[0]);
                } elseif (($numlen = strspn($pdfData, '+-.0123456789', $offset)) > 0) {
                    // numeric object
                    $objtype = 'numeric';
                    $objval = substr($pdfData, $offset, $numlen);
                    $offset += $numlen;
                }
                break;
        }

        return [$objtype, $objval, $offset];
    }

    /**
     * Get value of an object header's section (obj << YYY >> part ).
     *
     * It is similar to Header::get('...')->getContent(), the only difference is it can be used during the parsing process,
     * when no Smalot\PdfParser\Header objects are created yet.
     *
     * @param string            $key     header's section name
     * @param string            $type    type of the section (i.e. 'numeric', '/', '<<', etc.)
     * @param string|array|null $default default value for header's section
     *
     * @return string|array|null value of obj header's section, or default value if none found, or its type doesn't match $type param
     */
    private function getHeaderValue(?array $headerDic, string $key, string $type, $default = '')
    {
        if (false === \is_array($headerDic)) {
            return $default;
        }

        /*
         * It recieves dictionary of header fields, as it is returned by RawDataParser::getRawObject,
         * iterates over it, searching for section of type '/' whith requested key.
         * If such a section is found, it tries to receive it's value (next object in dictionary),
         * returning it, if it matches requested type, or default value otherwise.
         */
        foreach ($headerDic as $i => $val) {
            $isSectionName = \is_array($val) && 3 == \count($val) && '/' == $val[0];
            if (
                $isSectionName
                && $val[1] == $key
                && isset($headerDic[$i + 1])
            ) {
                $isSectionValue = \is_array($headerDic[$i + 1]) && 1 < \count($headerDic[$i + 1]);

                return $isSectionValue && $type == $headerDic[$i + 1][0]
                    ? $headerDic[$i + 1][1]
                    : $default;
            }
        }

        return $default;
    }

    /**
     * Get Cross-Reference (xref) table and trailer data from PDF document data.
     *
     * @param int        $offset        xref offset (if known)
     * @param array      $xref          previous xref array (if any)
     * @param array<int> $visitedOffsets array of visited offsets to prevent infinite loops
     *
     * @return array containing xref and trailer data
     *
     * @throws \Exception if it was unable to find startxref
     * @throws \Exception if it was unable to find xref
     */
    protected function getXrefData(string $pdfData, int $offset = 0, array $xref = [], array $visitedOffsets = []): array
    {
        // Check for circular references to prevent infinite loops
        if (\in_array($offset, $visitedOffsets, true)) {
            // We've already processed this offset, skip to avoid infinite loop
            return $xref;
        }

        // Track this offset as visited
        $visitedOffsets[] = $offset;
        // If the $offset is currently pointed at whitespace, bump it
        // forward until it isn't; affects loosely targetted offsets
        // for the 'xref' keyword
        // See: https://github.com/smalot/pdfparser/issues/673
        $bumpOffset = $offset;
        while (preg_match('/\s/', substr($pdfData, $bumpOffset, 1))) {
            ++$bumpOffset;
        }

        // Find all startxref tables from this $offset forward
        $startxrefPreg = preg_match_all(
            '/(?<=[\r\n])startxref[\s]*[\r\n]+([0-9]+)[\s]*[\r\n]+%%EOF/i',
            $pdfData,
            $startxrefMatches,
            \PREG_SET_ORDER,
            $offset
        );

        if (0 == $startxrefPreg) {
            // No startxref tables were found
            throw new \Exception('Unable to find startxref');
        } elseif (0 == $offset) {
            // Use the last startxref in the document
            $startxref = (int) $startxrefMatches[\count($startxrefMatches) - 1][1];
        } elseif (strpos($pdfData, 'xref', $bumpOffset) == $bumpOffset) {
            // Already pointing at the xref table
            $startxref = $bumpOffset;
        } elseif (preg_match('/([0-9]+[\s][0-9]+[\s]obj)/i', $pdfData, $matches, 0, $bumpOffset)) {
            // Cross-Reference Stream object
            $startxref = $bumpOffset;
        } else {
            // Use the next startxref from this $offset
            $startxref = (int) $startxrefMatches[0][1];
        }

        if ($startxref > \strlen($pdfData)) {
            throw new \Exception('Unable to find xref (PDF corrupted?)');
        }

        // check xref position
        if (strpos($pdfData, 'xref', $startxref) == $startxref) {
            // Cross-Reference
            $xref = $this->decodeXref($pdfData, $startxref, $xref, $visitedOffsets);
        } else {
            // Check if the $pdfData might have the wrong line-endings
            $pdfDataUnix = str_replace("\r\n", "\n", $pdfData);
            if ($startxref < \strlen($pdfDataUnix) && strpos($pdfDataUnix, 'xref', $startxref) == $startxref) {
                // Return Unix-line-ending flag
                $xref = ['Unix' => true];
            } else {
                // Cross-Reference Stream
                $xref = $this->decodeXrefStream($pdfData, $startxref, $xref, $visitedOffsets);
            }
        }
        if (empty($xref)) {
            throw new \Exception('Unable to find xref');
        }

        return $xref;
    }

    /**
     * Parses PDF data and returns extracted data as array.
     *
     * @param string $data PDF data to parse
     *
     * @return array array of parsed PDF document objects
     *
     * @throws EmptyPdfException if empty PDF data given
     * @throws MissingPdfHeaderException if PDF data missing `%PDF-` header
     */
    public function parseData(string $data): array
    {
        if (empty($data)) {
            throw new EmptyPdfException('Empty PDF data given.');
        }
        // find the pdf header starting position
        if (false === ($trimpos = strpos($data, '%PDF-'))) {
            throw new MissingPdfHeaderException('Invalid PDF data: Missing `%PDF-` header.');
        }

        // get PDF content string
        $pdfData = $trimpos > 0 ? substr($data, $trimpos) : $data;

        // get xref and trailer data
        $xref = $this->getXrefData($pdfData);

        // If we found Unix line-endings
        if (isset($xref['Unix'])) {
            $pdfData = str_replace("\r\n", "\n", $pdfData);
            $xref = $this->getXrefData($pdfData);
        }

        // parse all document objects
        $objects = [];
        foreach ($xref['xref'] as $obj => $offset) {
            if (!isset($objects[$obj]) && ($offset > 0)) {
                // decode objects with positive offset
                $objects[$obj] = $this->getIndirectObject($pdfData, $xref, $obj, $offset, true);
            }
        }

        return [$xref, $objects];
    }
}
