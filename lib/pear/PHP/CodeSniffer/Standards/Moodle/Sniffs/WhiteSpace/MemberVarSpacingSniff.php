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
 * Verifies that class members are spaced correctly.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-whitespace
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found');
}

/**
 * Verifies that class members are spaced correctly.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_whitespace_membervarspacingsniff extends php_codesniffer_standards_abstractvariablesniff {


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file where this token was found.
     * @param int                  $stackptr  The position where the token was found.
     *
     * @return void
     */
    protected function processmembervar(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();

        // There needs to be 1 blank line before the var, not counting comments.
        $prevlinetoken = null;

        for ($i = ($stackptr - 1); $i > 0; $i--) {

            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_tokens::$commentTokens) === true) {
                // Skip comments.
                continue;

            } else if (strpos($tokens[$i]['content'], $phpcsfile->eolChar) === false) {
                // Not the end of the line.
                continue;

            } else {
                // If this is a WHITESPACE token, and the token right before
                // it is a DOC_COMMENT, then it is just the newline after the
                // member var's comment, and can be skipped.
                if ($tokens[$i]['code'] === T_WHITESPACE &&
                    in_array($tokens[($i - 1)]['code'], PHP_CodeSniffer_tokens::$commentTokens) === true) {
                    continue;
                }

                $prevlinetoken = $i;
                break;
            }
        }

        if (is_null($prevlinetoken) === true) {
            // Never found the previous line, which means
            // there are 0 blank lines before the member var.
            $foundlines = 0;

        } else {
            $prevcontent = $phpcsfile->findprevious(array(T_WHITESPACE, T_DOC_COMMENT), $prevlinetoken, null, true);
            $foundlines  = ($tokens[$prevlinetoken]['line'] - $tokens[$prevcontent]['line']);
        }

        if ($foundlines !== 1) {
            // $phpcsfile->adderror("Expected 1 blank line before member var; $foundlines found", $stackptr);
        }
    }


    /**
     * Processes normal variables.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file where this token was found.
     * @param int                  $stackptr  The position where the token was found.
     *
     * @return void
     */
    protected function processvariable(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        // We don't care about normal variables.
        return;
    }


    /**
     * Processes variables in double quoted strings.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file where this token was found.
     * @param int                  $stackptr  The position where the token was found.
     *
     * @return void
     */
    protected function processvariableinstring(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        // We don't care about normal variables.
        return;
    }
}
