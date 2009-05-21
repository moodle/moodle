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
class moodle_sniffs_codeanalysis_uselessoverridingmethodsniff implements php_codesniffer_sniff {


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array(integer)
     */
    public function register() {
        return array(T_FUNCTION);
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

        // Skip function without body.
        if (isset($token['scope_opener']) === false) {
            return;
        }

        // Get function name.
        $methodname = $phpcsfile->getdeclarationname($stackptr);

        // Get all parameters from method signature.
        $signature = array();

        foreach ($phpcsfile->getmethodparameters($stackptr) as $param) {
            $signature[] = $param['name'];
        }

        $next = ++$token['scope_opener'];
        $end  = --$token['scope_closer'];

        for (; $next <= $end; ++$next) {
            $code = $tokens[$next]['code'];

            if (in_array($code, PHP_CodeSniffer_tokens::$emptyTokens) === true) {
                continue;

            } else if ($code === T_RETURN) {
                continue;
            }

            break;
        }

        // Any token except 'parent' indicates correct code.
        if ($tokens[$next]['code'] !== T_PARENT) {
            return;
        }

        // Find next non empty token index, should be double colon.
        $next = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($next + 1), null, true);

        // Skip for invalid code.
        if ($next === false || $tokens[$next]['code'] !== T_DOUBLE_COLON) {
            return;
        }

        // Find next non empty token index, should be the function name.
        $next = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($next + 1), null, true);

        // Skip for invalid code or other method.
        if ($next === false || $tokens[$next]['content'] !== $methodname) {
            return;
        }

        // Find next non empty token index, should be the open parenthesis.
        $next = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($next + 1), null, true);

        // Skip for invalid code.
        if ($next === false || $tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        $validparametertypes = array(
                                T_VARIABLE,
                                T_LNUMBER,
                                T_CONSTANT_ENCAPSED_STRING,
                               );

        $parameters       = array('');
        $parenthesiscount = 1;
        $count            = count($tokens);

        for (++$next; $next < $count; ++$next) {
            $code = $tokens[$next]['code'];

            if ($code === T_OPEN_PARENTHESIS) {
                ++$parenthesiscount;

            } else if ($code === T_CLOSE_PARENTHESIS) {
                --$parenthesiscount;

            } else if ($parenthesiscount === 1 && $code === T_COMMA) {
                $parameters[] = '';

            } else if (in_array($code, PHP_CodeSniffer_tokens::$emptyTokens) === false) {
                $parameters[(count($parameters) - 1)] .= $tokens[$next]['content'];
            }

            if ($parenthesiscount === 0) {
                break;
            }
        }

        $next = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($next + 1), null, true);

        if ($next === false || $tokens[$next]['code'] !== T_SEMICOLON) {
            return;
        }

        // Check rest of the scope.
        for (++$next; $next <= $end; ++$next) {
            $code = $tokens[$next]['code'];
            // Skip for any other content.

            if (in_array($code, PHP_CodeSniffer_tokens::$emptyTokens) === false) {
                return;
            }
        }

        $parameters = array_map('trim', $parameters);
        $parameters = array_filter($parameters);

        if (count($parameters) === count($signature) && $parameters === $signature) {
            $phpcsfile->addwarning('Useless method overriding detected', $stackptr);
        }
    }
}
