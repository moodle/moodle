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
 * Detects for-loops that use a function call in the test expression.
 *
 * This rule is based on the PMD rule catalog. Detects for-loops that use a
 * function call in the test expression.
 *
 * <code>
 * class Foo
 * {
 *     public function bar($x)
 *     {
 *         $a = array(1, 2, 3, 4);
 *         for ($i = 0; $i < count($a); $i++) {
 *              $a[$i] *= $i;
 *         }
 *     }
 * }
 * </code>
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_codeanalysis_forloopwithtestfunctioncallsniff implements php_codesniffer_sniff {


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array(integer)
     */
    public function register() {
        return array(T_FOR);
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

        // Skip invalid statement.
        if (isset($token['parenthesis_opener']) === false) {
            return;
        }

        $next = ++$token['parenthesis_opener'];
        $end  = --$token['parenthesis_closer'];

        $position = 0;

        for (; $next <= $end; ++$next) {
            $code = $tokens[$next]['code'];

            if ($code === T_SEMICOLON) {
                ++$position;
            }

            if ($position < 1) {
                continue;

            } else if ($position > 1) {
                break;

            } else if ($code !== T_VARIABLE && $code !== T_STRING) {
                continue;
            }

            // Find next non empty token, if it is a open curly brace we have a
            // function call.
            $index = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($next + 1), null, true);

            if ($tokens[$index]['code'] === T_OPEN_PARENTHESIS) {
                $error = 'Avoid function calls in a FOR loop test part';
                $phpcsfile->addwarning($error, $stackptr);
                break;
            }
        }
    }
}
