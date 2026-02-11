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

namespace Smalot\PdfParser\Element;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\Element;
use Smalot\PdfParser\Font;

/**
 * Class ElementString
 */
class ElementString extends Element
{
    public function __construct($value)
    {
        parent::__construct($value, null);
    }

    public function equals($value): bool
    {
        return $value == $this->value;
    }

    /**
     * @return bool|ElementString
     */
    public static function parse(string $content, ?Document $document = null, int &$offset = 0)
    {
        if (preg_match('/^\s*\((?P<name>.*)/s', $content, $match)) {
            $name = $match['name'];

            // Find next ')' not escaped.
            $cur_start_text = $start_search_end = 0;
            while (false !== ($cur_start_pos = strpos($name, ')', $start_search_end))) {
                $cur_extract = substr($name, $cur_start_text, $cur_start_pos - $cur_start_text);
                preg_match('/(?P<escape>[\\\]*)$/s', $cur_extract, $match);
                if (!(\strlen($match['escape']) % 2)) {
                    break;
                }
                $start_search_end = $cur_start_pos + 1;
            }

            // Extract string.
            $name = substr($name, 0, (int) $cur_start_pos);
            $offset += strpos($content, '(') + $cur_start_pos + 2; // 2 for '(' and ')'
            $name = str_replace(
                ['\\\\', '\\ ', '\\/', '\(', '\)', '\n', '\r', '\t'],
                ['\\',   ' ',   '/',   '(',  ')',  "\n", "\r", "\t"],
                $name
            );

            // Decode string.
            $name = Font::decodeOctal($name);
            $name = Font::decodeEntities($name);
            $name = Font::decodeHexadecimal($name, false);
            $name = Font::decodeUnicode($name);

            return new self($name);
        }

        return false;
    }
}
