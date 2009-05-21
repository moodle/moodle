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
 * This file is part of the CodeAnalysis addon for PHP_CodeSniffer.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-codeanalysis
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Detects unnecessary final modifiers inside of final classes.
 *
 * This rule is based on the PMD rule catalog. The Unnecessary Final Modifier
 * sniff detects the use of the final modifier inside of a final class which
 * is unnecessary.
 *
 * <code>
 * final class Foo
 * {
 *     public final function bar()
 *     {
 *     }
 * }
 * </code>
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_codeanalysis_unnecessaryfinalmodifiersniff implements php_codesniffer_sniff {


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array(integer)
     */
    public function register() {
        return array(T_CLASS);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();
        $token  = $tokens[$stackptr];

        // Skip for-statements without body.
        if (isset($token['scope_opener']) === false) {
            return;
        }

        // Fetch previous token.
        $prev = $phpcsfile->findprevious(PHP_CodeSniffer_tokens::$emptyTokens, ($stackptr - 1), null, true);

        // Skip for non final class.
        if ($prev === false || $tokens[$prev]['code'] !== T_FINAL) {
            return;
        }

        $next = ++$token['scope_opener'];
        $end  = --$token['scope_closer'];

        for (; $next <= $end; ++$next) {

            if ($tokens[$next]['code'] === T_FINAL) {
                $error = 'Unnecessary FINAL modifier in FINAL class';
                $phpcsfile->addwarning($error, $next);
            }
        }
    }
}
