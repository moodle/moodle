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
 * @package   lib-pear-php-codesniffer-standards-moodle-sniffs-whitespace
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_controlstructureblanklinesniff
 *
 * Checks that there is a blank line before control structures
 *
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_whitespace_controlstructureblanklinesniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_IF, T_FOR, T_FOREACH, T_WHILE, T_SWITCH);
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

        while ($tokens[$previoustoken]['line'] == $tokens[$stackptr]['line']) {
            $previoustoken = $phpcsfile->findprevious(T_WHITESPACE, ($previoustoken - 1), null, true);
        }

        $previous_non_ws_token = $tokens[$previoustoken];

        if ($previous_non_ws_token['line'] == ($tokens[$stackptr]['line'] - 1)) {
            $phpcsfile->addWarning('You should add a blank line before control structures', $stackptr);
        }
    }
}
