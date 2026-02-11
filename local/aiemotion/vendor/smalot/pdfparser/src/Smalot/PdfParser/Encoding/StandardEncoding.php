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

// Source : http://cpansearch.perl.org/src/JV/PostScript-Font-1.10.02/lib/PostScript/StandardEncoding.pm

namespace Smalot\PdfParser\Encoding;

/**
 * Class StandardEncoding
 */
class StandardEncoding extends AbstractEncoding
{
    public function getTranslations(): array
    {
        $encoding =
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          'space exclam quotedbl numbersign dollar percent ampersand quoteright '.
          'parenleft parenright asterisk plus comma hyphen period slash zero '.
          'one two three four five six seven eight nine colon semicolon less '.
          'equal greater question at A B C D E F G H I J K L M N O P Q R S T U '.
          'V W X Y Z bracketleft backslash bracketright asciicircum underscore '.
          'quoteleft a b c d e f g h i j k l m n o p q r s t u v w x y z '.
          'braceleft bar braceright asciitilde .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef exclamdown cent '.
          'sterling fraction yen florin section currency quotesingle '.
          'quotedblleft guillemotleft guilsinglleft guilsinglright fi fl '.
          '.notdef endash dagger daggerdbl periodcentered .notdef paragraph '.
          'bullet quotesinglbase quotedblbase quotedblright guillemotright '.
          'ellipsis perthousand .notdef questiondown .notdef grave acute '.
          'circumflex tilde macron breve dotaccent dieresis .notdef ring '.
          'cedilla .notdef hungarumlaut ogonek caron emdash .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef AE .notdef '.
          'ordfeminine .notdef .notdef .notdef .notdef Lslash Oslash OE '.
          'ordmasculine .notdef .notdef .notdef .notdef .notdef ae .notdef '.
          '.notdef .notdef dotlessi .notdef .notdef lslash oslash oe germandbls '.
          '.notdef .notdef .notdef .notdef';

        return explode(' ', $encoding);
    }
}
