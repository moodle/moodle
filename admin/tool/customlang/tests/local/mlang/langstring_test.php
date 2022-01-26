<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * mlang langstring tests.
 *
 * Based on local_amos mlang_langstring tests.
 *
 * @package    tool_customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_customlang\local\mlang;

use advanced_testcase;
use moodle_exception;

/**
 * Langstring tests.
 *
 * @package    tool_customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class langstring_test extends advanced_testcase {

    /**
     * Sanity 1.x string
     * - all variables but $a placeholders must be escaped because the string is eval'ed
     * - all ' and " must be escaped
     * - all single % must be converted into %% for backwards compatibility
     *
     * @dataProvider fix_syntax_data
     * @param string $text the text to test
     * @param int $version the lang package version (1 or 2)
     * @param int|null $fromversion the version to convert (null for none)
     * @param string $expected the expected result
     *
     */
    public function test_fix_syntax(string $text, int $version, ?int $fromversion, string $expected): void {
        $this->assertEquals(langstring::fix_syntax($text, $version, $fromversion), $expected);
    }

    /**
     * Data provider for the test_parse.
     *
     * @return  array
     */
    public function fix_syntax_data() : array {
        return [
            // Syntax sanity v1 strings.
            [
                'No change', 1, null,
                'No change'
            ],
            [
                'Completed 100% of work', 1, null,
                'Completed 100%% of work'
            ],
            [
                'Completed 100%% of work', 1, null,
                'Completed 100%% of work'
            ],
            [
                "Windows\r\nsucks", 1, null,
                "Windows\nsucks"
            ],
            [
                "Linux\nsucks", 1, null,
                "Linux\nsucks"
            ],
            [
                "Mac\rsucks", 1, null,
                "Mac\nsucks"
            ],
            [
                "LINE TABULATION\x0Bnewline", 1, null,
                "LINE TABULATION\nnewline"
            ],
            [
                "FORM FEED\x0Cnewline", 1, null,
                "FORM FEED\nnewline"
            ],
            [
                "END OF TRANSMISSION BLOCK\x17newline", 1, null,
                "END OF TRANSMISSION BLOCK\nnewline"
            ],
            [
                "END OF MEDIUM\x19newline", 1, null,
                "END OF MEDIUM\nnewline"
            ],
            [
                "SUBSTITUTE\x1Anewline", 1, null,
                "SUBSTITUTE\nnewline"
            ],
            [
                "BREAK PERMITTED HERE\xC2\x82newline", 1, null,
                "BREAK PERMITTED HERE\nnewline"
            ],
            [
                "NEXT LINE\xC2\x85newline", 1, null,
                "NEXT LINE\nnewline"
            ],
            [
                "START OF STRING\xC2\x98newline", 1, null,
                "START OF STRING\nnewline"
            ],
            [
                "STRING TERMINATOR\xC2\x9Cnewline", 1, null,
                "STRING TERMINATOR\nnewline"
            ],
            [
                "Unicode Zl\xE2\x80\xA8newline", 1, null,
                "Unicode Zl\nnewline"
            ],
            [
                "Unicode Zp\xE2\x80\xA9newline", 1, null,
                "Unicode Zp\nnewline"
            ],
            [
                "Empty\n\n\n\n\n\nlines", 1, null,
                "Empty\n\nlines"
            ],
            [
                "Trailing   \n  whitespace \t \nat \nmultilines  ", 1, null,
                "Trailing\n  whitespace\nat\nmultilines"
            ],
            [
                'Escape $variable names', 1, null,
                'Escape \$variable names'
            ],
            [
                'Escape $alike names', 1, null,
                'Escape \$alike names'
            ],
            [
                'String $a placeholder', 1, null,
                'String $a placeholder'
            ],
            [
                'Escaped \$a', 1, null,
                'Escaped \$a'
            ],
            [
                'Wrapped {$a}', 1, null,
                'Wrapped {$a}'
            ],
            [
                'Trailing $a', 1, null,
                'Trailing $a'
            ],
            [
                '$a leading', 1, null,
                '$a leading'
            ],
            [
                'Hit $a-times', 1, null,
                'Hit $a-times'
            ], // This is placeholder.
            [
                'This is $a_book', 1, null,
                'This is \$a_book'
            ], // This is not a place holder.
            [
                'Bye $a, ttyl', 1, null,
                'Bye $a, ttyl'
            ],
            [
                'Object $a->foo placeholder', 1, null,
                'Object $a->foo placeholder'
            ],
            [
                'Trailing $a->bar', 1, null,
                'Trailing $a->bar'
            ],
            [
                '<strong>AMOS</strong>', 1, null,
                '<strong>AMOS</strong>'
            ],
            [
                '<a href="http://localhost">AMOS</a>', 1, null,
                '<a href=\"http://localhost\">AMOS</a>'
            ],
            [
                '<a href=\"http://localhost\">AMOS</a>', 1, null,
                '<a href=\"http://localhost\">AMOS</a>'
            ],
            [
                "'Murder!', she wrote", 1, null,
                "'Murder!', she wrote"
            ], // Will be escaped by var_export().
            [
                "\t  Trim Hunter  \t\t", 1, null,
                'Trim Hunter'
            ],
            [
                'Delete role "$a->role"?', 1, null,
                'Delete role \"$a->role\"?'
            ],
            [
                'Delete role \"$a->role\"?', 1, null,
                'Delete role \"$a->role\"?'
            ],
            [
                "Delete ASCII\0 NULL control character", 1, null,
                'Delete ASCII NULL control character'
            ],
            [
                "Delete ASCII\x05 ENQUIRY control character", 1, null,
                'Delete ASCII ENQUIRY control character'
            ],
            [
                "Delete ASCII\x06 ACKNOWLEDGE control character", 1, null,
                'Delete ASCII ACKNOWLEDGE control character'
            ],
            [
                "Delete ASCII\x07 BELL control character", 1, null,
                'Delete ASCII BELL control character'
            ],
            [
                "Delete ASCII\x0E SHIFT OUT control character", 1, null,
                'Delete ASCII SHIFT OUT control character'
            ],
            [
                "Delete ASCII\x0F SHIFT IN control character", 1, null,
                'Delete ASCII SHIFT IN control character'
            ],
            [
                "Delete ASCII\x10 DATA LINK ESCAPE control character", 1, null,
                'Delete ASCII DATA LINK ESCAPE control character'
            ],
            [
                "Delete ASCII\x11 DEVICE CONTROL ONE control character", 1, null,
                'Delete ASCII DEVICE CONTROL ONE control character'
            ],
            [
                "Delete ASCII\x12 DEVICE CONTROL TWO control character", 1, null,
                'Delete ASCII DEVICE CONTROL TWO control character'
            ],
            [
                "Delete ASCII\x13 DEVICE CONTROL THREE control character", 1, null,
                'Delete ASCII DEVICE CONTROL THREE control character'
            ],
            [
                "Delete ASCII\x14 DEVICE CONTROL FOUR control character", 1, null,
                'Delete ASCII DEVICE CONTROL FOUR control character'
            ],
            [
                "Delete ASCII\x15 NEGATIVE ACKNOWLEDGE control character", 1, null,
                'Delete ASCII NEGATIVE ACKNOWLEDGE control character'
            ],
            [
                "Delete ASCII\x16 SYNCHRONOUS IDLE control character", 1, null,
                'Delete ASCII SYNCHRONOUS IDLE control character'
            ],
            [
                "Delete ASCII\x1B ESCAPE control character", 1, null,
                'Delete ASCII ESCAPE control character'
            ],
            [
                "Delete ASCII\x7F DELETE control character", 1, null,
                'Delete ASCII DELETE control character'
            ],
            [
                "Delete ISO 8859\xC2\x80 PADDING CHARACTER control character", 1, null,
                'Delete ISO 8859 PADDING CHARACTER control character'
            ],
            [
                "Delete ISO 8859\xC2\x81 HIGH OCTET PRESET control character", 1, null,
                'Delete ISO 8859 HIGH OCTET PRESET control character'
            ],
            [
                "Delete ISO 8859\xC2\x83 NO BREAK HERE control character", 1, null,
                'Delete ISO 8859 NO BREAK HERE control character'
            ],
            [
                "Delete ISO 8859\xC2\x84 INDEX control character", 1, null,
                'Delete ISO 8859 INDEX control character'
            ],
            [
                "Delete ISO 8859\xC2\x86 START OF SELECTED AREA control character", 1, null,
                'Delete ISO 8859 START OF SELECTED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x87 END OF SELECTED AREA control character", 1, null,
                'Delete ISO 8859 END OF SELECTED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x88 CHARACTER TABULATION SET control character", 1, null,
                'Delete ISO 8859 CHARACTER TABULATION SET control character'
            ],
            [
                "Delete ISO 8859\xC2\x89 CHARACTER TABULATION WITH JUSTIFICATION control character", 1, null,
                'Delete ISO 8859 CHARACTER TABULATION WITH JUSTIFICATION control character'
            ],
            [
                "Delete ISO 8859\xC2\x8A LINE TABULATION SET control character", 1, null,
                'Delete ISO 8859 LINE TABULATION SET control character'
            ],
            [
                "Delete ISO 8859\xC2\x8B PARTIAL LINE FORWARD control character", 1, null,
                'Delete ISO 8859 PARTIAL LINE FORWARD control character'
            ],
            [
                "Delete ISO 8859\xC2\x8C PARTIAL LINE BACKWARD control character", 1, null,
                'Delete ISO 8859 PARTIAL LINE BACKWARD control character'
            ],
            [
                "Delete ISO 8859\xC2\x8D REVERSE LINE FEED control character", 1, null,
                'Delete ISO 8859 REVERSE LINE FEED control character'
            ],
            [
                "Delete ISO 8859\xC2\x8E SINGLE SHIFT TWO control character", 1, null,
                'Delete ISO 8859 SINGLE SHIFT TWO control character'
            ],
            [
                "Delete ISO 8859\xC2\x8F SINGLE SHIFT THREE control character", 1, null,
                'Delete ISO 8859 SINGLE SHIFT THREE control character'
            ],
            [
                "Delete ISO 8859\xC2\x90 DEVICE CONTROL STRING control character", 1, null,
                'Delete ISO 8859 DEVICE CONTROL STRING control character'
            ],
            [
                "Delete ISO 8859\xC2\x91 PRIVATE USE ONE control character", 1, null,
                'Delete ISO 8859 PRIVATE USE ONE control character'
            ],
            [
                "Delete ISO 8859\xC2\x92 PRIVATE USE TWO control character", 1, null,
                'Delete ISO 8859 PRIVATE USE TWO control character'
            ],
            [
                "Delete ISO 8859\xC2\x93 SET TRANSMIT STATE control character", 1, null,
                'Delete ISO 8859 SET TRANSMIT STATE control character'
            ],
            [
                "Delete ISO 8859\xC2\x95 MESSAGE WAITING control character", 1, null,
                'Delete ISO 8859 MESSAGE WAITING control character'
            ],
            [
                "Delete ISO 8859\xC2\x96 START OF GUARDED AREA control character", 1, null,
                'Delete ISO 8859 START OF GUARDED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x97 END OF GUARDED AREA control character", 1, null,
                'Delete ISO 8859 END OF GUARDED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x99 SINGLE GRAPHIC CHARACTER INTRODUCER control character", 1, null,
                'Delete ISO 8859 SINGLE GRAPHIC CHARACTER INTRODUCER control character'
            ],
            [
                "Delete ISO 8859\xC2\x9A SINGLE CHARACTER INTRODUCER control character", 1, null,
                'Delete ISO 8859 SINGLE CHARACTER INTRODUCER control character'
            ],
            [
                "Delete ISO 8859\xC2\x9B CONTROL SEQUENCE INTRODUCER control character", 1, null,
                'Delete ISO 8859 CONTROL SEQUENCE INTRODUCER control character'
            ],
            [
                "Delete ISO 8859\xC2\x9D OPERATING SYSTEM COMMAND control character", 1, null,
                'Delete ISO 8859 OPERATING SYSTEM COMMAND control character'
            ],
            [
                "Delete ISO 8859\xC2\x9E PRIVACY MESSAGE control character", 1, null,
                'Delete ISO 8859 PRIVACY MESSAGE control character'
            ],
            [
                "Delete ISO 8859\xC2\x9F APPLICATION PROGRAM COMMAND control character", 1, null,
                'Delete ISO 8859 APPLICATION PROGRAM COMMAND control character'
            ],
            [
                "Delete Unicode\xE2\x80\x8B ZERO WIDTH SPACE control character", 1, null,
                'Delete Unicode ZERO WIDTH SPACE control character'
            ],
            [
                "Delete Unicode\xEF\xBB\xBF ZERO WIDTH NO-BREAK SPACE control character", 1, null,
                'Delete Unicode ZERO WIDTH NO-BREAK SPACE control character'
            ],
            [
                "Delete Unicode\xEF\xBF\xBD REPLACEMENT CHARACTER control character", 1, null,
                'Delete Unicode REPLACEMENT CHARACTER control character'
            ],
            // Syntax sanity v2 strings.
            [
                'No change', 2, null,
                'No change'
            ],
            [
                'Completed 100% of work', 2, null,
                'Completed 100% of work'
            ],
            [
                '%%%% HEADER %%%%', 2, null,
                '%%%% HEADER %%%%'
            ], // Was not possible before.
            [
                "Windows\r\nsucks", 2, null,
                "Windows\nsucks"
            ],
            [
                "Linux\nsucks", 2, null,
                "Linux\nsucks"
            ],
            [
                "Mac\rsucks", 2, null,
                "Mac\nsucks"
            ],
            [
                "LINE TABULATION\x0Bnewline", 2, null,
                "LINE TABULATION\nnewline"
            ],
            [
                "FORM FEED\x0Cnewline", 2, null,
                "FORM FEED\nnewline"
            ],
            [
                "END OF TRANSMISSION BLOCK\x17newline", 2, null,
                "END OF TRANSMISSION BLOCK\nnewline"
            ],
            [
                "END OF MEDIUM\x19newline", 2, null,
                "END OF MEDIUM\nnewline"
            ],
            [
                "SUBSTITUTE\x1Anewline", 2, null,
                "SUBSTITUTE\nnewline"
            ],
            [
                "BREAK PERMITTED HERE\xC2\x82newline", 2, null,
                "BREAK PERMITTED HERE\nnewline"
            ],
            [
                "NEXT LINE\xC2\x85newline", 2, null,
                "NEXT LINE\nnewline"
            ],
            [
                "START OF STRING\xC2\x98newline", 2, null,
                "START OF STRING\nnewline"
            ],
            [
                "STRING TERMINATOR\xC2\x9Cnewline", 2, null,
                "STRING TERMINATOR\nnewline"
            ],
            [
                "Unicode Zl\xE2\x80\xA8newline", 2, null,
                "Unicode Zl\nnewline"
            ],
            [
                "Unicode Zp\xE2\x80\xA9newline", 2, null,
                "Unicode Zp\nnewline"
            ],
            [
                "Empty\n\n\n\n\n\nlines", 2, null,
                "Empty\n\n\nlines"
            ], // Now allows up to two empty lines.
            [
                "Trailing   \n  whitespace\t\nat \nmultilines  ", 2, null,
                "Trailing\n  whitespace\nat\nmultilines"
            ],
            [
                'Do not escape $variable names', 2, null,
                'Do not escape $variable names'
            ],
            [
                'Do not escape $alike names', 2, null,
                'Do not escape $alike names'
            ],
            [
                'Not $a placeholder', 2, null,
                'Not $a placeholder'
            ],
            [
                'String {$a} placeholder', 2, null,
                'String {$a} placeholder'
            ],
            [
                'Trailing {$a}', 2, null,
                'Trailing {$a}'
            ],
            [
                '{$a} leading', 2, null,
                '{$a} leading'
            ],
            [
                'Trailing $a', 2, null,
                'Trailing $a'
            ],
            [
                '$a leading', 2, null,
                '$a leading'
            ],
            [
                'Not $a->foo placeholder', 2, null,
                'Not $a->foo placeholder'
            ],
            [
                'Object {$a->foo} placeholder', 2, null,
                'Object {$a->foo} placeholder'
            ],
            [
                'Trailing $a->bar', 2, null,
                'Trailing $a->bar'
            ],
            [
                'Invalid $a-> placeholder', 2, null,
                'Invalid $a-> placeholder'
            ],
            [
                '<strong>AMOS</strong>', 2, null,
                '<strong>AMOS</strong>'
            ],
            [
                "'Murder!', she wrote", 2, null,
                "'Murder!', she wrote"
            ], // Will be escaped by var_export().
            [
                "\t  Trim Hunter  \t\t", 2, null,
                'Trim Hunter'
            ],
            [
                'Delete role "$a->role"?', 2, null,
                'Delete role "$a->role"?'
            ],
            [
                'Delete role \"$a->role\"?', 2, null,
                'Delete role \"$a->role\"?'
            ],
            [
                "Delete ASCII\0 NULL control character", 2, null,
                'Delete ASCII NULL control character'
            ],
            [
                "Delete ASCII\x05 ENQUIRY control character", 2, null,
                'Delete ASCII ENQUIRY control character'
            ],
            [
                "Delete ASCII\x06 ACKNOWLEDGE control character", 2, null,
                'Delete ASCII ACKNOWLEDGE control character'
            ],
            [
                "Delete ASCII\x07 BELL control character", 2, null,
                'Delete ASCII BELL control character'
            ],
            [
                "Delete ASCII\x0E SHIFT OUT control character", 2, null,
                'Delete ASCII SHIFT OUT control character'
            ],
            [
                "Delete ASCII\x0F SHIFT IN control character", 2, null,
                'Delete ASCII SHIFT IN control character'
            ],
            [
                "Delete ASCII\x10 DATA LINK ESCAPE control character", 2, null,
                'Delete ASCII DATA LINK ESCAPE control character'
            ],
            [
                "Delete ASCII\x11 DEVICE CONTROL ONE control character", 2, null,
                'Delete ASCII DEVICE CONTROL ONE control character'
            ],
            [
                "Delete ASCII\x12 DEVICE CONTROL TWO control character", 2, null,
                'Delete ASCII DEVICE CONTROL TWO control character'
            ],
            [
                "Delete ASCII\x13 DEVICE CONTROL THREE control character", 2, null,
                'Delete ASCII DEVICE CONTROL THREE control character'
            ],
            [
                "Delete ASCII\x14 DEVICE CONTROL FOUR control character", 2, null,
                'Delete ASCII DEVICE CONTROL FOUR control character'
            ],
            [
                "Delete ASCII\x15 NEGATIVE ACKNOWLEDGE control character", 2, null,
                'Delete ASCII NEGATIVE ACKNOWLEDGE control character'
            ],
            [
                "Delete ASCII\x16 SYNCHRONOUS IDLE control character", 2, null,
                'Delete ASCII SYNCHRONOUS IDLE control character'
            ],
            [
                "Delete ASCII\x1B ESCAPE control character", 2, null,
                'Delete ASCII ESCAPE control character'
            ],
            [
                "Delete ASCII\x7F DELETE control character", 2, null,
                'Delete ASCII DELETE control character'
            ],
            [
                "Delete ISO 8859\xC2\x80 PADDING CHARACTER control character", 2, null,
                'Delete ISO 8859 PADDING CHARACTER control character'
            ],
            [
                "Delete ISO 8859\xC2\x81 HIGH OCTET PRESET control character", 2, null,
                'Delete ISO 8859 HIGH OCTET PRESET control character'
            ],
            [
                "Delete ISO 8859\xC2\x83 NO BREAK HERE control character", 2, null,
                'Delete ISO 8859 NO BREAK HERE control character'
            ],
            [
                "Delete ISO 8859\xC2\x84 INDEX control character", 2, null,
                'Delete ISO 8859 INDEX control character'
            ],
            [
                "Delete ISO 8859\xC2\x86 START OF SELECTED AREA control character", 2, null,
                'Delete ISO 8859 START OF SELECTED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x87 END OF SELECTED AREA control character", 2, null,
                'Delete ISO 8859 END OF SELECTED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x88 CHARACTER TABULATION SET control character", 2, null,
                'Delete ISO 8859 CHARACTER TABULATION SET control character'
            ],
            [
                "Delete ISO 8859\xC2\x89 CHARACTER TABULATION WITH JUSTIFICATION control character", 2, null,
                'Delete ISO 8859 CHARACTER TABULATION WITH JUSTIFICATION control character'
            ],
            [
                "Delete ISO 8859\xC2\x8A LINE TABULATION SET control character", 2, null,
                'Delete ISO 8859 LINE TABULATION SET control character'
            ],
            [
                "Delete ISO 8859\xC2\x8B PARTIAL LINE FORWARD control character", 2, null,
                'Delete ISO 8859 PARTIAL LINE FORWARD control character'
            ],
            [
                "Delete ISO 8859\xC2\x8C PARTIAL LINE BACKWARD control character", 2, null,
                'Delete ISO 8859 PARTIAL LINE BACKWARD control character'
            ],
            [
                "Delete ISO 8859\xC2\x8D REVERSE LINE FEED control character", 2, null,
                'Delete ISO 8859 REVERSE LINE FEED control character'
            ],
            [
                "Delete ISO 8859\xC2\x8E SINGLE SHIFT TWO control character", 2, null,
                'Delete ISO 8859 SINGLE SHIFT TWO control character'
            ],
            [
                "Delete ISO 8859\xC2\x8F SINGLE SHIFT THREE control character", 2, null,
                'Delete ISO 8859 SINGLE SHIFT THREE control character'
            ],
            [
                "Delete ISO 8859\xC2\x90 DEVICE CONTROL STRING control character", 2, null,
                'Delete ISO 8859 DEVICE CONTROL STRING control character'
            ],
            [
                "Delete ISO 8859\xC2\x91 PRIVATE USE ONE control character", 2, null,
                'Delete ISO 8859 PRIVATE USE ONE control character'
            ],
            [
                "Delete ISO 8859\xC2\x92 PRIVATE USE TWO control character", 2, null,
                'Delete ISO 8859 PRIVATE USE TWO control character'
            ],
            [
                "Delete ISO 8859\xC2\x93 SET TRANSMIT STATE control character", 2, null,
                'Delete ISO 8859 SET TRANSMIT STATE control character'
            ],
            [
                "Delete ISO 8859\xC2\x95 MESSAGE WAITING control character", 2, null,
                'Delete ISO 8859 MESSAGE WAITING control character'
            ],
            [
                "Delete ISO 8859\xC2\x96 START OF GUARDED AREA control character", 2, null,
                'Delete ISO 8859 START OF GUARDED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x97 END OF GUARDED AREA control character", 2, null,
                'Delete ISO 8859 END OF GUARDED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x99 SINGLE GRAPHIC CHARACTER INTRODUCER control character", 2, null,
                'Delete ISO 8859 SINGLE GRAPHIC CHARACTER INTRODUCER control character'
            ],
            [
                "Delete ISO 8859\xC2\x9A SINGLE CHARACTER INTRODUCER control character", 2, null,
                'Delete ISO 8859 SINGLE CHARACTER INTRODUCER control character'
            ],
            [
                "Delete ISO 8859\xC2\x9B CONTROL SEQUENCE INTRODUCER control character", 2, null,
                'Delete ISO 8859 CONTROL SEQUENCE INTRODUCER control character'
            ],
            [
                "Delete ISO 8859\xC2\x9D OPERATING SYSTEM COMMAND control character", 2, null,
                'Delete ISO 8859 OPERATING SYSTEM COMMAND control character'
            ],
            [
                "Delete ISO 8859\xC2\x9E PRIVACY MESSAGE control character", 2, null,
                'Delete ISO 8859 PRIVACY MESSAGE control character'
            ],
            [
                "Delete ISO 8859\xC2\x9F APPLICATION PROGRAM COMMAND control character", 2, null,
                'Delete ISO 8859 APPLICATION PROGRAM COMMAND control character'
            ],
            [
                "Delete Unicode\xE2\x80\x8B ZERO WIDTH SPACE control character", 2, null,
                'Delete Unicode ZERO WIDTH SPACE control character'
            ],
            [
                "Delete Unicode\xEF\xBB\xBF ZERO WIDTH NO-BREAK SPACE control character", 2, null,
                'Delete Unicode ZERO WIDTH NO-BREAK SPACE control character'
            ],
            [
                "Delete Unicode\xEF\xBF\xBD REPLACEMENT CHARACTER control character", 2, null,
                'Delete Unicode REPLACEMENT CHARACTER control character'
            ],
            // Conterting from v1 to v2.
            [
                'No change', 2, 1,
                'No change'
            ],
            [
                'Completed 100% of work', 2, 1,
                'Completed 100% of work'
            ],
            [
                'Completed 100%% of work', 2, 1,
                'Completed 100% of work'
            ],
            [
                "Windows\r\nsucks", 2, 1,
                "Windows\nsucks"
            ],
            [
                "Linux\nsucks", 2, 1,
                "Linux\nsucks"
            ],
            [
                "Mac\rsucks", 2, 1,
                "Mac\nsucks"
            ],
            [
                "LINE TABULATION\x0Bnewline", 2, 1,
                "LINE TABULATION\nnewline"
            ],
            [
                "FORM FEED\x0Cnewline", 2, 1,
                "FORM FEED\nnewline"
            ],
            [
                "END OF TRANSMISSION BLOCK\x17newline", 2, 1,
                "END OF TRANSMISSION BLOCK\nnewline"
            ],
            [
                "END OF MEDIUM\x19newline", 2, 1,
                "END OF MEDIUM\nnewline"
            ],
            [
                "SUBSTITUTE\x1Anewline", 2, 1,
                "SUBSTITUTE\nnewline"
            ],
            [
                "BREAK PERMITTED HERE\xC2\x82newline", 2, 1,
                "BREAK PERMITTED HERE\nnewline"
            ],
            [
                "NEXT LINE\xC2\x85newline", 2, 1,
                "NEXT LINE\nnewline"
            ],
            [
                "START OF STRING\xC2\x98newline", 2, 1,
                "START OF STRING\nnewline"
            ],
            [
                "STRING TERMINATOR\xC2\x9Cnewline", 2, 1,
                "STRING TERMINATOR\nnewline"
            ],
            [
                "Unicode Zl\xE2\x80\xA8newline", 2, 1,
                "Unicode Zl\nnewline"
            ],
            [
                "Unicode Zp\xE2\x80\xA9newline", 2, 1,
                "Unicode Zp\nnewline"
            ],
            [
                "Empty\n\n\n\n\n\nlines", 2, 1,
                "Empty\n\n\nlines"
            ],
            [
                "Trailing   \n  whitespace\t\nat \nmultilines  ", 2, 1,
                "Trailing\n  whitespace\nat\nmultilines"
            ],
            [
                'Do not escape $variable names', 2, 1,
                'Do not escape $variable names'
            ],
            [
                'Do not escape \$variable names', 2, 1,
                'Do not escape $variable names'
            ],
            [
                'Do not escape $alike names', 2, 1,
                'Do not escape $alike names'
            ],
            [
                'Do not escape \$alike names', 2, 1,
                'Do not escape $alike names'
            ],
            [
                'Do not escape \$a names', 2, 1,
                'Do not escape $a names'
            ],
            [
                'String $a placeholder', 2, 1,
                'String {$a} placeholder'
            ],
            [
                'String {$a} placeholder', 2, 1,
                'String {$a} placeholder'
            ],
            [
                'Trailing $a', 2, 1,
                'Trailing {$a}'
            ],
            [
                '$a leading', 2, 1,
                '{$a} leading'
            ],
            [
                '$a', 2, 1,
                '{$a}'
            ],
            [
                '$a->single', 2, 1,
                '{$a->single}'
            ],
            [
                'Trailing $a->foobar', 2, 1,
                'Trailing {$a->foobar}'
            ],
            [
                'Trailing {$a}', 2, 1,
                'Trailing {$a}'
            ],
            [
                'Hit $a-times', 2, 1,
                'Hit {$a}-times'
            ],
            [
                'This is $a_book', 2, 1,
                'This is $a_book'
            ],
            [
                'Object $a->foo placeholder', 2, 1,
                'Object {$a->foo} placeholder'
            ],
            [
                'Object {$a->foo} placeholder', 2, 1,
                'Object {$a->foo} placeholder'
            ],
            [
                'Trailing $a->bar', 2, 1,
                'Trailing {$a->bar}'
            ],
            [
                'Trailing {$a->bar}', 2, 1,
                'Trailing {$a->bar}'
            ],
            [
                'Invalid $a-> placeholder', 2, 1,
                'Invalid {$a}-> placeholder'
                ], // Weird but BC.
            [
                '<strong>AMOS</strong>', 2, 1,
                '<strong>AMOS</strong>'
            ],
            [
                "'Murder!', she wrote", 2, 1,
                "'Murder!', she wrote"
            ], // Will be escaped by var_export().
            [
                "\'Murder!\', she wrote", 2, 1,
                "'Murder!', she wrote"
            ], // Will be escaped by var_export().
            [
                "\t  Trim Hunter  \t\t", 2, 1,
                'Trim Hunter'
            ],
            [
                'Delete role "$a->role"?', 2, 1,
                'Delete role "{$a->role}"?'
            ],
            [
                'Delete role \"$a->role\"?', 2, 1,
                'Delete role "{$a->role}"?'
            ],
            [
                'See &#36;CFG->foo', 2, 1,
                'See $CFG->foo'
            ],
            [
                "Delete ASCII\0 NULL control character", 2, 1,
                'Delete ASCII NULL control character'
            ],
            [
                "Delete ASCII\x05 ENQUIRY control character", 2, 1,
                'Delete ASCII ENQUIRY control character'
            ],
            [
                "Delete ASCII\x06 ACKNOWLEDGE control character", 2, 1,
                'Delete ASCII ACKNOWLEDGE control character'
            ],
            [
                "Delete ASCII\x07 BELL control character", 2, 1,
                'Delete ASCII BELL control character'
            ],
            [
                "Delete ASCII\x0E SHIFT OUT control character", 2, 1,
                'Delete ASCII SHIFT OUT control character'
            ],
            [
                "Delete ASCII\x0F SHIFT IN control character", 2, 1,
                'Delete ASCII SHIFT IN control character'
            ],
            [
                "Delete ASCII\x10 DATA LINK ESCAPE control character", 2, 1,
                'Delete ASCII DATA LINK ESCAPE control character'
            ],
            [
                "Delete ASCII\x11 DEVICE CONTROL ONE control character", 2, 1,
                'Delete ASCII DEVICE CONTROL ONE control character'
            ],
            [
                "Delete ASCII\x12 DEVICE CONTROL TWO control character", 2, 1,
                'Delete ASCII DEVICE CONTROL TWO control character'
            ],
            [
                "Delete ASCII\x13 DEVICE CONTROL THREE control character", 2, 1,
                'Delete ASCII DEVICE CONTROL THREE control character'
            ],
            [
                "Delete ASCII\x14 DEVICE CONTROL FOUR control character", 2, 1,
                'Delete ASCII DEVICE CONTROL FOUR control character'
            ],
            [
                "Delete ASCII\x15 NEGATIVE ACKNOWLEDGE control character", 2, 1,
                'Delete ASCII NEGATIVE ACKNOWLEDGE control character'
            ],
            [
                "Delete ASCII\x16 SYNCHRONOUS IDLE control character", 2, 1,
                'Delete ASCII SYNCHRONOUS IDLE control character'
            ],
            [
                "Delete ASCII\x1B ESCAPE control character", 2, 1,
                'Delete ASCII ESCAPE control character'
            ],
            [
                "Delete ASCII\x7F DELETE control character", 2, 1,
                'Delete ASCII DELETE control character'
            ],
            [
                "Delete ISO 8859\xC2\x80 PADDING CHARACTER control character", 2, 1,
                'Delete ISO 8859 PADDING CHARACTER control character'
            ],
            [
                "Delete ISO 8859\xC2\x81 HIGH OCTET PRESET control character", 2, 1,
                'Delete ISO 8859 HIGH OCTET PRESET control character'
            ],
            [
                "Delete ISO 8859\xC2\x83 NO BREAK HERE control character", 2, 1,
                'Delete ISO 8859 NO BREAK HERE control character'
            ],
            [
                "Delete ISO 8859\xC2\x84 INDEX control character", 2, 1,
                'Delete ISO 8859 INDEX control character'
            ],
            [
                "Delete ISO 8859\xC2\x86 START OF SELECTED AREA control character", 2, 1,
                'Delete ISO 8859 START OF SELECTED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x87 END OF SELECTED AREA control character", 2, 1,
                'Delete ISO 8859 END OF SELECTED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x88 CHARACTER TABULATION SET control character", 2, 1,
                'Delete ISO 8859 CHARACTER TABULATION SET control character'
            ],
            [
                "Delete ISO 8859\xC2\x89 CHARACTER TABULATION WITH JUSTIFICATION control character", 2, 1,
                'Delete ISO 8859 CHARACTER TABULATION WITH JUSTIFICATION control character'
            ],
            [
                "Delete ISO 8859\xC2\x8A LINE TABULATION SET control character", 2, 1,
                'Delete ISO 8859 LINE TABULATION SET control character'
            ],
            [
                "Delete ISO 8859\xC2\x8B PARTIAL LINE FORWARD control character", 2, 1,
                'Delete ISO 8859 PARTIAL LINE FORWARD control character'
            ],
            [
                "Delete ISO 8859\xC2\x8C PARTIAL LINE BACKWARD control character", 2, 1,
                'Delete ISO 8859 PARTIAL LINE BACKWARD control character'
            ],
            [
                "Delete ISO 8859\xC2\x8D REVERSE LINE FEED control character", 2, 1,
                'Delete ISO 8859 REVERSE LINE FEED control character'
            ],
            [
                "Delete ISO 8859\xC2\x8E SINGLE SHIFT TWO control character", 2, 1,
                'Delete ISO 8859 SINGLE SHIFT TWO control character'
            ],
            [
                "Delete ISO 8859\xC2\x8F SINGLE SHIFT THREE control character", 2, 1,
                'Delete ISO 8859 SINGLE SHIFT THREE control character'
            ],
            [
                "Delete ISO 8859\xC2\x90 DEVICE CONTROL STRING control character", 2, 1,
                'Delete ISO 8859 DEVICE CONTROL STRING control character'
            ],
            [
                "Delete ISO 8859\xC2\x91 PRIVATE USE ONE control character", 2, 1,
                'Delete ISO 8859 PRIVATE USE ONE control character'
            ],
            [
                "Delete ISO 8859\xC2\x92 PRIVATE USE TWO control character", 2, 1,
                'Delete ISO 8859 PRIVATE USE TWO control character'
            ],
            [
                "Delete ISO 8859\xC2\x93 SET TRANSMIT STATE control character", 2, 1,
                'Delete ISO 8859 SET TRANSMIT STATE control character'
            ],
            [
                "Delete ISO 8859\xC2\x95 MESSAGE WAITING control character", 2, 1,
                'Delete ISO 8859 MESSAGE WAITING control character'
            ],
            [
                "Delete ISO 8859\xC2\x96 START OF GUARDED AREA control character", 2, 1,
                'Delete ISO 8859 START OF GUARDED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x97 END OF GUARDED AREA control character", 2, 1,
                'Delete ISO 8859 END OF GUARDED AREA control character'
            ],
            [
                "Delete ISO 8859\xC2\x99 SINGLE GRAPHIC CHARACTER INTRODUCER control character", 2, 1,
                'Delete ISO 8859 SINGLE GRAPHIC CHARACTER INTRODUCER control character'
            ],
            [
                "Delete ISO 8859\xC2\x9A SINGLE CHARACTER INTRODUCER control character", 2, 1,
                'Delete ISO 8859 SINGLE CHARACTER INTRODUCER control character'
            ],
            [
                "Delete ISO 8859\xC2\x9B CONTROL SEQUENCE INTRODUCER control character", 2, 1,
                'Delete ISO 8859 CONTROL SEQUENCE INTRODUCER control character'
            ],
            [
                "Delete ISO 8859\xC2\x9D OPERATING SYSTEM COMMAND control character", 2, 1,
                'Delete ISO 8859 OPERATING SYSTEM COMMAND control character'
            ],
            [
                "Delete ISO 8859\xC2\x9E PRIVACY MESSAGE control character", 2, 1,
                'Delete ISO 8859 PRIVACY MESSAGE control character'
            ],
            [
                "Delete ISO 8859\xC2\x9F APPLICATION PROGRAM COMMAND control character", 2, 1,
                'Delete ISO 8859 APPLICATION PROGRAM COMMAND control character'
            ],
            [
                "Delete Unicode\xE2\x80\x8B ZERO WIDTH SPACE control character", 2, 1,
                'Delete Unicode ZERO WIDTH SPACE control character'
            ],
            [
                "Delete Unicode\xEF\xBB\xBF ZERO WIDTH NO-BREAK SPACE control character", 2, 1,
                'Delete Unicode ZERO WIDTH NO-BREAK SPACE control character'
            ],
            [
                "Delete Unicode\xEF\xBF\xBD REPLACEMENT CHARACTER control character", 2, 1,
                'Delete Unicode REPLACEMENT CHARACTER control character'
            ],
        ];
    }
}
