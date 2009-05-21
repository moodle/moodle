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
 * moodle_sniffs_php_lowercaseconstantsniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-php
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_php_lowercaseconstantsniff.
 *
 * Checks that all uses of true, false and null are lowerrcase.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_php_lowercaseconstantsniff implements php_codesniffer_sniff {

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedtokenizers = array('PHP', 'JS');

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_TRUE, T_FALSE, T_NULL);

    }


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();

        $keyword = $tokens[$stackptr]['content'];

        if (strtolower($keyword) !== $keyword) {
            $error = 'TRUE, FALSE and NULL must be lowercase; expected "'.
                     strtolower($keyword).'" but found "'.$keyword.'"';
            $phpcsfile->adderror($error, $stackptr);
        }
    }
}
