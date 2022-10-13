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
 * Checks variable names are all lower-case, no underscores.
 *
 * @package    local_codechecker
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\NamingConventions;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Files\File;

class ValidVariableNameSniff extends AbstractVariableSniff {

    static public $allowedglobals = array('ADMIN', 'CFG', 'COURSE', 'DB', 'FULLME',
            'OUTPUT', 'PAGE', 'PERF', 'SESSION', 'SITE', 'THEME', 'USER',
            '_SERVER', '_GET', '_POST', '_FILES', '_REQUEST', '_SESSION', '_ENV',
            '_COOKIE', '_HTTP_RAW_POST_DATA', 'ACCESSLIB_PRIVATE', 'ME',
            'CONDITIONLIB_PRIVATE', 'FILTERLIB_PRIVATE', 'SCRIPT', 'MNET_REMOTE_CLIENT',
            'http_response_header');

    /**
     * Processes class member variables.
     *
     * @param File $phpcsfile The file being scanned.
     * @param int $stackptr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->getTokens();
        $membername = ltrim($tokens[$stackptr]['content'], '$');

        if (preg_match('/[A-Z]+/', $membername)) {
            $error = "Member variable \"$membername\" must be all lower-case";
            $fix = $phpcsfile->addFixableError($error, $stackptr, 'MemberNameUnderscore');
            if ($fix === true) {
                $phpcsfile->fixer->beginChangeset();
                $phpcsfile->fixer->replaceToken($stackptr, strtolower($tokens[$stackptr]['content']));
                $phpcsfile->fixer->endChangeset();
            }
        }

        // Find underscores in variable names (accepting $_foo for private vars).
        $pos = strpos($membername, '_');
        if ($pos > 1) {
            $error = "Member variable \"$membername\" must not contain underscores.";
            $fix = $phpcsfile->addFixableError($error, $stackptr, 'MemberNameUnderscore');
            if ($fix === true) {
                $phpcsfile->fixer->beginChangeset();
                $phpcsfile->fixer->replaceToken($stackptr, str_replace('_', '', $tokens[$stackptr]['content']));
                $phpcsfile->fixer->endChangeset();
            }
        }

        // Must not be preceded by 'var' keyword.
        $keyword = $phpcsfile->findPrevious(T_VAR, $stackptr);

        if ($tokens[$keyword]['line'] == $tokens[$stackptr]['line']) {
            $error = "The 'var' keyword is not permitted." .
                     'Visibility must be explicitly declared with public, private or protected';
            $phpcsfile->addError($error, $stackptr, 'MemberNameVisibility');
        }
    }

    /**
     * Processes normal variables.
     *
     * @param File $phpcsfile The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->getTokens();
        $membername     = ltrim($tokens[$stackptr]['content'], '$');
        $this->validate_moodle_variable_name($membername, $phpcsfile, $stackptr);
    }

    /**
     * Processes variables in double quoted strings.
     *
     * @param File $phpcsfile The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    protected function processVariableInString(File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->getTokens();

        if (preg_match('/\$([A-Za-z0-9_]+)(\-\>([A-Za-z0-9_]+))?/i',
                $tokens[$stackptr]['content'], $matches)) {
            $firstvar = $matches[1];

            $this->validate_moodle_variable_name($firstvar, $phpcsfile, $stackptr);
        }
    }

    /**
     * Processes normal moodle variables against Moodle coding guidelines. Note this
     * can't be used for member variables as we allow slightly different rules there.
     *
     * @param string $varname The name of the variable.
     * @param File $phpcsfile The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    private function validate_moodle_variable_name($varname, File $phpcsfile, $stackptr) {
        if (preg_match('/[A-Z]+/', $varname) && !in_array($varname, self::$allowedglobals)) {
            $error = "Variable \"$varname\" must be all lower-case";
            $fix = $phpcsfile->addFixableError($error, $stackptr, 'VariableNameLowerCase');
            if ($fix === true) {
                $phpcsfile->fixer->beginChangeset();
                $tokens = $phpcsfile->getTokens();
                $phpcsfile->fixer->replaceToken(
                    $stackptr, str_replace('$' . $varname, '$' . strtolower($varname), $tokens[$stackptr]['content']));
                $phpcsfile->fixer->endChangeset();
            }
        }

        if (strpos($varname, '_') !== false && !in_array($varname, self::$allowedglobals)) {
            $error = "Variable \"$varname\" must not contain underscores.";
            $fix = $phpcsfile->addFixableError($error, $stackptr, 'VariableNameUnderscore');
            if ($fix === true) {
                $phpcsfile->fixer->beginChangeset();
                $tokens = $phpcsfile->getTokens();
                $phpcsfile->fixer->replaceToken($stackptr, str_replace('_', '', $tokens[$stackptr]['content']));
                $phpcsfile->fixer->endChangeset();
            }
        }
    }
}
