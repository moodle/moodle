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
 * Checks that each string does not have extra whitespace at end of line
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleCodeSniffer\moodle\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class WhiteSpaceInStringsSniff implements Sniff {

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(
        T_CONSTANT_ENCAPSED_STRING,
        T_DOUBLE_QUOTED_STRING,
        T_HEREDOC,
        T_WHITESPACE
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsfile The file being scanned.
     * @param int $stackptr  The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->getTokens();
        // Look for final whitespace endings but not in whitespace tokens
        // (not sure which cases are covered by this, because it seems to
        // conflict/dupe {@link SuperfluousWhitespaceSniff} but, it's kept
        // working for any registered token but T_WHITESPACE, that is handled
        // by other regexps.
        if ($tokens[$stackptr]['type'] != 'T_WHITESPACE') {
            preg_match('~\s[\r\n]~', $tokens[$stackptr]['content'], $matches);
            if (!empty($matches)) {
                $error = 'Whitespace found at end of line within string';
                $phpcsfile->addError($error, $stackptr, 'EndLine');
            }
        } else {
            // Other tests within T_WHITESPACE tokens
            // Look for tabs only in whitespace tokens.
            if (strpos($tokens[$stackptr]['content'], "\t") !== false) {
                $error = 'Tab found within whitespace';
                $phpcsfile->addError($error, $stackptr, 'TabWhitespace');
            }
        }
    }
}
