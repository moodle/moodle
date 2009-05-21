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
 * Detects incrementer jumbling in for loops.
 *
 * This rule is based on the PMD rule catalog. The jumbling incrementer sniff
 * detects the usage of one and the same incrementer into an outer and an inner
 * loop. Even it is intended this is confusing code.
 *
 * <code>
 * class Foo
 * {
 *     public function bar($x)
 *     {
 *         for ($i = 0; $i < 10; $i++)
 *         {
 *             for ($k = 0; $k < 20; $i++)
 *             {
 *                 echo 'Hello';
 *             }
 *         }
 *     }
 * }
 * </code>
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_codeanalysis_jumbledincrementersniff implements php_codesniffer_sniff {


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

        // Skip for-loop without body.
        if (isset($token['scope_opener']) === false) {
            return;
        }

        // Find incrementors for outer loop.
        $outer = $this->findincrementers($tokens, $token);

        // Skip if empty.
        if (count($outer) === 0) {
            return;
        }

        // Find nested for loops.
        $start = ++$token['scope_opener'];
        $end   = --$token['scope_closer'];

        for (; $start <= $end; ++$start) {

            if ($tokens[$start]['code'] !== T_FOR) {
                continue;
            }

            $inner = $this->findincrementers($tokens, $tokens[$start]);
            $diff  = array_intersect($outer, $inner);

            if (count($diff) !== 0) {
                $error = sprintf('Loop incrementor (%s) jumbling with inner loop', join(', ', $diff));
                $phpcsfile->addwarning($error, $stackptr);
            }
        }
    }


    /**
     * Get all used variables in the incrementer part of a for statement.
     *
     * @param array(integer=>array) $tokens Array with all code sniffer tokens.
     * @param array(string=>mixed)  $token  Current for loop token
     *
     * @return array(string) List of all found incrementer variables.
     */
    protected function findincrementers(array $tokens, array $token) {
        // Skip invalid statement.
        if (isset($token['parenthesis_opener']) === false) {
            return array();
        }

        $start = ++$token['parenthesis_opener'];
        $end   = --$token['parenthesis_closer'];

        $incrementers = array();
        $semicolons   = 0;

        for ($next = $start; $next <= $end; ++$next) {
            $code = $tokens[$next]['code'];

            if ($code === T_SEMICOLON) {
                ++$semicolons;

            } else if ($semicolons === 2 && $code === T_VARIABLE) {
                $incrementers[] = $tokens[$next]['content'];
            }
        }

        return $incrementers;

    }
}
