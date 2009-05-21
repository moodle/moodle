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
 * moodle_sniffs_strings_doublequoteusagesniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-strings
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_strings_doublequoteusagesniff.
 *
 * Makes sure that any use of Double Quotes ("") are warranted.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_strings_doublequoteusagesniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_CONSTANT_ENCAPSED_STRING);
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

        $workingstring = $tokens[$stackptr]['content'];

        // Check if it's a double quoted string.
        if (strpos($workingstring, '"') === false) {
            return;
        }

        // Make sure it's not a part of a string started above.
        // If it is, then we have already checked it.
        if ($workingstring[0] !== '"') {
            return;
        }

        // Work through the following tokens, in case this string is stretched
        // over multiple Lines.
        for ($i = ($stackptr + 1); $i < $phpcsfile->numTokens; $i++) {

            if ($tokens[$i]['type'] !== 'T_CONSTANT_ENCAPSED_STRING') {
                break;
            }
            $workingstring .= $tokens[$i]['content'];
        }

        $allowedchars = array(
                         '\n',
                         '\r',
                         '\f',
                         '\t',
                         '\v',
                         '\x',
                         '\'',
                         '$'
                        );

        foreach ($allowedchars as $testchar) {

            if (strpos($workingstring, $testchar) !== false) {
                return;
            }
        }

        $error = "String $workingstring does not require double quotes; use single quotes instead";
        $phpcsfile->addwarning($error, $stackptr);
    }
}
