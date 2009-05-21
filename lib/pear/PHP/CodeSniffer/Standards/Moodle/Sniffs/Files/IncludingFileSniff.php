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
 * moodle_sniffs_files_includingfilesniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-files
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_files_includingfilesniff.
 *
 * Checks that the include_once is used in conditional situations, and
 * require_once is used elsewhere. Also checks that brackets do not surround
 * the file being included.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_files_includingfilesniff implements php_codesniffer_sniff {

    /**
     * Conditions that should use include_once
     *
     * @var array(int)
     */
    private static $_conditions = array(T_IF, T_ELSE, T_ELSEIF, T_SWITCH);


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_INCLUDE_ONCE, T_REQUIRE_ONCE, T_REQUIRE, T_INCLUDE);
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

        //$nexttoken = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($stackptr + 1), null, true);
        //if ($tokens[$nexttoken]['code'] === T_OPEN_PARENTHESIS) {
        //    $error  = '"'.$tokens[$stackptr]['content'].'"';
        //    $error .= ' is a statement, not a function; ';
        //    $error .= 'no parentheses are required';
        //    $phpcsfile->adderror($error, $stackptr);
        //}

        $incondition = (count($tokens[$stackptr]['conditions']) !== 0) ? true : false;

        // Check to see if this including statement is within the parenthesis of a condition.
        // If that's the case then we need to process it as being within a condition, as they
        // are checking the return value.
        if (isset($tokens[$stackptr]['nested_parenthesis']) === true) {

            foreach ($tokens[$stackptr]['nested_parenthesis'] as $left => $right) {

                if (isset($tokens[$left]['parenthesis_owner']) === true) {
                    $incondition = true;
                }
            }
        }

        // Check to see if they are assigning the return value of this including call.
        // If they are then they are probably checking it, so its conditional.
        $previous = $phpcsfile->findprevious(PHP_CodeSniffer_tokens::$emptyTokens, ($stackptr - 1), null, true);

        if (in_array($tokens[$previous]['code'], PHP_CodeSniffer_tokens::$assignmentTokens) === true) {
            // The have assigned the return value to it, so its conditional.
            $incondition = true;
        }

        $tokencode = $tokens[$stackptr]['code'];

        if ($incondition === true) {
            // We are inside a conditional statement. We need an include_once.
            if ($tokencode === T_REQUIRE_ONCE) {
                $error  = 'File is being conditionally included; ';
                $error .= 'use "include_once" instead';
                $phpcsfile->adderror($error, $stackptr);

            } else if ($tokencode === T_REQUIRE) {
                $error  = 'File is being conditionally included; ';
                $error .= 'use "include" instead';
                $phpcsfile->adderror($error, $stackptr);
            }

        } else {
            // We are unconditionally including, we need a require_once.
            if ($tokencode === T_INCLUDE_ONCE) {
                $error  = 'File is being unconditionally included; ';
                $error .= 'use "require_once" instead';
                $phpcsfile->adderror($error, $stackptr);

            } else if ($tokencode === T_INCLUDE) {
                $error  = 'File is being unconditionally included; ';
                $error .= 'use "require" instead';
                $phpcsfile->adderror($error, $stackptr);
            }
        }
    }
}
