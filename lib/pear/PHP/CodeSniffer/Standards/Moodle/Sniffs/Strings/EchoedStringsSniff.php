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
 * moodle_sniffs_strings_echoedstringssniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-strings
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_strings_echoedstringssniff.
 *
 * Makes sure that any strings that are "echoed" are not enclosed in brackets
 * like a function call.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_strings_echoedstringssniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_ECHO);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();

        $firstcontent = $phpcsfile->findnext(array(T_WHITESPACE), ($stackptr + 1), null, true);
        // If the first non-whitespace token is not an opening parenthesis, then we are not concerned.
        if ($tokens[$firstcontent]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        $endofstatement = $phpcsfile->findnext(array(T_SEMICOLON), $stackptr, null, false);

        // If the token before the semi-colon is not a closing parenthesis, then we are not concerned.
        if ($tokens[($endofstatement - 1)]['code'] !== T_CLOSE_PARENTHESIS) {
            return;
        }

        if (($phpcsfile->findnext(PHP_CodeSniffer_tokens::$operators, $stackptr, $endofstatement, false)) === false) {
            // There are no arithmetic operators in this.
            $error = 'Echoed strings should not be bracketed';
            $phpcsfile->adderror($error, $stackptr);
        }
    }
}
