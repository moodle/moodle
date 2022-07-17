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
 * Verifies that class members have scope modifiers. Created by sam marshall,
 * based on a sniff by Greg Sherwood and Marc McIntyre.
 *
 * @package   local_codechecker
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    sam marshall <s.marshall@open.ac.uk>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2011 The Open University
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 */

namespace MoodleCodeSniffer\moodle\Sniffs\PHP;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class MemberVarScopeSniff
        extends AbstractVariableSniff {

    /**
     * Processes the function tokens within the class.
     *
     * @param File $file The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    protected function processMemberVar(File $file, $stackptr) {
        $tokens = $file->getTokens();

        $modifier = $file->findPrevious(Tokens::$scopeModifiers, $stackptr);
        $semicolon = $file->findPrevious(T_SEMICOLON, $stackptr);

        if ($modifier === false || $modifier < $semicolon) {
            $error = 'Scope modifier not specified for member variable "%s"';
            $data  = array($tokens[$stackptr]['content']);
            $file->addError($error, $stackptr, 'Missing', $data);
        }
    }

    /**
     * Processes normal variables.
     *
     * @param File $file The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(File $file, $stackptr) {
        return;
    }

    /**
     * Processes variables in double quoted strings.
     *
     * @param File $file The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    protected function processVariableInString(File $file, $stackptr) {
        return;
    }
}
