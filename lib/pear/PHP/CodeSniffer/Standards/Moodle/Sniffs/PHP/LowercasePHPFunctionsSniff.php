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
 * moodle_sniffs_php_lowercasephpfunctionssniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-php
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_php_lowercasephpfunctionssniff.
 *
 * Ensures all calls to inbuilt PHP functions are lowercase.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_php_lowercasephpfunctionssniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(
                T_ISSET,
                T_ECHO,
                T_PRINT,
                T_RETURN,
                T_BREAK,
                T_CONTINUE,
                T_EMPTY,
                T_EVAL,
                T_EXIT,
                T_LIST,
                T_UNSET,
                T_INCLUDE,
                T_INCLUDE_ONCE,
                T_REQUIRE,
                T_REQUIRE_ONCE,
                T_NEW,
                T_DECLARE,
                T_STRING,
               );
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();

        if ($tokens[$stackptr]['code'] !== T_STRING) {
            $content = $tokens[$stackptr]['content'];

            if ($content !== strtolower($content)) {
                $type     = strtoupper($content);
                $expected = strtolower($content);
                $error    = "$type keyword must be lowercase; expected \"$expected\" but found \"$content\"";
                $phpcsfile->adderror($error, $stackptr);
            }

            return;
        }

        // Make sure this is a function call.
        $next = $phpcsfile->findnext(T_WHITESPACE, ($stackptr + 1), null, true);

        if ($next === false) {
            // Not a function call.
            return;
        }

        if ($tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        $prev = $phpcsfile->findprevious(T_WHITESPACE, ($stackptr - 1), null, true);

        if ($tokens[$prev]['code'] === T_FUNCTION) {
            // Function declaration, not a function call.
            return;
        }

        if ($tokens[$prev]['code'] === T_OBJECT_OPERATOR) {
            // Not an inbuilt function.
            return;
        }

        if ($tokens[$prev]['code'] === T_DOUBLE_COLON) {
            // Not an inbuilt function.
            return;
        }

        // Make sure it is an inbuilt PHP function.
        // PHP_CodeSniffer doesn't include/require any files, so no
        // user defined global functions can exist, except for
        // PHP_CodeSniffer ones.
        $content = $tokens[$stackptr]['content'];

        if (function_exists($content) === false) {
            return;
        }

        if ($content !== strtolower($content)) {
            $expected = strtolower($content);
            $error = "Calls to inbuilt PHP functions must be lowercase; expected \"$expected\" but found \"$content\"";
            $phpcsfile->adderror($error, $stackptr);
        }
    }
}
