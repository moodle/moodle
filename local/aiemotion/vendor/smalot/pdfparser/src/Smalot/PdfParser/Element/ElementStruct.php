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
use Smalot\PdfParser\Header;

/**
 * Class ElementStruct
 */
class ElementStruct extends Element
{
    /**
     * @return false|Header
     */
    public static function parse(string $content, ?Document $document = null, int &$offset = 0)
    {
        if (preg_match('/^\s*<<(?P<struct>.*)/is', $content)) {
            preg_match_all('/(.*?)(<<|>>)/s', trim($content), $matches);

            $level = 0;
            $sub = '';
            foreach ($matches[0] as $part) {
                $sub .= $part;
                $level += (false !== strpos($part, '<<') ? 1 : -1);
                if ($level <= 0) {
                    break;
                }
            }

            $offset += strpos($content, '<<') + \strlen(rtrim($sub));

            // Removes '<<' and '>>'.
            $sub = trim((string) preg_replace('/^\s*<<(.*)>>\s*$/s', '\\1', $sub));

            $position = 0;
            $elements = Element::parse($sub, $document, $position);

            return new Header($elements, $document);
        }

        return false;
    }
}
