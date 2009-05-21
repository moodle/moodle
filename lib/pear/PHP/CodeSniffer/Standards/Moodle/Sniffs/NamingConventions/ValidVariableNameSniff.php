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
 * moodle_sniffs_namingconventions_validvariablenamesniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-namingconventions
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    $error = 'Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * moodle_sniffs_namingconventions_validvariablenamesniff.
 *
 * Checks the naming of member variables.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_namingconventions_validvariablenamesniff extends php_codesniffer_standards_abstractvariablesniff {

    private $allowed_global_vars = array('CFG', 'SESSION', 'USER', 'COURSE', 'SITE', 'PAGE', 'DB', 'THEME',
                                         '_SERVER', '_GET', '_POST', '_FILES', '_REQUEST', '_SESSION',
                                         '_ENV', '_COOKIE', '_HTTP_RAW_POST_DATA');

    /**
     * Processes class member variables.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processmembervar(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();
        $membername     = ltrim($tokens[$stackptr]['content'], '$');

        if (preg_match('/[A-Z]+/', $membername)) {
            $error = "Member variable \"$membername\" must be all lower-case";
            $phpcsfile->adderror($error, $stackptr);
            return;
        }

        // Must not be preceded by 'var' keyword
        $keyword = $phpcsfile->findprevious(T_VAR, $stackptr);

        if ($tokens[$keyword]['line'] == $tokens[$stackptr]['line']) {
            $error = "The 'var' keyword is not permitted." .
                     'Visibility must be explicitly declared with public, private or protected';
            $phpcsfile->adderror($error, $stackptr);
            return;
        }
    }


    /**
     * Processes normal variables.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file where this token was found.
     * @param int                  $stackptr  The position where the token was found.
     *
     * @return void
     */
    protected function processvariable(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();
        $membername     = ltrim($tokens[$stackptr]['content'], '$');

        if (preg_match('/[A-Z]+/', $membername)) {

            if (!in_array($membername, $this->allowed_global_vars)) {
                $error = "Member variable \"$membername\" must be all lower-case";
                $phpcsfile->adderror($error, $stackptr);
                return;
            }
        }
    }


    /**
     * Processes variables in double quoted strings.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file where this token was found.
     * @param int                  $stackptr  The position where the token was found.
     *
     * @return void
     */
    protected function processvariableinstring(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();

        if (preg_match('/\$([A-Za-z0-9_]+)(\-\>([A-Za-z0-9_]+))?/i', $tokens[$stackptr]['content'], $matches)) {
            $firstvar = $matches[1];
            $objectvar = (empty($matches[3])) ? null : $matches[3];
            $membername = $firstvar . $objectvar;

            if (preg_match('/[A-Z]+/', $firstvar, $matches)) {

                if (!in_array($firstvar, $this->allowed_global_vars)) {
                    $error = "Member variable \"$firstvar\" must be all lower-case";
                    $phpcsfile->adderror($error, $stackptr);
                    return;
                }
            }

            if (!empty($objectvar) && preg_match('/[A-Z]+/', $objectvar, $matches)) {
                $error = "Member variable \"$objectvar\" must be all lower-case";
                $phpcsfile->adderror($error, $stackptr);
                return;
            }
        }
        return;
    }
}
