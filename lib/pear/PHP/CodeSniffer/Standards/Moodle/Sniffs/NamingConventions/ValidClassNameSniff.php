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
 * moodle_sniffs_namingconventions_validclassnamesniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-namingconventions
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_namingconventions_validclassnamesniff.
 *
 * Ensures class and interface names start with a capital letter
 * and use _ separators.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_namingconventions_validclassnamesniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_CLASS, T_INTERFACE);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The current file being processed.
     * @param int                  $stackptr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();

        $classname = $phpcsfile->findnext(T_STRING, $stackptr);
        $name      = trim($tokens[$classname]['content']);

        // Make sure that the word is all lowercase

        if (!preg_match('/[a-z]?/', $name)) {
            $error = ucfirst($tokens[$stackptr]['content']).' name is not valid, must be all lower-case';
            $phpcsfile->adderror($error, $stackptr);
        }
    }
}
