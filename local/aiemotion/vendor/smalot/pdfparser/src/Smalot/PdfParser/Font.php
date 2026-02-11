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

use Smalot\PdfParser\Encoding\WinAnsiEncoding;
use Smalot\PdfParser\Exception\EncodingNotFoundException;

/**
 * Class Font
 */
class Font extends PDFObject
{
    public const MISSING = '?';

    /**
     * @var array
     */
    protected $table;

    /**
     * @var array
     */
    protected $tableSizes;

    /**
     * Caches results from uchr.
     *
     * @var array
     */
    private static $uchrCache = [];

    /**
     * In some PDF-files encoding could be referenced by object id but object itself does not contain
     * `/Type /Encoding` in its dictionary. These objects wouldn't be initialized as Encoding in
     * \Smalot\PdfParser\PDFObject::factory() during file parsing (they would be just PDFObject).
     *
     * Therefore, we create an instance of Encoding from them during decoding and cache this value in this property.
     *
     * @var Encoding
     *
     * @see https://github.com/smalot/pdfparser/pull/500
     */
    private $initializedEncodingByPdfObject;

    public function init()
    {
        // Load translate table.
        $this->loadTranslateTable();
    }

    public function getName(): string
    {
        return $this->has('BaseFont') ? (string) $this->get('BaseFont') : '[Unknown]';
    }

    public function getType(): string
    {
        return (string) $this->header->get('Subtype');
    }

    public function getDetails(bool $deep = true): array
    {
        $details = [];

        $details['Name'] = $this->getName();
        $details['Type'] = $this->getType();
        $details['Encoding'] = ($this->has('Encoding') ? (string) $this->get('Encoding') : 'Ansi');

        $details += parent::getDetails($deep);

        return $details;
    }

    /**
     * @return string|bool
     */
    public function translateChar(string $char, bool $use_default = true)
    {
        $dec = hexdec(bin2hex($char));

        if (\array_key_exists($dec, $this->table)) {
            return $this->table[$dec];
        }

        // fallback for decoding single-byte ANSI characters that are not in the lookup table
        $fallbackDecoded = $char;
        if (
            \strlen($char) < 2
            && $this->has('Encoding')
            && $this->get('Encoding') instanceof Encoding
        ) {
            try {
                if (WinAnsiEncoding::class === $this->get('Encoding')->__toString()) {
                    $fallbackDecoded = self::uchr($dec);
                }
            } catch (EncodingNotFoundException $e) {
                // Encoding->getEncodingClass() throws EncodingNotFoundException when BaseEncoding doesn't exists
                // See table 5.11 on PDF 1.5 specs for more info
            }
        }

        return $use_default ? self::MISSING : $fallbackDecoded;
    }

    /**
     * Convert unicode character code to "utf-8" encoded string.
     *
     * @param int|float $code Unicode character code. Will be casted to int internally!
     */
    public static function uchr($code): string
    {
        // note:
        // $code was typed as int before, but changed in https://github.com/smalot/pdfparser/pull/623
        // because in some cases uchr was called with a float instead of an integer.
        $code = (int) $code;

        if (!isset(self::$uchrCache[$code])) {
            // html_entity_decode() will not work with UTF-16 or UTF-32 char entities,
            // therefore, we use mb_convert_encoding() instead
            self::$uchrCache[$code] = mb_convert_encoding("&#{$code};", 'UTF-8', 'HTML-ENTITIES');
        }

        return self::$uchrCache[$code];
    }

    /**
     * Init internal chars translation table by ToUnicode CMap.
     */
    public function loadTranslateTable(): array
    {
        if (null !== $this->table) {
            return $this->table;
        }

        $this->table = [];
        $this->tableSizes = [
            'from' => 1,
            'to' => 1,
        ];

        if ($this->has('ToUnicode')) {
            $content = $this->get('ToUnicode')->getContent();
            $matches = [];

            // Support for multiple spacerange sections
            if (preg_match_all('/begincodespacerange(?P<sections>.*?)endcodespacerange/s', $content, $matches)) {
                foreach ($matches['sections'] as $section) {
                    $regexp = '/<(?P<from>[0-9A-F]+)> *<(?P<to>[0-9A-F]+)>[ \r\n]+/is';

                    preg_match_all($regexp, $section, $matches);

                    $this->tableSizes = [
                        'from' => max(1, \strlen(current($matches['from'])) / 2),
                        'to' => max(1, \strlen(current($matches['to'])) / 2),
                    ];

                    break;
                }
            }

            // Support for multiple bfchar sections
            if (preg_match_all('/beginbfchar(?P<sections>.*?)endbfchar/s', $content, $matches)) {
                foreach ($matches['sections'] as $section) {
                    $regexp = '/<(?P<from>[0-9A-F]+)> *<(?P<to>[0-9A-F]+)>[ \r\n]+/is';

                    preg_match_all($regexp, $section, $matches);

                    $this->tableSizes['from'] = max(1, \strlen(current($matches['from'])) / 2);

                    foreach ($matches['from'] as $key => $from) {
                        $parts = preg_split(
                            '/([0-9A-F]{4})/i',
                            $matches['to'][$key],
                            0,
                            \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE
                        );
                        $text = '';
                        foreach ($parts as $part) {
                            $text .= self::uchr(hexdec($part));
                        }
                        $this->table[hexdec($from)] = $text;
                    }
                }
            }

            // Support for multiple bfrange sections
            if (preg_match_all('/beginbfrange(?P<sections>.*?)endbfrange/s', $content, $matches)) {
                foreach ($matches['sections'] as $section) {
                    /**
                     * Regexp to capture <from>, <to>, and either <offset> or [...] items.
                     * - (?P<from>...) Source range's start
                     * - (?P<to>...)   Source range's end
                     * - (?P<dest>...) Destination range's offset or each char code
                     *                 Some PDF file has 2-byte Unicode values on new lines > added \r\n
                     */
                    $regexp = '/<(?P<from>[0-9A-F]+)> *<(?P<to>[0-9A-F]+)> *(?P<dest><[0-9A-F]+>|\[[\r\n<>0-9A-F ]+\])[ \r\n]+/is';

                    preg_match_all($regexp, $section, $matches);

                    foreach ($matches['from'] as $key => $from) {
                        $char_from = hexdec($from);
                        $char_to = hexdec($matches['to'][$key]);
                        $dest = $matches['dest'][$key];

                        if (1 === preg_match('/^<(?P<offset>[0-9A-F]+)>$/i', $dest, $offset_matches)) {
                            // Support for : <srcCode1> <srcCode2> <dstString>
                            $offset = hexdec($offset_matches['offset']);

                            for ($char = $char_from; $char <= $char_to; ++$char) {
                                $this->table[$char] = self::uchr($char - $char_from + $offset);
                            }
                        } else {
                            // Support for : <srcCode1> <srcCodeN> [<dstString1> <dstString2> ... <dstStringN>]
                            $strings = [];
                            $matched = preg_match_all('/<(?P<string>[0-9A-F]+)> */is', $dest, $strings);
                            if (false === $matched || 0 === $matched) {
                                continue;
                            }

                            foreach ($strings['string'] as $position => $string) {
                                $parts = preg_split(
                                    '/([0-9A-F]{4})/i',
                                    $string,
                                    0,
                                    \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE
                                );
                                if (false === $parts) {
                                    continue;
                                }
                                $text = '';
                                foreach ($parts as $part) {
                                    $text .= self::uchr(hexdec($part));
                                }
                                $this->table[$char_from + $position] = $text;
                            }
                        }
                    }
                }
            }
        }

        return $this->table;
    }

    /**
     * Set custom char translation table where:
     * - key - integer character code;
     * - value - "utf-8" encoded value;
     *
     * @return void
     */
    public function setTable(array $table)
    {
        $this->table = $table;
    }

    /**
     * Calculate text width with data from header 'Widths'. If width of character is not found then character is added to missing array.
     */
    public function calculateTextWidth(string $text, ?array &$missing = null): ?float
    {
        $index_map = array_flip($this->table);
        $details = $this->getDetails();

        // Usually, Widths key is set in $details array, but if it isn't use an empty array instead.
        $widths = $details['Widths'] ?? [];

        /*
         * Widths array is zero indexed but table is not. We must map them based on FirstChar and LastChar
         *
         * Note: Without the change you would see warnings in PHP 8.4 because the values of FirstChar or LastChar
         *       can be null sometimes.
         */
        $width_map = array_flip(range((int) $details['FirstChar'], (int) $details['LastChar']));

        $width = null;
        $missing = [];
        $textLength = mb_strlen($text);
        for ($i = 0; $i < $textLength; ++$i) {
            $char = mb_substr($text, $i, 1);
            if (
                !\array_key_exists($char, $index_map)
                || !\array_key_exists($index_map[$char], $width_map)
                || !\array_key_exists($width_map[$index_map[$char]], $widths)
            ) {
                $missing[] = $char;
                continue;
            }
            $width_index = $width_map[$index_map[$char]];
            $width += $widths[$width_index];
        }

        return $width;
    }

    /**
     * Decode hexadecimal encoded string. If $add_braces is true result value would be wrapped by parentheses.
     */
    public static function decodeHexadecimal(string $hexa, bool $add_braces = false): string
    {
        // Special shortcut for XML content.
        if (false !== stripos($hexa, '<?xml')) {
            return $hexa;
        }

        $text = '';
        $parts = preg_split('/(<[a-f0-9\s]+>)/si', $hexa, -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);

        foreach ($parts as $part) {
            if (preg_match('/^<[a-f0-9\s]+>$/si', $part)) {
                // strip whitespace
                $part = preg_replace("/\s/", '', $part);
                $part = trim($part, '<>');
                if ($add_braces) {
                    $text .= '(';
                }

                $part = pack('H*', $part);
                $text .= ($add_braces ? preg_replace('/\\\/s', '\\\\\\', $part) : $part);

                if ($add_braces) {
                    $text .= ')';
                }
            } else {
                $text .= $part;
            }
        }

        return $text;
    }

    /**
     * Decode string with octal-decoded chunks.
     */
    public static function decodeOctal(string $text): string
    {
        // Replace all double backslashes \\ with a special string
        $text = strtr($text, ['\\\\' => '[**pdfparserdblslsh**]']);

        // Now we can replace all octal codes without worrying about
        // escaped backslashes
        $text = preg_replace_callback('/\\\\([0-7]{1,3})/', function ($m) {
            return \chr(octdec($m[1]));
        }, $text);

        // Unescape any parentheses
        $text = str_replace(['\\(', '\\)'], ['(', ')'], $text);

        // Replace instances of the special string with a single backslash
        return str_replace('[**pdfparserdblslsh**]', '\\', $text);
    }

    /**
     * Decode string with html entity encoded chars.
     */
    public static function decodeEntities(string $text): string
    {
        return preg_replace_callback('/#([0-9a-f]{2})/i', function ($m) {
            return \chr(hexdec($m[1]));
        }, $text);
    }

    /**
     * Check if given string is Unicode text (by BOM);
     * If true - decode to "utf-8" encoded string.
     * Otherwise - return text as is.
     *
     * @todo Rename in next major release to make the name correspond to reality (for ex. decodeIfUnicode())
     */
    public static function decodeUnicode(string $text): string
    {
        if ("\xFE\xFF" === substr($text, 0, 2)) {
            // Strip U+FEFF byte order marker.
            $decode = substr($text, 2);
            $text = '';
            $length = \strlen($decode);

            for ($i = 0; $i < $length; $i += 2) {
                $text .= self::uchr(hexdec(bin2hex(substr($decode, $i, 2))));
            }
        }

        return $text;
    }

    /**
     * @todo Deprecated, use $this->config->getFontSpaceLimit() instead.
     */
    protected function getFontSpaceLimit(): int
    {
        return $this->config->getFontSpaceLimit();
    }

    /**
     * Decode text by commands array.
     */
    public function decodeText(array $commands, float $fontFactor = 4): string
    {
        $word_position = 0;
        $words = [];
        $font_space = $this->getFontSpaceLimit() * abs($fontFactor) / 4;

        foreach ($commands as $command) {
            switch ($command[PDFObject::TYPE]) {
                case 'n':
                    $offset = (float) trim($command[PDFObject::COMMAND]);
                    if ($offset - (float) $font_space < 0) {
                        $word_position = \count($words);
                    }
                    continue 2;
                case '<':
                    // Decode hexadecimal.
                    $text = self::decodeHexadecimal('<'.$command[PDFObject::COMMAND].'>');
                    break;

                default:
                    // Decode octal (if necessary).
                    $text = self::decodeOctal($command[PDFObject::COMMAND]);
            }

            // replace escaped chars
            $text = str_replace(
                ['\\\\', '\(', '\)', '\n', '\r', '\t', '\f', '\ ', '\b'],
                [\chr(92), \chr(40), \chr(41), \chr(10), \chr(13), \chr(9), \chr(12), \chr(32), \chr(8)],
                $text
            );

            // add content to result string
            if (isset($words[$word_position])) {
                $words[$word_position] .= $text;
            } else {
                $words[$word_position] = $text;
            }
        }

        foreach ($words as &$word) {
            $word = $this->decodeContent($word);
            $word = str_replace("\t", ' ', $word);
        }

        // Remove internal "words" that are just spaces, but leave them
        // if they are at either end of the array of words. This fixes,
        // for   example,   lines   that   are   justified   to   fill
        // a whole row.
        for ($x = \count($words) - 2; $x >= 1; --$x) {
            if ('' === trim($words[$x], ' ')) {
                unset($words[$x]);
            }
        }
        $words = array_values($words);

        // Cut down on the number of unnecessary internal spaces by
        // imploding the string on the null byte, and checking if the
        // text includes extra spaces on either side. If so, merge
        // where appropriate.
        $words = implode("\x00\x00", $words);
        $words = str_replace(
            [" \x00\x00 ", "\x00\x00 ", " \x00\x00", "\x00\x00"],
            ['  ', ' ', ' ', ' '],
            $words
        );

        return $words;
    }

    /**
     * Decode given $text to "utf-8" encoded string.
     *
     * @param bool $unicode This parameter is deprecated and might be removed in a future release
     */
    public function decodeContent(string $text, ?bool &$unicode = null): string
    {
        // If this string begins with a UTF-16BE BOM, then decode it
        // directly as Unicode
        if ("\xFE\xFF" === substr($text, 0, 2)) {
            return $this->decodeUnicode($text);
        }

        if ($this->has('ToUnicode')) {
            return $this->decodeContentByToUnicodeCMapOrDescendantFonts($text);
        }

        if ($this->has('Encoding')) {
            $result = $this->decodeContentByEncoding($text);

            if (null !== $result) {
                return $result;
            }
        }

        return $this->decodeContentByAutodetectIfNecessary($text);
    }

    /**
     * First try to decode $text by ToUnicode CMap.
     * If char translation not found in ToUnicode CMap tries:
     *  - If DescendantFonts exists tries to decode char by one of that fonts.
     *      - If have no success to decode by DescendantFonts interpret $text as a string with "Windows-1252" encoding.
     *  - If DescendantFonts does not exist just return "?" as decoded char.
     *
     * @todo Seems this is invalid algorithm that do not follow pdf-format specification. Must be rewritten.
     */
    private function decodeContentByToUnicodeCMapOrDescendantFonts(string $text): string
    {
        $bytes = $this->tableSizes['from'];

        if ($bytes) {
            $result = '';
            $length = \strlen($text);

            for ($i = 0; $i < $length; $i += $bytes) {
                $char = substr($text, $i, $bytes);

                if (false !== ($decoded = $this->translateChar($char, false))) {
                    $char = $decoded;
                } elseif ($this->has('DescendantFonts')) {
                    if ($this->get('DescendantFonts') instanceof PDFObject) {
                        $fonts = $this->get('DescendantFonts')->getHeader()->getElements();
                    } else {
                        $fonts = $this->get('DescendantFonts')->getContent();
                    }
                    $decoded = false;

                    foreach ($fonts as $font) {
                        if ($font instanceof self) {
                            if (false !== ($decoded = $font->translateChar($char, false))) {
                                $decoded = mb_convert_encoding($decoded, 'UTF-8', 'Windows-1252');
                                break;
                            }
                        }
                    }

                    if (false !== $decoded) {
                        $char = $decoded;
                    } else {
                        $char = mb_convert_encoding($char, 'UTF-8', 'Windows-1252');
                    }
                } else {
                    $char = self::MISSING;
                }

                $result .= $char;
            }

            $text = $result;
        }

        return $text;
    }

    /**
     * Decode content by any type of Encoding (dictionary's item) instance.
     */
    private function decodeContentByEncoding(string $text): ?string
    {
        $encoding = $this->get('Encoding');

        // When Encoding referenced by object id (/Encoding 520 0 R) but object itself does not contain `/Type /Encoding` in it's dictionary.
        if ($encoding instanceof PDFObject) {
            $encoding = $this->getInitializedEncodingByPdfObject($encoding);
        }

        // When Encoding referenced by object id (/Encoding 520 0 R) but object itself contains `/Type /Encoding` in it's dictionary.
        if ($encoding instanceof Encoding) {
            return $this->decodeContentByEncodingEncoding($text, $encoding);
        }

        // When Encoding is just string (/Encoding /WinAnsiEncoding)
        if ($encoding instanceof Element) { // todo: ElementString class must by used?
            return $this->decodeContentByEncodingElement($text, $encoding);
        }

        // don't double-encode strings already in UTF-8
        if (!mb_check_encoding($text, 'UTF-8')) {
            return mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        }

        return $text;
    }

    /**
     * Returns already created or create a new one if not created before Encoding instance by PDFObject instance.
     */
    private function getInitializedEncodingByPdfObject(PDFObject $PDFObject): Encoding
    {
        if (!$this->initializedEncodingByPdfObject) {
            $this->initializedEncodingByPdfObject = $this->createInitializedEncodingByPdfObject($PDFObject);
        }

        return $this->initializedEncodingByPdfObject;
    }

    /**
     * Decode content when $encoding (given by $this->get('Encoding')) is instance of Encoding.
     */
    private function decodeContentByEncodingEncoding(string $text, Encoding $encoding): string
    {
        $result = '';
        $length = \strlen($text);

        for ($i = 0; $i < $length; ++$i) {
            $dec_av = hexdec(bin2hex($text[$i]));
            $dec_ap = $encoding->translateChar($dec_av);
            $result .= self::uchr($dec_ap ?? $dec_av);
        }

        return $result;
    }

    /**
     * Decode content when $encoding (given by $this->get('Encoding')) is instance of Element.
     */
    private function decodeContentByEncodingElement(string $text, Element $encoding): ?string
    {
        $pdfEncodingName = $encoding->getContent();

        // mb_convert_encoding does not support MacRoman/macintosh,
        // so we use iconv() here
        $iconvEncodingName = $this->getIconvEncodingNameOrNullByPdfEncodingName($pdfEncodingName);

        return $iconvEncodingName ? iconv($iconvEncodingName, 'UTF-8//TRANSLIT//IGNORE', $text) : null;
    }

    /**
     * Convert PDF encoding name to iconv-known encoding name.
     */
    private function getIconvEncodingNameOrNullByPdfEncodingName(string $pdfEncodingName): ?string
    {
        $pdfToIconvEncodingNameMap = [
            'StandardEncoding' => 'ISO-8859-1',
            'MacRomanEncoding' => 'MACINTOSH',
            'WinAnsiEncoding' => 'CP1252',
        ];

        return \array_key_exists($pdfEncodingName, $pdfToIconvEncodingNameMap)
            ? $pdfToIconvEncodingNameMap[$pdfEncodingName]
            : null;
    }

    /**
     * If string seems like "utf-8" encoded string do nothing and just return given string as is.
     * Otherwise, interpret string as "Window-1252" encoded string.
     *
     * @return string|false
     */
    private function decodeContentByAutodetectIfNecessary(string $text)
    {
        if (mb_check_encoding($text, 'UTF-8')) {
            return $text;
        }

        return mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        // todo: Why exactly `Windows-1252` used?
    }

    /**
     * Create Encoding instance by PDFObject instance and init it.
     */
    private function createInitializedEncodingByPdfObject(PDFObject $PDFObject): Encoding
    {
        $encoding = $this->createEncodingByPdfObject($PDFObject);
        $encoding->init();

        return $encoding;
    }

    /**
     * Create Encoding instance by PDFObject instance (without init).
     */
    private function createEncodingByPdfObject(PDFObject $PDFObject): Encoding
    {
        $document = $PDFObject->getDocument();
        $header = $PDFObject->getHeader();
        $content = $PDFObject->getContent();
        $config = $PDFObject->getConfig();

        return new Encoding($document, $header, $content, $config);
    }
}
