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

/**
 * Class ElementHexa
 */
class ElementHexa extends ElementString
{
    /**
     * @return bool|ElementHexa|ElementDate
     */
    public static function parse(string $content, ?Document $document = null, int &$offset = 0)
    {
        if (preg_match('/^\s*\<(?P<name>[A-F0-9]+)\>/is', $content, $match)) {
            $name = $match['name'];
            $offset += strpos($content, '<'.$name) + \strlen($name) + 2; // 1 for '>'
            // repackage string as standard
            $name = '('.self::decode($name).')';
            $element = ElementDate::parse($name, $document);

            if (!$element) {
                $element = ElementString::parse($name, $document);
            }

            return $element;
        }

        return false;
    }

    public static function decode(string $value): string
    {
        $text = '';

        // Filter $value of non-hexadecimal characters
        $value = (string) preg_replace('/[^0-9a-f]/i', '', $value);

        // Check for leading zeros (4-byte hexadecimal indicator), or
        // the BE BOM
        if ('00' === substr($value, 0, 2) || 'feff' === strtolower(substr($value, 0, 4))) {
            $value = (string) preg_replace('/^feff/i', '', $value);
            for ($i = 0, $length = \strlen($value); $i < $length; $i += 4) {
                $hex = substr($value, $i, 4);
                $text .= '&#'.str_pad(hexdec($hex), 4, '0', \STR_PAD_LEFT).';';
            }
        } else {
            // Otherwise decode this as 2-byte hexadecimal
            for ($i = 0, $length = \strlen($value); $i < $length; $i += 2) {
                $hex = substr($value, $i, 2);
                $text .= \chr(hexdec($hex));
            }
        }

        $text = html_entity_decode($text, \ENT_NOQUOTES, 'UTF-8');

        return $text;
    }
}
