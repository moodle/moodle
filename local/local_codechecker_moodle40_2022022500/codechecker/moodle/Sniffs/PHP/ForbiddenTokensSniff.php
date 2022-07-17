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
 * The use of some tokens is forbidden.
 *
 * This Sniff looks for some functions and operators that are handled
 * as specific tokens by the CS tokenizer. Complements {@link moodle_Sniffs_PHP_ForbiddenFunctionsSniff}.
 *
 * @package    local_codechecker
 * @copyright  2014 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleCodeSniffer\moodle\Sniffs\PHP;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ForbiddenTokensSniff implements Sniff {

    /**
     * Returns an array of Tokenizer tokens and errors this Sniff will listen and process.
     *
     * @return array with tokens as keys and error messages as description.
     */
    protected function get_forbidden_tokens() {
        return array(
            T_EVAL => 'The use of function eval() is forbidden',
            T_GOTO => 'The use of operator goto is forbidden',
            T_GOTO_LABEL => 'The use of goto labels is forbidden',
            T_BACKTICK => 'The use of backticks for shell execution is forbidden',
        );
    }

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array tokens this sniff will handle.
     */
    public function register() {
        return array_keys($this->get_forbidden_tokens());
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr) {

        $tokens = $phpcsFile->getTokens();
        $forbidden = $this->get_forbidden_tokens();
        $token = $tokens[$stackPtr];
        $phpcsFile->addError($forbidden[$token['code']], $stackPtr, 'Found');
    }
}
