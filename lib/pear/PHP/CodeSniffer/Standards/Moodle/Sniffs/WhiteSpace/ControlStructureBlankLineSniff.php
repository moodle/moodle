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
 * moodle_sniffs_whitespace_controlstructureblanklinesniff
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-whitespace
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_controlstructureblanklinesniff
 *
 * Checks that there is a blank line before control structures
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_whitespace_controlstructureblanklinesniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_IF, T_FOR, T_FOREACH, T_WHILE, T_SWITCH, T_TRY, T_CATCH);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile All the tokens found in the document.
     * @param int                  $stackptr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();
        $previoustoken = $stackptr - 1;

        // Move back until we find the previous non-whitespace, non-comment token
        do {
            $previoustoken = $phpcsfile->findprevious(array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT),
                                                      ($previoustoken - 1), null, true);

        } while ($tokens[$previoustoken]['line'] == $tokens[$stackptr]['line']);

        $previous_non_ws_token = $tokens[$previoustoken];

        // If this token is immediately on the line before this control structure, print a warning
        if ($previous_non_ws_token['line'] == ($tokens[$stackptr]['line'] - 1)) {
            // Exception: do {EOL...} while (...);
            if ($tokens[$stackptr]['code'] == T_WHILE && $tokens[($stackptr - 1)]['code'] == T_CLOSE_CURLY_BRACKET) {
                // Ignore do...while (see above)
            } else {
                $phpcsfile->addWarning('You should add a blank line before control structures', $stackptr);
            }
        }
    }
}
