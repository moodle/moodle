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
 * moodle_sniffs_functions_validdefaultvaluesniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-functions
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_functions_validdefaultvaluesniff.
 *
 * A Sniff to ensure that parameters defined for a function that have a default
 * value come at the end of the function signature.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_functions_validdefaultvaluesniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_FUNCTION);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();

        $argstart = $tokens[$stackptr]['parenthesis_opener'];
        $argend   = $tokens[$stackptr]['parenthesis_closer'];

        // Flag for when we have found a default in our arg list.
        // If there is a value without a default after this, it is an error.
        $defaultfound = false;

        $nextarg = $argstart;

        while (($nextarg = $phpcsfile->findnext(T_VARIABLE, ($nextarg + 1), $argend)) !== false) {
            $arghasdefault = self::_arghasdefault($phpcsfile, $nextarg);

            if (($arghasdefault === false) && ($defaultfound === true)) {
                $error  = 'Arguments with default values must be at the end';
                $error .= ' of the argument list';
                $phpcsfile->adderror($error, $nextarg);
                return;
            }

            if ($arghasdefault === true) {
                $defaultfound = true;
            }
        }

    }


    /**
     * Returns true if the passed argument has a default value.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $argptr    The position of the argument
     *                                        in the stack.
     *
     * @return bool
     */
    private static function _arghasdefault(PHP_CodeSniffer_File $phpcsfile, $argptr) {
        $tokens    = $phpcsfile->gettokens();
        $nexttoken = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($argptr + 1), null, true);

        if ($tokens[$nexttoken]['code'] !== T_EQUAL) {
            return false;
        }

        return true;

    }


}
