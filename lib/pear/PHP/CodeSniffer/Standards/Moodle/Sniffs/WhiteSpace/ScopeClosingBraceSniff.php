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
 * moodle_sniffs_whitespace_scopeclosingbracesniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-whitespace
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_whitespace_scopeclosingbracesniff.
 *
 * Checks that the closing braces of scopes are aligned correctly.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_whitespace_scopeclosingbracesniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return PHP_CodeSniffer_tokens::$scopeOpeners;
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

        // If this is an inline condition (ie. there is no scope opener), then
        // return, as this is not a new scope.
        if (isset($tokens[$stackptr]['scope_closer']) === false) {
            return;
        }

        // We need to actually find the first piece of content on this line,
        // as if this is a method with tokens before it (public, static etc)
        // or an if with an else before it, then we need to start the scope
        // checking from there, rather than the current token.
        $linestart = ($stackptr - 1);

        for ($linestart; $linestart > 0; $linestart--) {

            if (strpos($tokens[$linestart]['content'], $phpcsfile->eolChar) !== false) {
                break;
            }
        }

        // We found a new line, now go forward and find the first non-whitespace
        // token.
        $linestart = $phpcsfile->findnext(array(T_WHITESPACE), ($linestart + 1), null, true);

        $startcolumn = $tokens[$linestart]['column'];
        $scopestart  = $tokens[$stackptr]['scope_opener'];
        $scopeend    = $tokens[$stackptr]['scope_closer'];

        // Check that the closing brace is on its own line.
        $lastcontent = $phpcsfile->findprevious(array(T_WHITESPACE), ($scopeend - 1), $scopestart, true);

        if ($tokens[$lastcontent]['line'] === $tokens[$scopeend]['line']) {
            $error = 'Closing brace must be on a line by itself';
            $phpcsfile->adderror($error, $scopeend);
            return;
        }

        // Check now that the closing brace is lined up correctly.
        $braceindent   = $tokens[$scopeend]['column'];
        $isbreakcloser = ($tokens[$scopeend]['code'] === T_BREAK);

        if (in_array($tokens[$stackptr]['code'], array(T_CASE, T_DEFAULT)) === true && $isbreakcloser === true) {
            // BREAK statements should be indented 4 spaces from the
            // CASE or DEFAULT statement.
            if ($braceindent !== ($startcolumn + 4)) {
                $error = 'Break statement indented incorrectly; expected '.($startcolumn + 3).' spaces, found '.
                         ($braceindent - 1);
                $phpcsfile->adderror($error, $scopeend);
            }

        } else {

            if (in_array($tokens[$stackptr]['code'], array(T_CASE, T_DEFAULT))) {
                $startcolumn -= 4;
            }

            if ($braceindent !== $startcolumn) {
                $error = 'Closing brace indented incorrectly; expected '.($startcolumn - 1).' spaces, found '.
                         ($braceindent - 1);
                $phpcsfile->adderror($error, $scopeend);
            }
        }
    }
}
