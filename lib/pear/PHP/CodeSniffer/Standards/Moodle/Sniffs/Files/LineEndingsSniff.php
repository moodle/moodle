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
 * moodle_sniffs_files_lineendingssniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-files
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_files_lineendingssniff.
 *
 * Checks that end of line characters are correct.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_files_lineendingssniff implements php_codesniffer_sniff {

    /**
     * The valid EOL character.
     *
     * @var string
     */
    protected $eolChar = "\n";


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_OPEN_TAG);

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
        // We are only interested if this is the first open tag.
        if ($stackptr !== 0) {

            if ($phpcsfile->findprevious(T_OPEN_TAG, ($stackptr - 1)) !== false) {
                return;
            }
        }

        if ($phpcsfile->eolChar !== $this->eolChar) {
            $expected = $this->eolChar;
            $expected = str_replace("\n", '\n', $expected);
            $expected = str_replace("\r", '\r', $expected);
            $found    = $phpcsfile->eolChar;
            $found    = str_replace("\n", '\n', $found);
            $found    = str_replace("\r", '\r', $found);
            $error    = "end of line character is invalid; expected \"$expected\" but found \"$found\"";
            $phpcsfile->adderror($error, $stackptr);
        }

    }
}
