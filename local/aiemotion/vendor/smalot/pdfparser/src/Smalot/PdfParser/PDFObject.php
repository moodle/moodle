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

use Smalot\PdfParser\Exception\InvalidDictionaryObjectException;
use Smalot\PdfParser\XObject\Form;
use Smalot\PdfParser\XObject\Image;

/**
 * Class PDFObject
 */
class PDFObject
{
    public const TYPE = 't';

    public const OPERATOR = 'o';

    public const COMMAND = 'c';

    /**
     * The recursion stack.
     *
     * @var array
     */
    public static $recursionStack = [];

    /**
     * @var Document|null
     */
    protected $document;

    /**
     * @var Header
     */
    protected $header;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var Config|null
     */
    protected $config;

    /**
     * @var bool
     */
    protected $addPositionWhitespace = false;

    public function __construct(
        Document $document,
        ?Header $header = null,
        ?string $content = null,
        ?Config $config = null
    ) {
        $this->document = $document;
        $this->header = $header ?? new Header();
        $this->content = $content;
        $this->config = $config;
    }

    public function init()
    {
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function getHeader(): ?Header
    {
        return $this->header;
    }

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    /**
     * @return Element|PDFObject|Header
     */
    public function get(string $name)
    {
        return $this->header->get($name);
    }

    public function has(string $name): bool
    {
        return $this->header->has($name);
    }

    public function getDetails(bool $deep = true): array
    {
        return $this->header->getDetails($deep);
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Creates a duplicate of the document stream with
     * strings and other items replaced by $char. Formerly
     * getSectionsText() used this output to more easily gather offset
     * values to extract text from the *actual* document stream.
     *
     * @deprecated function is no longer used and will be removed in a future release
     *
     * @internal
     */
    public function cleanContent(string $content, string $char = 'X')
    {
        $char = $char[0];
        $content = str_replace(['\\\\', '\\)', '\\('], $char.$char, $content);

        // Remove image bloc with binary content
        preg_match_all('/\s(BI\s.*?(\sID\s).*?(\sEI))\s/s', $content, $matches, \PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as $part) {
            $content = substr_replace($content, str_repeat($char, \strlen($part[0])), $part[1], \strlen($part[0]));
        }

        // Clean content in square brackets [.....]
        preg_match_all('/\[((\(.*?\)|[0-9\.\-\s]*)*)\]/s', $content, $matches, \PREG_OFFSET_CAPTURE);
        foreach ($matches[1] as $part) {
            $content = substr_replace($content, str_repeat($char, \strlen($part[0])), $part[1], \strlen($part[0]));
        }

        // Clean content in round brackets (.....)
        preg_match_all('/\((.*?)\)/s', $content, $matches, \PREG_OFFSET_CAPTURE);
        foreach ($matches[1] as $part) {
            $content = substr_replace($content, str_repeat($char, \strlen($part[0])), $part[1], \strlen($part[0]));
        }

        // Clean structure
        if ($parts = preg_split('/(<|>)/s', $content, -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE)) {
            $content = '';
            $level = 0;
            foreach ($parts as $part) {
                if ('<' == $part) {
                    ++$level;
                }

                $content .= (0 == $level ? $part : str_repeat($char, \strlen($part)));

                if ('>' == $part) {
                    --$level;
                }
            }
        }

        // Clean BDC and EMC markup
        preg_match_all(
            '/(\/[A-Za-z0-9\_]*\s*'.preg_quote($char).'*BDC)/s',
            $content,
            $matches,
            \PREG_OFFSET_CAPTURE
        );
        foreach ($matches[1] as $part) {
            $content = substr_replace($content, str_repeat($char, \strlen($part[0])), $part[1], \strlen($part[0]));
        }

        preg_match_all('/\s(EMC)\s/s', $content, $matches, \PREG_OFFSET_CAPTURE);
        foreach ($matches[1] as $part) {
            $content = substr_replace($content, str_repeat($char, \strlen($part[0])), $part[1], \strlen($part[0]));
        }

        return $content;
    }

    /**
     * Takes a string of PDF document stream text and formats
     * it into a multi-line string with one PDF command on each line,
     * separated by \r\n. If the given string is null, or binary data
     * is detected instead of a document stream then return an empty
     * string.
     */
    private function formatContent(?string $content): string
    {
        if (null === $content) {
            return '';
        }

        // Outside of (String) and inline image content in PDF document
        // streams, all text should conform to UTF-8. Test for binary
        // content by deleting everything after the first open-
        // parenthesis ( which indicates the beginning of a string, or
        // the first ID command which indicates the beginning of binary
        // inline image content. Then test what remains for valid
        // UTF-8. If it's not UTF-8, return an empty string as this
        // $content is most likely binary. Unfortunately, using
        // mb_check_encoding(..., 'UTF-8') is not strict enough, so the
        // following regexp, adapted from the W3, is used. See:
        // https://www.w3.org/International/questions/qa-forms-utf-8.en
        // We use preg_replace() instead of preg_match() to avoid "JIT
        // stack limit exhausted" errors on larger files.
        $utf8Filter = preg_replace('/(
            [\x09\x0A\x0D\x20-\x7E] |            # ASCII
            [\xC2-\xDF][\x80-\xBF] |             # non-overlong 2-byte
            \xE0[\xA0-\xBF][\x80-\xBF] |         # excluding overlongs
            [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} |  # straight 3-byte
            \xED[\x80-\x9F][\x80-\xBF] |         # excluding surrogates
            \xF0[\x90-\xBF][\x80-\xBF]{2} |      # planes 1-3
            [\xF1-\xF3][\x80-\xBF]{3} |          # planes 4-15
            \xF4[\x80-\x8F][\x80-\xBF]{2}        # plane 16
        )/xs', '', preg_replace('/(\(|ID\s).*$/s', '', $content));

        if ('' !== $utf8Filter) {
            return '';
        }

        // Find all inline image content and replace them so they aren't
        // affected by the next steps
        $pdfInlineImages = [];
        $offsetBI = 0;
        while (preg_match('/\sBI\s(\/.+?)\sID\s(.+?)\sEI(?=\s|$)/s', $content, $text, \PREG_OFFSET_CAPTURE, $offsetBI)) {
            // Attempt to detemine if this instance of the 'BI' command
            // actually occured within a (string) using the following
            // steps:

            // Step 1: Remove any escaped slashes and parentheses from
            // the alleged image characteristics data
            $para = str_replace(['\\\\', '\\(', '\\)'], '', $text[1][0]);

            // Step 2: Remove all correctly ordered and balanced
            // parentheses from (strings)
            do {
                $paraTest = $para;
                $para = preg_replace('/\(([^()]*)\)/', '$1', $paraTest);
            } while ($para != $paraTest);

            $paraOpen = strpos($para, '(');
            $paraClose = strpos($para, ')');

            // Check: If the remaining text contains a close parenthesis
            // ')' AND it occurs before any open parenthesis, then we
            // are almost certain to be inside a (string)
            if (0 < $paraClose && (false === $paraOpen || $paraClose < $paraOpen)) {
                // Bump the search offset forward and match again
                $offsetBI = (int) $text[1][1];
                continue;
            }

            // Step 3: Double check that this is actually inline image
            // data by parsing the alleged image characteristics as a
            // dictionary
            $dict = $this->parseDictionary('<<'.$text[1][0].'>>');

            // Check if an image Width and Height are set in the dict
            if ((isset($dict['W']) || isset($dict['Width']))
                && (isset($dict['H']) || isset($dict['Height']))) {
                $id = uniqid('IMAGE_', true);
                $pdfInlineImages[$id] = [
                    preg_replace(['/\r\n/', '/\r/', '/\n/'], ' ', $text[1][0]),
                    preg_replace(['/\r\n/', '/\r/', '/\n/'], '', $text[2][0]),
                ];
                $content = preg_replace(
                    '/'.preg_quote($text[0][0], '/').'/',
                    '^^^'.$id.'^^^',
                    $content,
                    1
                );
            } else {
                // If there was no valid dictionary, or a height and width
                // weren't specified, then we don't know what this is, so
                // just leave it alone; bump the search offset forward and
                // match again
                $offsetBI = (int) $text[1][1];
            }
        }

        // Find all strings () and replace them so they aren't affected
        // by the next steps
        $pdfstrings = [];
        $attempt = '(';
        while (preg_match('/'.preg_quote($attempt, '/').'.*?\)/s', $content, $text)) {
            // Remove all escaped slashes and parentheses from the target text
            $para = str_replace(['\\\\', '\\(', '\\)'], '', $text[0]);

            // PDF strings can contain unescaped parentheses as long as
            // they're balanced, so check for balanced parentheses
            $left = preg_match_all('/\(/', $para);
            $right = preg_match_all('/\)/', $para);

            if (')' == $para[-1] && $left == $right) {
                // Replace the string with a unique placeholder
                $id = uniqid('STRING_', true);
                $pdfstrings[$id] = $text[0];
                $content = preg_replace(
                    '/'.preg_quote($text[0], '/').'/',
                    '@@@'.$id.'@@@',
                    $content,
                    1
                );

                // Reset to search for the next string
                $attempt = '(';
            } else {
                // We had unbalanced parentheses, so use the current
                // match as a base to find a longer string
                $attempt = $text[0];
            }
        }

        // Remove all carriage returns and line-feeds from the document stream
        $content = str_replace(["\r", "\n"], ' ', trim($content));

        // Find all dictionary << >> commands and replace them so they
        // aren't affected by the next steps
        $dictstore = [];
        while (preg_match('/(<<.*?>> *)(BDC|BMC|DP|MP)/s', $content, $dicttext)) {
            $dictid = uniqid('DICT_', true);
            $dictstore[$dictid] = $dicttext[1];
            $content = preg_replace(
                '/'.preg_quote($dicttext[0], '/').'/',
                ' ###'.$dictid.'###'.$dicttext[2],
                $content,
                1
            );
        }

        // Normalize white-space in the document stream
        $content = preg_replace('/\s{2,}/', ' ', $content);

        // Find all valid PDF operators and add \r\n after each; this
        // ensures there is just one command on every line
        // Source: https://ia801001.us.archive.org/1/items/pdf1.7/pdf_reference_1-7.pdf - Appendix A
        // Source: https://archive.org/download/pdf320002008/PDF32000_2008.pdf - Annex A
        // Note: PDF Reference 1.7 lists 'I' and 'rI' as valid commands, while
        //       PDF 32000:2008 lists them as 'i' and 'ri' respectively. Both versions
        //       appear here in the list for completeness.
        $operators = [
            'b*', 'b', 'BDC', 'BMC', 'B*', 'BI', 'BT', 'BX', 'B', 'cm', 'cs', 'c', 'CS',
            'd0', 'd1', 'd', 'Do', 'DP', 'EMC', 'EI', 'ET', 'EX', 'f*', 'f', 'F', 'gs',
            'g', 'G',  'h', 'i', 'ID', 'I', 'j', 'J', 'k', 'K', 'l', 'm', 'MP', 'M', 'n',
            'q', 'Q', 're', 'rg', 'ri', 'rI', 'RG', 'scn', 'sc', 'sh', 's', 'SCN', 'SC',
            'S', 'T*', 'Tc', 'Td', 'TD', 'Tf', 'TJ', 'Tj', 'TL', 'Tm', 'Tr', 'Ts', 'Tw',
            'Tz', 'v', 'w', 'W*', 'W', 'y', '\'', '"',
        ];
        foreach ($operators as $operator) {
            $content = preg_replace(
                '/(?<!\w|\/)'.preg_quote($operator, '/').'(?![\w10\*])/',
                $operator."\r\n",
                $content
            );
        }

        // Restore the original content of the dictionary << >> commands
        $dictstore = array_reverse($dictstore, true);
        foreach ($dictstore as $id => $dict) {
            $content = str_replace('###'.$id.'###', $dict, $content);
        }

        // Restore the original string content
        $pdfstrings = array_reverse($pdfstrings, true);
        foreach ($pdfstrings as $id => $text) {
            // Strings may contain escaped newlines, or literal newlines
            // and we should clean these up before replacing the string
            // back into the content stream; this ensures no strings are
            // split between two lines (every command must be on one line)
            $text = str_replace(
                ["\\\r\n", "\\\r", "\\\n", "\r", "\n"],
                ['', '', '', '\r', '\n'],
                $text
            );

            $content = str_replace('@@@'.$id.'@@@', $text, $content);
        }

        // Restore the original content of any inline images
        $pdfInlineImages = array_reverse($pdfInlineImages, true);
        foreach ($pdfInlineImages as $id => $image) {
            $content = str_replace(
                '^^^'.$id.'^^^',
                "\r\nBI\r\n".$image[0]." ID\r\n".$image[1]." EI\r\n",
                $content
            );
        }

        $content = trim(preg_replace(['/(\r\n){2,}/', '/\r\n +/'], "\r\n", $content));

        return $content;
    }

    /**
     * getSectionsText() now takes an entire, unformatted
     * document stream as a string, cleans it, then filters out
     * commands that aren't needed for text positioning/extraction. It
     * returns an array of unprocessed PDF commands, one command per
     * element.
     *
     * @internal
     */
    public function getSectionsText(?string $content): array
    {
        $sections = [];

        // A cleaned stream has one command on every line, so split the
        // cleaned stream content on \r\n into an array
        $textCleaned = preg_split(
            '/(\r\n|\n|\r)/',
            $this->formatContent($content),
            -1,
            \PREG_SPLIT_NO_EMPTY
        );

        $inTextBlock = false;
        foreach ($textCleaned as $line) {
            $line = trim($line);

            // Skip empty lines
            if ('' === $line) {
                continue;
            }

            // If a 'BT' is encountered, set the $inTextBlock flag
            if (preg_match('/BT$/', $line)) {
                $inTextBlock = true;
                $sections[] = $line;

                // If an 'ET' is encountered, unset the $inTextBlock flag
            } elseif ('ET' == $line) {
                $inTextBlock = false;
                $sections[] = $line;
            } elseif ($inTextBlock) {
                // If we are inside a BT ... ET text block, save all lines
                $sections[] = trim($line);
            } else {
                // Otherwise, if we are outside of a text block, only
                // save specific, necessary lines. Care should be taken
                // to ensure a command being checked for *only* matches
                // that command. For instance, a simple search for 'c'
                // may also match the 'sc' command. See the command
                // list in the formatContent() method above.
                // Add more commands to save here as you find them in
                // weird PDFs!
                if ('q' == $line[-1] || 'Q' == $line[-1]) {
                    // Save and restore graphics state commands
                    $sections[] = $line;
                } elseif (preg_match('/(?<!\w)B[DM]C$/', $line)) {
                    // Begin marked content sequence
                    $sections[] = $line;
                } elseif (preg_match('/(?<!\w)[DM]P$/', $line)) {
                    // Marked content point
                    $sections[] = $line;
                } elseif (preg_match('/(?<!\w)EMC$/', $line)) {
                    // End marked content sequence
                    $sections[] = $line;
                } elseif (preg_match('/(?<!\w)cm$/', $line)) {
                    // Graphics position change commands
                    $sections[] = $line;
                } elseif (preg_match('/(?<!\w)Tf$/', $line)) {
                    // Font change commands
                    $sections[] = $line;
                } elseif (preg_match('/(?<!\w)Do$/', $line)) {
                    // Invoke named XObject command
                    $sections[] = $line;
                }
            }
        }

        return $sections;
    }

    private function getDefaultFont(?Page $page = null): Font
    {
        $fonts = [];
        if (null !== $page) {
            $fonts = $page->getFonts();
        }

        $firstFont = $this->document->getFirstFont();
        if (null !== $firstFont) {
            $fonts[] = $firstFont;
        }

        if (\count($fonts) > 0) {
            return reset($fonts);
        }

        return new Font($this->document, null, null, $this->config);
    }

    /**
     * Decode a '[]TJ' command and attempt to use alternate
     * fonts if the current font results in output that contains
     * Unicode control characters.
     *
     * @internal
     *
     * @param array<int,array<string,string|bool>> $command
     */
    private function getTJUsingFontFallback(Font $font, array $command, ?Page $page = null, float $fontFactor = 4): string
    {
        $orig_text = $font->decodeText($command, $fontFactor);
        $text = $orig_text;

        // If we make this a Config option, we can add a check if it's
        // enabled here.
        if (null !== $page) {
            $font_ids = array_keys($page->getFonts());

            // If the decoded text contains UTF-8 control characters
            // then the font page being used is probably the wrong one.
            // Loop through the rest of the fonts to see if we can get
            // a good decode. Allow x09 to x0d which are whitespace.
            while (preg_match('/[\x00-\x08\x0e-\x1f\x7f]/u', $text) || false !== strpos(bin2hex($text), '00')) {
                // If we're out of font IDs, then give up and use the
                // original string
                if (0 == \count($font_ids)) {
                    return $orig_text;
                }

                // Try the next font ID
                $font = $page->getFont(array_shift($font_ids));
                $text = $font->decodeText($command, $fontFactor);
            }
        }

        return $text;
    }

    /**
     * Expects a string that is a full PDF dictionary object,
     * including the outer enclosing << >> angle brackets
     *
     * @internal
     *
     * @throws InvalidDictionaryObjectException
     */
    public function parseDictionary(string $dictionary): array
    {
        // Normalize whitespace
        $dictionary = preg_replace(['/\r/', '/\n/', '/\s{2,}/'], ' ', trim($dictionary));

        if ('<<' != substr($dictionary, 0, 2)) {
            throw new InvalidDictionaryObjectException('Not a valid dictionary object.');
        }

        $parsed = [];
        $stack = [];
        $currentName = '';
        $arrayTypeNumeric = false;

        // Remove outer layer of dictionary, and split on tokens
        $split = preg_split(
            '/(<<|>>|\[|\]|\/[^\s\/\[\]\(\)<>]*)/',
            trim(preg_replace('/^<<|>>$/', '', $dictionary)),
            -1,
            \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE
        );

        foreach ($split as $token) {
            $token = trim($token);
            switch ($token) {
                case '':
                    break;

                    // Open numeric array
                case '[':
                    $parsed[$currentName] = [];
                    $arrayTypeNumeric = true;

                    // Move up one level in the stack
                    $stack[\count($stack)] = &$parsed;
                    $parsed = &$parsed[$currentName];
                    $currentName = '';
                    break;

                    // Open hashed array
                case '<<':
                    $parsed[$currentName] = [];
                    $arrayTypeNumeric = false;

                    // Move up one level in the stack
                    $stack[\count($stack)] = &$parsed;
                    $parsed = &$parsed[$currentName];
                    $currentName = '';
                    break;

                    // Close numeric array
                case ']':
                    // Revert string type arrays back to a single element
                    if (\is_array($parsed) && 1 == \count($parsed)
                        && isset($parsed[0]) && \is_string($parsed[0])
                        && '' !== $parsed[0] && '/' != $parsed[0][0]) {
                        $parsed = '['.$parsed[0].']';
                    }
                    // Close hashed array
                    // no break
                case '>>':
                    $arrayTypeNumeric = false;

                    // Move down one level in the stack
                    $parsed = &$stack[\count($stack) - 1];
                    unset($stack[\count($stack) - 1]);
                    break;

                default:
                    // If value begins with a slash, then this is a name
                    // Add it to the appropriate array
                    if ('/' == substr($token, 0, 1)) {
                        $currentName = substr($token, 1);
                        if (true == $arrayTypeNumeric) {
                            $parsed[] = $currentName;
                            $currentName = '';
                        }
                    } elseif ('' != $currentName) {
                        if (false == $arrayTypeNumeric) {
                            $parsed[$currentName] = $token;
                        }
                        $currentName = '';
                    } elseif ('' == $currentName) {
                        $parsed[] = $token;
                    }
            }
        }

        return $parsed;
    }

    /**
     * Returns the text content of a PDF as a string. Attempts to add
     * whitespace for spacing and line-breaks where appropriate.
     *
     * getText() leverages getTextArray() to get the content
     * of the document, setting the addPositionWhitespace flag to true
     * so whitespace is inserted in a logical way for reading by
     * humans.
     */
    public function getText(?Page $page = null): string
    {
        $this->addPositionWhitespace = true;
        $result = $this->getTextArray($page);
        $this->addPositionWhitespace = false;

        return implode('', $result).' ';
    }

    /**
     * Returns the text content of a PDF as an array of strings. No
     * extra whitespace is inserted besides what is actually encoded in
     * the PDF text.
     *
     * @throws \Exception
     */
    public function getTextArray(?Page $page = null): array
    {
        $result = [];
        $text = [];

        $marked_stack = [];
        $last_written_position = false;

        $sections = $this->getSectionsText($this->content);
        $current_font = $this->getDefaultFont($page);
        $current_font_size = 1;
        $current_text_leading = 0;

        $current_position = ['x' => false, 'y' => false];
        $current_position_tm = [
            'a' => 1, 'b' => 0, 'c' => 0,
            'i' => 0, 'j' => 1, 'k' => 0,
            'x' => 0, 'y' => 0, 'z' => 1,
        ];
        $current_position_td = ['x' => 0, 'y' => 0];
        $current_position_cm = [
            'a' => 1, 'b' => 0, 'c' => 0,
            'i' => 0, 'j' => 1, 'k' => 0,
            'x' => 0, 'y' => 0, 'z' => 1,
        ];

        $clipped_font = [];
        $clipped_position_cm = [];

        self::$recursionStack[] = $this->getUniqueId();

        foreach ($sections as $section) {
            $commands = $this->getCommandsText($section);
            foreach ($commands as $command) {
                switch ($command[self::OPERATOR]) {
                    // Begin text object
                    case 'BT':
                        // Reset text positioning matrices
                        $current_position_tm = [
                            'a' => 1, 'b' => 0, 'c' => 0,
                            'i' => 0, 'j' => 1, 'k' => 0,
                            'x' => 0, 'y' => 0, 'z' => 1,
                        ];
                        $current_position_td = ['x' => 0, 'y' => 0];
                        $current_text_leading = 0;
                        break;

                        // Begin marked content sequence with property list
                    case 'BDC':
                        if (preg_match('/(<<.*>>)$/', $command[self::COMMAND], $match)) {
                            $dict = $this->parseDictionary($match[1]);

                            // Check for ActualText block
                            if (isset($dict['ActualText']) && \is_string($dict['ActualText']) && '' !== $dict['ActualText']) {
                                if ('[' == $dict['ActualText'][0]) {
                                    // Simulate a 'TJ' command on the stack
                                    $marked_stack[] = [
                                        'ActualText' => $this->getCommandsText($dict['ActualText'].'TJ')[0],
                                    ];
                                } elseif ('<' == $dict['ActualText'][0] || '(' == $dict['ActualText'][0]) {
                                    // Simulate a 'Tj' command on the stack
                                    $marked_stack[] = [
                                        'ActualText' => $this->getCommandsText($dict['ActualText'].'Tj')[0],
                                    ];
                                }
                            }
                        }
                        break;

                        // Begin marked content sequence
                    case 'BMC':
                        if ('ReversedChars' == $command[self::COMMAND]) {
                            // Upon encountering a ReversedChars command,
                            // add the characters we've built up so far to
                            // the result array
                            $result = array_merge($result, $text);

                            // Start a fresh $text array that will contain
                            // reversed characters
                            $text = [];

                            // Add the reversed text flag to the stack
                            $marked_stack[] = ['ReversedChars' => true];
                        }
                        break;

                        // set graphics position matrix
                    case 'cm':
                        $args = preg_split('/\s+/s', $command[self::COMMAND]);
                        $current_position_cm = [
                            'a' => (float) $args[0], 'b' => (float) $args[1], 'c' => 0,
                            'i' => (float) $args[2], 'j' => (float) $args[3], 'k' => 0,
                            'x' => (float) $args[4], 'y' => (float) $args[5], 'z' => 1,
                        ];
                        break;

                    case 'Do':
                        if (is_null($page)) {
                            break;
                        }

                        $args = preg_split('/\s/s', $command[self::COMMAND]);
                        $id = trim(array_pop($args), '/ ');
                        $xobject = $page->getXObject($id);

                        // Check we got a PDFObject back.
                        if (!$xobject instanceof self) {
                            break;
                        }

                        // If the PDFObject is an Image or a Form, do nothing as
                        // neither of these XObject types are text.
                        if ($xobject instanceof Image || $xobject instanceof Form) {
                            break;
                        }

                        // Check this is not a circular reference.
                        if (!\in_array($xobject->getUniqueId(), self::$recursionStack, true)) {
                            $text[] = $xobject->getText($page);
                        }
                        break;

                        // Marked content point with (DP) & without (MP) property list
                    case 'DP':
                    case 'MP':
                        break;

                        // End text object
                    case 'ET':
                        break;

                        // Store current selected font and graphics matrix
                    case 'q':
                        $clipped_font[] = [$current_font, $current_font_size];
                        $clipped_position_cm[] = $current_position_cm;
                        break;

                        // Restore previous selected font and graphics matrix
                    case 'Q':
                        list($current_font, $current_font_size) = array_pop($clipped_font);
                        $current_position_cm = array_pop($clipped_position_cm);
                        break;

                        // End marked content sequence
                    case 'EMC':
                        $data = false;
                        if (\count($marked_stack)) {
                            $marked = array_pop($marked_stack);
                            $action = key($marked);
                            $data = $marked[$action];

                            switch ($action) {
                                // If we are in ReversedChars mode...
                                case 'ReversedChars':
                                    // Reverse the characters we've built up so far
                                    foreach ($text as $key => $t) {
                                        $text[$key] = implode('', array_reverse(
                                            mb_str_split($t, 1, mb_internal_encoding())
                                        ));
                                    }

                                    // Add these characters to the result array
                                    $result = array_merge($result, $text);

                                    // Start a fresh $text array that will contain
                                    // non-reversed characters
                                    $text = [];
                                    break;

                                case 'ActualText':
                                    // Use the content of the ActualText as a command
                                    $command = $data;
                                    break;
                            }
                        }

                        // If this EMC command has been transformed into a 'Tj'
                        // or 'TJ' command because of being ActualText, then bypass
                        // the break to proceed to the writing section below.
                        if ('Tj' != $command[self::OPERATOR] && 'TJ' != $command[self::OPERATOR]) {
                            break;
                        }

                        // no break
                    case "'":
                    case '"':
                        if ("'" == $command[self::OPERATOR] || '"' == $command[self::OPERATOR]) {
                            // Move to next line and write text
                            $current_position['x'] = 0;
                            $current_position_td['x'] = 0;
                            $current_position_td['y'] += $current_text_leading;
                        }
                        // no break
                    case 'Tj':
                        $command[self::COMMAND] = [$command];
                        // no break
                    case 'TJ':
                        // Check the marked content stack for flags
                        $actual_text = false;
                        $reverse_text = false;
                        foreach ($marked_stack as $marked) {
                            if (isset($marked['ActualText'])) {
                                $actual_text = true;
                            }
                            if (isset($marked['ReversedChars'])) {
                                $reverse_text = true;
                            }
                        }

                        // Account for text position ONLY just before we write text
                        if (false === $actual_text && \is_array($last_written_position)) {
                            // If $last_written_position is an array, that
                            // means we have stored text position coordinates
                            // for placing an ActualText
                            $currentX = $last_written_position[0];
                            $currentY = $last_written_position[1];
                            $last_written_position = false;
                        } else {
                            $currentX = $current_position_cm['x'] + $current_position_tm['x'] + $current_position_td['x'];
                            $currentY = $current_position_cm['y'] + $current_position_tm['y'] + $current_position_td['y'];
                        }
                        $whiteSpace = '';

                        $factorX = -$current_font_size * $current_position_tm['a'] - $current_font_size * $current_position_tm['i'];
                        $factorY = $current_font_size * $current_position_tm['b'] + $current_font_size * $current_position_tm['j'];

                        if (true === $this->addPositionWhitespace && false !== $current_position['x']) {
                            $curY = $currentY - $current_position['y'];
                            if (abs($curY) >= abs($factorY) / 4) {
                                $whiteSpace = "\n";
                            } else {
                                if (true === $reverse_text) {
                                    $curX = $current_position['x'] - $currentX;
                                } else {
                                    $curX = $currentX - $current_position['x'];
                                }

                                // In abs($factorX * 7) below, the 7 is chosen arbitrarily
                                // as the number of apparent "spaces" in a document we
                                // would need before considering them a "tab". In the
                                // future, we might offer this value to users as a config
                                // option.
                                if ($curX >= abs($factorX * 7)) {
                                    $whiteSpace = "\t";
                                } elseif ($curX >= abs($factorX * 2)) {
                                    $whiteSpace = ' ';
                                }
                            }
                        }

                        $newtext = $this->getTJUsingFontFallback(
                            $current_font,
                            $command[self::COMMAND],
                            $page,
                            $factorX
                        );

                        // If there is no ActualText pending then write
                        if (false === $actual_text) {
                            $newtext = str_replace(["\r", "\n"], '', $newtext);
                            if (false !== $reverse_text) {
                                // If we are in ReversedChars mode, add the whitespace last
                                $text[] = preg_replace('/  $/', ' ', $newtext.$whiteSpace);
                            } else {
                                // Otherwise add the whitespace first
                                if (' ' === $whiteSpace && isset($text[\count($text) - 1])) {
                                    $text[\count($text) - 1] = preg_replace('/ $/', '', $text[\count($text) - 1]);
                                }
                                $text[] = preg_replace('/^[ \t]{2}/', ' ', $whiteSpace.$newtext);
                            }

                            // Record the position of this inserted text for comparison
                            // with the next text block.
                            // Provide a 'fudge' factor guess on how wide this text block
                            // is based on the number of characters. This helps limit the
                            // number of tabs inserted, but isn't perfect.
                            $factor = $factorX / 2;
                            $current_position = [
                                'x' => $currentX - mb_strlen($newtext) * $factor,
                                'y' => $currentY,
                            ];
                        } elseif (false === $last_written_position) {
                            // If there is an ActualText in the pipeline
                            // store the position this undisplayed text
                            // *would* have been written to, so the
                            // ActualText is displayed in the right spot
                            $last_written_position = [$currentX, $currentY];
                            $current_position['x'] = $currentX;
                        }
                        break;

                        // move to start of next line
                    case 'T*':
                        $current_position['x'] = 0;
                        $current_position_td['x'] = 0;
                        $current_position_td['y'] += $current_text_leading;
                        break;

                        // set character spacing
                    case 'Tc':
                        break;

                        // move text current point and set leading
                    case 'Td':
                    case 'TD':
                        // move text current point
                        $args = preg_split('/\s+/s', $command[self::COMMAND]);
                        $y = (float) array_pop($args);
                        $x = (float) array_pop($args);

                        if ('TD' == $command[self::OPERATOR]) {
                            $current_text_leading = -$y * $current_position_tm['b'] - $y * $current_position_tm['j'];
                        }

                        $current_position_td = [
                            'x' => $current_position_td['x'] + $x * $current_position_tm['a'] + $x * $current_position_tm['i'],
                            'y' => $current_position_td['y'] + $y * $current_position_tm['b'] + $y * $current_position_tm['j'],
                        ];
                        break;

                    case 'Tf':
                        $args = preg_split('/\s/s', $command[self::COMMAND]);
                        $size = (float) array_pop($args);
                        $id = trim(array_pop($args), '/');
                        if (null !== $page) {
                            $new_font = $page->getFont($id);
                            // If an invalid font ID is given, do not update the font.
                            // This should theoretically never happen, as the PDF spec states for the Tf operator:
                            // "The specified font value shall match a resource name in the Font entry of the default resource dictionary"
                            // (https://www.adobe.com/content/dam/acom/en/devnet/pdf/pdfs/PDF32000_2008.pdf, page 435)
                            // But we want to make sure that malformed PDFs do not simply crash.
                            if (null !== $new_font) {
                                $current_font = $new_font;
                                $current_font_size = $size;
                            }
                        }
                        break;

                        // set leading
                    case 'TL':
                        $y = (float) $command[self::COMMAND];
                        $current_text_leading = -$y * $current_position_tm['b'] + -$y * $current_position_tm['j'];
                        break;

                        // set text position matrix
                    case 'Tm':
                        $args = preg_split('/\s+/s', $command[self::COMMAND]);
                        $current_position_tm = [
                            'a' => (float) $args[0], 'b' => (float) $args[1], 'c' => 0,
                            'i' => (float) $args[2], 'j' => (float) $args[3], 'k' => 0,
                            'x' => (float) $args[4], 'y' => (float) $args[5], 'z' => 1,
                        ];
                        break;

                        // set text rendering mode
                    case 'Ts':
                        break;

                        // set super/subscripting text rise
                    case 'Ts':
                        break;

                        // set word spacing
                    case 'Tw':
                        break;

                        // set horizontal scaling
                    case 'Tz':
                        break;

                    default:
                }
            }
        }

        $result = array_merge($result, $text);

        return $result;
    }

    /**
     * getCommandsText() expects the content of $text_part to be an
     * already formatted, single-line command from a document stream.
     * The companion function getSectionsText() returns a document
     * stream as an array of single commands for just this purpose.
     * Because of this, the argument $offset is no longer used, and
     * may be removed in a future PdfParser release.
     *
     * A better name for this function would be getCommandText()
     * since it now always works on just one command.
     */
    public function getCommandsText(string $text_part, int &$offset = 0): array
    {
        $commands = $matches = [];

        preg_match('/^(([\/\[\(<])?.*)(?<!\w)([a-z01\'\"*]+)$/i', $text_part, $matches);

        // If no valid command is detected, return an empty array
        if (!isset($matches[1]) || !isset($matches[2]) || !isset($matches[3])) {
            return [];
        }

        $type = $matches[2];
        $operator = $matches[3];
        $command = trim($matches[1]);

        if ('TJ' == $operator) {
            $subcommand = [];
            $command = trim($command, '[]');
            do {
                $oldCommand = $command;

                // Search for parentheses string () format
                if (preg_match('/^ *\((.*?)(?<![^\\\\]\\\\)\) *(-?[\d.]+)?/', $command, $tjmatch)) {
                    $subcommand[] = [
                        self::TYPE => '(',
                        self::OPERATOR => 'TJ',
                        self::COMMAND => $tjmatch[1],
                    ];
                    if (isset($tjmatch[2]) && trim($tjmatch[2])) {
                        $subcommand[] = [
                            self::TYPE => 'n',
                            self::OPERATOR => '',
                            self::COMMAND => $tjmatch[2],
                        ];
                    }
                    $command = substr($command, \strlen($tjmatch[0]));
                }

                // Search for hexadecimal <> format
                if (preg_match('/^ *<([0-9a-f\s]*)> *(-?[\d.]+)?/i', $command, $tjmatch)) {
                    $tjmatch[1] = preg_replace('/\s/', '', $tjmatch[1]);
                    $subcommand[] = [
                        self::TYPE => '<',
                        self::OPERATOR => 'TJ',
                        self::COMMAND => $tjmatch[1],
                    ];
                    if (isset($tjmatch[2]) && trim($tjmatch[2])) {
                        $subcommand[] = [
                            self::TYPE => 'n',
                            self::OPERATOR => '',
                            self::COMMAND => $tjmatch[2],
                        ];
                    }
                    $command = substr($command, \strlen($tjmatch[0]));
                }
            } while ($command != $oldCommand);

            $command = $subcommand;
        } elseif ('Tj' == $operator || "'" == $operator || '"' == $operator) {
            // Depending on the string type, trim the data of the
            // appropriate delimiters
            if ('(' == $type) {
                // Don't use trim() here since a () string may end with
                // a balanced or escaped right parentheses, and trim()
                // will delete both. Both strings below are valid:
                //   eg. (String())
                //   eg. (String\))
                $command = preg_replace('/^\(|\)$/', '', $command);
            } elseif ('<' == $type) {
                $command = trim($command, '<>');
            }
        } elseif ('/' == $type) {
            $command = substr($command, 1);
        }

        $commands[] = [
            self::TYPE => $type,
            self::OPERATOR => $operator,
            self::COMMAND => $command,
        ];

        return $commands;
    }

    public static function factory(
        Document $document,
        Header $header,
        ?string $content,
        ?Config $config = null
    ): self {
        switch ($header->get('Type')->getContent()) {
            case 'XObject':
                switch ($header->get('Subtype')->getContent()) {
                    case 'Image':
                        return new Image($document, $header, $config->getRetainImageContent() ? $content : null, $config);

                    case 'Form':
                        return new Form($document, $header, $content, $config);
                }

                return new self($document, $header, $content, $config);

            case 'Pages':
                return new Pages($document, $header, $content, $config);

            case 'Page':
                return new Page($document, $header, $content, $config);

            case 'Encoding':
                return new Encoding($document, $header, $content, $config);

            case 'Font':
                $subtype = $header->get('Subtype')->getContent();
                $classname = '\Smalot\PdfParser\Font\Font'.$subtype;

                if (class_exists($classname)) {
                    return new $classname($document, $header, $content, $config);
                }

                return new Font($document, $header, $content, $config);

            default:
                return new self($document, $header, $content, $config);
        }
    }

    /**
     * Returns unique id identifying the object.
     */
    protected function getUniqueId(): string
    {
        return spl_object_hash($this);
    }
}
