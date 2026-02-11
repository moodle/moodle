<?php

/**
 * @file    This file is part of the PdfParser library.
 *
 * @author  Brian Huisman <bhuisman@greywyvern.com>
 *
 * @date    2023-06-28
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

// Source : https://opensource.adobe.com/dc-acrobat-sdk-docs/pdfstandards/pdfreference1.2.pdf
// Source : https://ia801001.us.archive.org/1/items/pdf1.7/pdf_reference_1-7.pdf

namespace Smalot\PdfParser\Encoding;

/**
 * Class PDFDocEncoding
 */
class PDFDocEncoding
{
    public static function getCodePage(): array
    {
        return [
            "\x18" => "\u{02d8}", // breve
            "\x19" => "\u{02c7}", // caron
            "\x1a" => "\u{02c6}", // circumflex
            "\x1b" => "\u{02d9}", // dotaccent
            "\x1c" => "\u{02dd}", // hungarumlaut
            "\x1d" => "\u{02db}", // ogonek
            "\x1e" => "\u{02de}", // ring
            "\x1f" => "\u{02dc}", // tilde
            "\x7f" => '',
            "\x80" => "\u{2022}", // bullet
            "\x81" => "\u{2020}", // dagger
            "\x82" => "\u{2021}", // daggerdbl
            "\x83" => "\u{2026}", // ellipsis
            "\x84" => "\u{2014}", // emdash
            "\x85" => "\u{2013}", // endash
            "\x86" => "\u{0192}", // florin
            "\x87" => "\u{2044}", // fraction
            "\x88" => "\u{2039}", // guilsinglleft
            "\x89" => "\u{203a}", // guilsinglright
            "\x8a" => "\u{2212}", // minus
            "\x8b" => "\u{2030}", // perthousand
            "\x8c" => "\u{201e}", // quotedblbase
            "\x8d" => "\u{201c}", // quotedblleft
            "\x8e" => "\u{201d}", // quotedblright
            "\x8f" => "\u{2018}", // quoteleft
            "\x90" => "\u{2019}", // quoteright
            "\x91" => "\u{201a}", // quotesinglbase
            "\x92" => "\u{2122}", // trademark
            "\x93" => "\u{fb01}", // fi
            "\x94" => "\u{fb02}", // fl
            "\x95" => "\u{0141}", // Lslash
            "\x96" => "\u{0152}", // OE
            "\x97" => "\u{0160}", // Scaron
            "\x98" => "\u{0178}", // Ydieresis
            "\x99" => "\u{017d}", // Zcaron
            "\x9a" => "\u{0131}", // dotlessi
            "\x9b" => "\u{0142}", // lslash
            "\x9c" => "\u{0153}", // oe
            "\x9d" => "\u{0161}", // scaron
            "\x9e" => "\u{017e}", // zcaron
            "\x9f" => '',
            "\xa0" => "\u{20ac}", // Euro
            "\xa1" => "\u{00a1}", // exclamdown
            "\xa2" => "\u{00a2}", // cent
            "\xa3" => "\u{00a3}", // sterling
            "\xa4" => "\u{00a4}", // currency
            "\xa5" => "\u{00a5}", // yen
            "\xa6" => "\u{00a6}", // brokenbar
            "\xa7" => "\u{00a7}", // section
            "\xa8" => "\u{00a8}", // dieresis
            "\xa9" => "\u{00a9}", // copyright
            "\xaa" => "\u{00aa}", // ordfeminine
            "\xab" => "\u{00ab}", // guillemotleft
            "\xac" => "\u{00ac}", // logicalnot
            "\xad" => '',
            "\xae" => "\u{00ae}", // registered
            "\xaf" => "\u{00af}", // macron
            "\xb0" => "\u{00b0}", // degree
            "\xb1" => "\u{00b1}", // plusminus
            "\xb2" => "\u{00b2}", // twosuperior
            "\xb3" => "\u{00b3}", // threesuperior
            "\xb4" => "\u{00b4}", // acute
            "\xb5" => "\u{00b5}", // mu
            "\xb6" => "\u{00b6}", // paragraph
            "\xb7" => "\u{00b7}", // periodcentered
            "\xb8" => "\u{00b8}", // cedilla
            "\xb9" => "\u{00b9}", // onesuperior
            "\xba" => "\u{00ba}", // ordmasculine
            "\xbb" => "\u{00bb}", // guillemotright
            "\xbc" => "\u{00bc}", // onequarter
            "\xbd" => "\u{00bd}", // onehalf
            "\xbe" => "\u{00be}", // threequarters
            "\xbf" => "\u{00bf}", // questiondown
            "\xc0" => "\u{00c0}", // Agrave
            "\xc1" => "\u{00c1}", // Aacute
            "\xc2" => "\u{00c2}", // Acircumflex
            "\xc3" => "\u{00c3}", // Atilde
            "\xc4" => "\u{00c4}", // Adieresis
            "\xc5" => "\u{00c5}", // Aring
            "\xc6" => "\u{00c6}", // AE
            "\xc7" => "\u{00c7}", // Ccedill
            "\xc8" => "\u{00c8}", // Egrave
            "\xc9" => "\u{00c9}", // Eacute
            "\xca" => "\u{00ca}", // Ecircumflex
            "\xcb" => "\u{00cb}", // Edieresis
            "\xcc" => "\u{00cc}", // Igrave
            "\xcd" => "\u{00cd}", // Iacute
            "\xce" => "\u{00ce}", // Icircumflex
            "\xcf" => "\u{00cf}", // Idieresis
            "\xd0" => "\u{00d0}", // Eth
            "\xd1" => "\u{00d1}", // Ntilde
            "\xd2" => "\u{00d2}", // Ograve
            "\xd3" => "\u{00d3}", // Oacute
            "\xd4" => "\u{00d4}", // Ocircumflex
            "\xd5" => "\u{00d5}", // Otilde
            "\xd6" => "\u{00d6}", // Odieresis
            "\xd7" => "\u{00d7}", // multiply
            "\xd8" => "\u{00d8}", // Oslash
            "\xd9" => "\u{00d9}", // Ugrave
            "\xda" => "\u{00da}", // Uacute
            "\xdb" => "\u{00db}", // Ucircumflex
            "\xdc" => "\u{00dc}", // Udieresis
            "\xdd" => "\u{00dd}", // Yacute
            "\xde" => "\u{00de}", // Thorn
            "\xdf" => "\u{00df}", // germandbls
            "\xe0" => "\u{00e0}", // agrave
            "\xe1" => "\u{00e1}", // aacute
            "\xe2" => "\u{00e2}", // acircumflex
            "\xe3" => "\u{00e3}", // atilde
            "\xe4" => "\u{00e4}", // adieresis
            "\xe5" => "\u{00e5}", // aring
            "\xe6" => "\u{00e6}", // ae
            "\xe7" => "\u{00e7}", // ccedilla
            "\xe8" => "\u{00e8}", // egrave
            "\xe9" => "\u{00e9}", // eacute
            "\xea" => "\u{00ea}", // ecircumflex
            "\xeb" => "\u{00eb}", // edieresis
            "\xec" => "\u{00ec}", // igrave
            "\xed" => "\u{00ed}", // iacute
            "\xee" => "\u{00ee}", // icircumflex
            "\xef" => "\u{00ef}", // idieresis
            "\xf0" => "\u{00f0}", // eth
            "\xf1" => "\u{00f1}", // ntilde
            "\xf2" => "\u{00f2}", // ograve
            "\xf3" => "\u{00f3}", // oacute
            "\xf4" => "\u{00f4}", // ocircumflex
            "\xf5" => "\u{00f5}", // otilde
            "\xf6" => "\u{00f6}", // odieresis
            "\xf7" => "\u{00f7}", // divide
            "\xf8" => "\u{00f8}", // oslash
            "\xf9" => "\u{00f9}", // ugrave
            "\xfa" => "\u{00fa}", // uacute
            "\xfb" => "\u{00fb}", // ucircumflex
            "\xfc" => "\u{00fc}", // udieresis
            "\xfd" => "\u{00fd}", // yacute
            "\xfe" => "\u{00fe}", // thorn
            "\xff" => "\u{00ff}", // ydieresis
        ];
    }

    public static function convertPDFDoc2UTF8(string $content): string
    {
        return strtr($content, static::getCodePage());
    }
}
