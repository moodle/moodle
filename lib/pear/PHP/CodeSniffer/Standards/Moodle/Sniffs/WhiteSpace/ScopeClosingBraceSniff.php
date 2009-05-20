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
 * @package   lib-pear-php-codesniffer-standards-moodle-sniffs-whitespace
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_whitespace_scopeclosingbracesniff.
 *
 * Checks that the closing braces of scopes are aligned correctly.
 *
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_whitespace_scopeclosingbracesniff implements php_codesniffer_sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
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
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr)
    {
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
        $lineStart = ($stackptr - 1);
        for ($lineStart; $lineStart > 0; $lineStart--) {
            if (strpos($tokens[$lineStart]['content'], $phpcsfile->eolChar) !== false) {
                break;
            }
        }

        // We found a new line, now go forward and find the first non-whitespace
        // token.
        $lineStart = $phpcsfile->findNext(array(T_WHITESPACE), ($lineStart + 1), null, true);

        $startColumn = $tokens[$lineStart]['column'];
        $scopeStart  = $tokens[$stackptr]['scope_opener'];
        $scopeEnd    = $tokens[$stackptr]['scope_closer'];

        // Check that the closing brace is on its own line.
        $lastcontent = $phpcsfile->findPrevious(array(T_WHITESPACE), ($scopeEnd - 1), $scopeStart, true);
        if ($tokens[$lastcontent]['line'] === $tokens[$scopeEnd]['line']) {
            $error = 'Closing brace must be on a line by itself';
            $phpcsfile->adderror($error, $scopeEnd);
            return;
        }

        // Check now that the closing brace is lined up correctly.
        $braceIndent   = $tokens[$scopeEnd]['column'];
        $isBreakCloser = ($tokens[$scopeEnd]['code'] === T_BREAK);
        if (in_array($tokens[$stackptr]['code'], array(T_CASE, T_DEFAULT)) === true && $isBreakCloser === true) {
            // BREAK statements should be indented 4 spaces from the
            // CASE or DEFAULT statement.
            if ($braceIndent !== ($startColumn + 4)) {
                $error = 'Break statement indented incorrectly; expected '.($startColumn + 3).' spaces, found '.($braceIndent - 1);
                $phpcsfile->adderror($error, $scopeEnd);
            }
        } else {
            if (in_array($tokens[$stackptr]['code'], array(T_CASE, T_DEFAULT))) {
                $startColumn -= 4;
            }

            if ($braceIndent !== $startColumn) {
                $error = 'Closing brace indented incorrectly; expected '.($startColumn - 1).' spaces, found '.($braceIndent - 1);
                $phpcsfile->adderror($error, $scopeEnd);
            }
        }

    }


}

?>
