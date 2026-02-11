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

// Source : http://www.opensource.apple.com/source/vim/vim-34/vim/runtime/print/mac-roman.ps

namespace Smalot\PdfParser\Encoding;

/**
 * Class MacRomanEncoding
 */
class MacRomanEncoding extends AbstractEncoding
{
    public function getTranslations(): array
    {
        $encoding =
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          '.notdef .notdef .notdef .notdef .notdef .notdef .notdef .notdef '.
          'space exclam quotedbl numbersign dollar percent ampersand quotesingle '.
          'parenleft parenright asterisk plus comma minus period slash '.
          'zero one two three four five six seven '.
          'eight nine colon semicolon less equal greater question '.
          'at A B C D E F G '.
          'H I J K L M N O '.
          'P Q R S T U V W '.
          'X Y Z bracketleft backslash bracketright asciicircum underscore '.
          'grave a b c d e f g '.
          'h i j k l m n o '.
          'p q r s t u v w '.
          'x y z braceleft bar braceright asciitilde .notdef '.
          'Adieresis Aring Ccedilla Eacute Ntilde Odieresis Udieresis aacute '.
          'agrave acircumflex adieresis atilde aring ccedilla eacute egrave '.
          'ecircumflex edieresis iacute igrave icircumflex idieresis ntilde oacute '.
          'ograve ocircumflex odieresis otilde uacute ugrave ucircumflex udieresis '.
          'dagger degree cent sterling section bullet paragraph germandbls '.
          'registered copyright trademark acute dieresis notequal AE Oslash '.
          'infinity plusminus lessequal greaterequal yen mu partialdiff summation '.
          'Pi pi integral ordfeminine ordmasculine Omega ae oslash '.
          'questiondown exclamdown logicalnot radical florin approxequal delta guillemotleft '.
          'guillemotright ellipsis space Agrave Atilde Otilde OE oe '.
          'endash emdash quotedblleft quotedblright quoteleft quoteright divide lozenge '.
          'ydieresis Ydieresis fraction currency guilsinglleft guilsinglright fi fl '.
          'daggerdbl periodcentered quotesinglbase quotedblbase perthousand Acircumflex Ecircumflex Aacute '.
          'Edieresis Egrave Iacute Icircumflex Idieresis Igrave Oacute Ocircumflex '.
          'heart Ograve Uacute Ucircumflex Ugrave dotlessi circumflex tilde '.
          'macron breve dotaccent ring cedilla hungarumlaut ogonek caron';

        return explode(' ', $encoding);
    }
}
