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

use Smalot\PdfParser\Exception\NotImplementedException;

class FilterHelper
{
    protected $availableFilters = ['ASCIIHexDecode', 'ASCII85Decode', 'LZWDecode', 'FlateDecode', 'RunLengthDecode'];

    /**
     * Decode data using the specified filter type.
     *
     * @param string $filter Filter name
     * @param string $data   Data to decode
     *
     * @return string Decoded data string
     *
     * @throws \Exception
     * @throws \Smalot\PdfParser\Exception\NotImplementedException if a certain decode function is not implemented yet
     */
    public function decodeFilter(string $filter, string $data, int $decodeMemoryLimit = 0): string
    {
        switch ($filter) {
            case 'ASCIIHexDecode':
                return $this->decodeFilterASCIIHexDecode($data);

            case 'ASCII85Decode':
                return $this->decodeFilterASCII85Decode($data);

            case 'LZWDecode':
                return $this->decodeFilterLZWDecode($data);

            case 'FlateDecode':
                return $this->decodeFilterFlateDecode($data, $decodeMemoryLimit);

            case 'RunLengthDecode':
                return $this->decodeFilterRunLengthDecode($data);

            case 'CCITTFaxDecode':
                throw new NotImplementedException('Decode CCITTFaxDecode not implemented yet.');
            case 'JBIG2Decode':
                throw new NotImplementedException('Decode JBIG2Decode not implemented yet.');
            case 'DCTDecode':
                throw new NotImplementedException('Decode DCTDecode not implemented yet.');
            case 'JPXDecode':
                throw new NotImplementedException('Decode JPXDecode not implemented yet.');
            case 'Crypt':
                throw new NotImplementedException('Decode Crypt not implemented yet.');
            default:
                return $data;
        }
    }

    /**
     * ASCIIHexDecode
     *
     * Decodes data encoded in an ASCII hexadecimal representation, reproducing the original binary data.
     *
     * @param string $data Data to decode
     *
     * @return string data string
     *
     * @throws \Exception
     */
    protected function decodeFilterASCIIHexDecode(string $data): string
    {
        // all white-space characters shall be ignored
        $data = preg_replace('/[\s]/', '', $data);
        // check for EOD character: GREATER-THAN SIGN (3Eh)
        $eod = strpos($data, '>');
        if (false !== $eod) {
            // remove EOD and extra data (if any)
            $data = substr($data, 0, $eod);
            $eod = true;
        }
        // get data length
        $data_length = \strlen($data);
        if (0 != ($data_length % 2)) {
            // odd number of hexadecimal digits
            if ($eod) {
                // EOD shall behave as if a 0 (zero) followed the last digit
                $data = substr($data, 0, -1).'0'.substr($data, -1);
            } else {
                throw new \Exception('decodeFilterASCIIHexDecode: invalid code');
            }
        }
        // check for invalid characters
        if (preg_match('/[^a-fA-F\d]/', $data) > 0) {
            throw new \Exception('decodeFilterASCIIHexDecode: invalid code');
        }
        // get one byte of binary data for each pair of ASCII hexadecimal digits
        $decoded = pack('H*', $data);

        return $decoded;
    }

    /**
     * ASCII85Decode
     *
     * Decodes data encoded in an ASCII base-85 representation, reproducing the original binary data.
     *
     * @param string $data Data to decode
     *
     * @return string data string
     *
     * @throws \Exception
     */
    protected function decodeFilterASCII85Decode(string $data): string
    {
        // initialize string to return
        $decoded = '';
        // all white-space characters shall be ignored
        $data = preg_replace('/[\s]/', '', $data);
        // remove start sequence 2-character sequence <~ (3Ch)(7Eh)
        if (0 === strpos($data, '<~')) {
            // remove EOD and extra data (if any)
            $data = substr($data, 2);
        }
        // check for EOD: 2-character sequence ~> (7Eh)(3Eh)
        $eod = strpos($data, '~>');
        if (\strlen($data) - 2 === $eod) {
            // remove EOD and extra data (if any)
            $data = substr($data, 0, $eod);
        }
        // data length
        $data_length = \strlen($data);
        // check for invalid characters
        if (preg_match('/[^\x21-\x75,\x74]/', $data) > 0) {
            throw new \Exception('decodeFilterASCII85Decode: invalid code');
        }
        // z sequence
        $zseq = \chr(0).\chr(0).\chr(0).\chr(0);
        // position inside a group of 4 bytes (0-3)
        $group_pos = 0;
        $tuple = 0;
        $pow85 = [85 * 85 * 85 * 85, 85 * 85 * 85, 85 * 85, 85, 1];

        // for each byte
        for ($i = 0; $i < $data_length; ++$i) {
            // get char value
            $char = \ord($data[$i]);
            if (122 == $char) { // 'z'
                if (0 == $group_pos) {
                    $decoded .= $zseq;
                } else {
                    throw new \Exception('decodeFilterASCII85Decode: invalid code');
                }
            } else {
                // the value represented by a group of 5 characters should never be greater than 2^32 - 1
                $tuple += (($char - 33) * $pow85[$group_pos]);
                if (4 == $group_pos) {
                    // The following if-clauses are an attempt to fix/suppress the following deprecation warning:
                    //      chr(): Providing a value not in-between 0 and 255 is deprecated, this is because a byte value
                    //      must be in the [0, 255] interval. The value used will be constrained using % 256
                    // I know this is ugly and there might be more fancier ways. If you know one, feel free to provide a pull request.
                    if (255 < $tuple >> 8) {
                        $chr8Part = \chr(($tuple >> 8) % 256);
                    } else {
                        $chr8Part = \chr($tuple >> 8);
                    }

                    if (255 < $tuple >> 16) {
                        $chr16Part = \chr(($tuple >> 16) % 256);
                    } else {
                        $chr16Part = \chr($tuple >> 16);
                    }

                    if (255 < $tuple >> 24) {
                        $chr24Part = \chr(($tuple >> 24) % 256);
                    } else {
                        $chr24Part = \chr($tuple >> 24);
                    }

                    if (255 < $tuple) {
                        $chrTuple = \chr($tuple % 256);
                    } else {
                        $chrTuple = \chr($tuple);
                    }

                    $decoded .= $chr24Part . $chr16Part . $chr8Part . $chrTuple;
                    $tuple = 0;
                    $group_pos = 0;
                } else {
                    ++$group_pos;
                }
            }
        }
        if ($group_pos > 1) {
            $tuple += $pow85[$group_pos - 1];
        }
        // last tuple (if any)
        switch ($group_pos) {
            case 4:
                $decoded .= \chr($tuple >> 24).\chr($tuple >> 16).\chr($tuple >> 8);
                break;

            case 3:
                $decoded .= \chr($tuple >> 24).\chr($tuple >> 16);
                break;

            case 2:
                $decoded .= \chr($tuple >> 24);
                break;

            case 1:
                throw new \Exception('decodeFilterASCII85Decode: invalid code');
        }

        return $decoded;
    }

    /**
     * FlateDecode
     *
     * Decompresses data encoded using the zlib/deflate compression method, reproducing the original text or binary data.
     *
     * @param string $data              Data to decode
     * @param int    $decodeMemoryLimit Memory limit on deflation
     *
     * @return string data string
     *
     * @throws \Exception
     */
    protected function decodeFilterFlateDecode(string $data, int $decodeMemoryLimit): ?string
    {
        // Uncatchable E_WARNING for "data error" is @ suppressed
        // so execution may proceed with an alternate decompression
        // method.
        $decoded = @gzuncompress($data, $decodeMemoryLimit);

        if (false === $decoded) {
            // If gzuncompress() failed, try again using the compress.zlib://
            // wrapper to decode it in a file-based context.
            // See: https://www.php.net/manual/en/function.gzuncompress.php#79042
            // Issue: https://github.com/smalot/pdfparser/issues/592
            $ztmp = tmpfile();
            if (false != $ztmp) {
                fwrite($ztmp, "\x1f\x8b\x08\x00\x00\x00\x00\x00".$data);
                $file = stream_get_meta_data($ztmp)['uri'];
                if (0 === $decodeMemoryLimit) {
                    $decoded = file_get_contents('compress.zlib://'.$file);
                } else {
                    $decoded = file_get_contents('compress.zlib://'.$file, false, null, 0, $decodeMemoryLimit);
                }
                fclose($ztmp);
            }
        }

        if (false === \is_string($decoded) || '' === $decoded) {
            // If the decoded string is empty, that means decoding failed.
            throw new \Exception('decodeFilterFlateDecode: invalid data');
        }

        return $decoded;
    }

    /**
     * LZWDecode
     *
     * Decompresses data encoded using the LZW (Lempel-Ziv-Welch) adaptive compression method, reproducing the original text or binary data.
     *
     * @param string $data Data to decode
     *
     * @return string Data string
     */
    protected function decodeFilterLZWDecode(string $data): string
    {
        // initialize string to return
        $decoded = '';
        // data length
        $data_length = \strlen($data);
        // convert string to binary string
        $bitstring = '';
        for ($i = 0; $i < $data_length; ++$i) {
            $bitstring .= \sprintf('%08b', \ord($data[$i]));
        }
        // get the number of bits
        $data_length = \strlen($bitstring);
        // initialize code length in bits
        $bitlen = 9;
        // initialize dictionary index
        $dix = 258;
        // initialize the dictionary (with the first 256 entries).
        $dictionary = [];
        for ($i = 0; $i < 256; ++$i) {
            $dictionary[$i] = \chr($i);
        }
        // previous val
        $prev_index = 0;
        // while we encounter EOD marker (257), read code_length bits
        while (($data_length > 0) && (257 != ($index = bindec(substr($bitstring, 0, $bitlen))))) {
            // remove read bits from string
            $bitstring = substr($bitstring, $bitlen);
            // update number of bits
            $data_length -= $bitlen;
            if (256 == $index) { // clear-table marker
                // reset code length in bits
                $bitlen = 9;
                // reset dictionary index
                $dix = 258;
                $prev_index = 256;
                // reset the dictionary (with the first 256 entries).
                $dictionary = [];
                for ($i = 0; $i < 256; ++$i) {
                    $dictionary[$i] = \chr($i);
                }
            } elseif (256 == $prev_index) {
                // first entry
                $decoded .= $dictionary[$index];
                $prev_index = $index;
            } else {
                // check if index exist in the dictionary
                if ($index < $dix) {
                    // index exist on dictionary
                    $decoded .= $dictionary[$index];
                    $dic_val = $dictionary[$prev_index].$dictionary[$index][0];
                    // store current index
                    $prev_index = $index;
                } else {
                    // index do not exist on dictionary
                    $dic_val = $dictionary[$prev_index].$dictionary[$prev_index][0];
                    $decoded .= $dic_val;
                }
                // update dictionary
                $dictionary[$dix] = $dic_val;
                ++$dix;
                // change bit length by case
                if (2047 == $dix) {
                    $bitlen = 12;
                } elseif (1023 == $dix) {
                    $bitlen = 11;
                } elseif (511 == $dix) {
                    $bitlen = 10;
                }
            }
        }

        return $decoded;
    }

    /**
     * RunLengthDecode
     *
     * Decompresses data encoded using a byte-oriented run-length encoding algorithm.
     *
     * @param string $data Data to decode
     */
    protected function decodeFilterRunLengthDecode(string $data): string
    {
        // initialize string to return
        $decoded = '';
        // data length
        $data_length = \strlen($data);
        $i = 0;
        while ($i < $data_length) {
            // get current byte value
            $byte = \ord($data[$i]);
            if (128 == $byte) {
                // a length value of 128 denote EOD
                break;
            } elseif ($byte < 128) {
                // if the length byte is in the range 0 to 127
                // the following length + 1 (1 to 128) bytes shall be copied literally during decompression
                $decoded .= substr($data, $i + 1, $byte + 1);
                // move to next block
                $i += ($byte + 2);
            } else {
                // if length is in the range 129 to 255,
                // the following single byte shall be copied 257 - length (2 to 128) times during decompression
                $decoded .= str_repeat($data[$i + 1], 257 - $byte);
                // move to next block
                $i += 2;
            }
        }

        return $decoded;
    }

    /**
     * @return array list of available filters
     */
    public function getAvailableFilters(): array
    {
        return $this->availableFilters;
    }
}
