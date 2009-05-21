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
 * moodle_sniffs_functions_functioncallsignaturesniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-functions
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_functions_functioncallsignaturesniff.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_functions_functioncallsignaturesniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_STRING);
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

        // Find the next non-empty token.
        $next = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($stackptr + 1), null, true);

        if ($tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        if (isset($tokens[$next]['parenthesis_closer']) === false) {
            // Not a function call.
            return;
        }

        // Find the previous non-empty token.
        $previous = $phpcsfile->findprevious(PHP_CodeSniffer_tokens::$emptyTokens, ($stackptr - 1), null, true);

        if ($tokens[$previous]['code'] === T_FUNCTION) {
            // It's a function definition, not a function call.
            return;
        }

        if ($tokens[$previous]['code'] === T_NEW) {
            // We are creating an object, not calling a function.
            return;
        }

        if (($stackptr + 1) !== $next) {
            // Checking this: $value = my_function[*](...).
            $error = 'Space before opening parenthesis of function call prohibited';
            $phpcsfile->adderror($error, $stackptr);
        }

        if ($tokens[($next + 1)]['code'] === T_WHITESPACE) {
            // Checking this: $value = my_function([*]...).
            $error = 'Space after opening parenthesis of function call prohibited';
            $phpcsfile->adderror($error, $stackptr);
        }

        $closer = $tokens[$next]['parenthesis_closer'];

        if ($tokens[($closer - 1)]['code'] === T_WHITESPACE) {
            // Checking this: $value = my_function(...[*]).
            $between = $phpcsfile->findnext(T_WHITESPACE, ($next + 1), null, true);

            // Only throw an error if there is some content between the parenthesis.
            // IE. Checking for this: $value = my_function().
            // If there is no content, then we would have thrown an error in the
            // previous IF statement because it would look like this:
            // $value = my_function( ).
            if ($between !== $closer) {
                $error = 'Space before closing parenthesis of function call prohibited';
                $phpcsfile->adderror($error, $closer);
            }
        }

        $next = $phpcsfile->findnext(T_WHITESPACE, ($closer + 1), null, true);

        if ($tokens[$next]['code'] === T_SEMICOLON) {

            if (in_array($tokens[($closer + 1)]['code'], PHP_CodeSniffer_tokens::$emptyTokens) === true) {
                $error = 'Space after closing parenthesis of function call prohibited';
                $phpcsfile->adderror($error, $closer);
            }
        }
    }
}
