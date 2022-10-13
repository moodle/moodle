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
 * moodle_sniffs_namingconventions_validfunctionnamesniff.
 *
 * Ensures method names are correct depending on whether they are public
 * or private, and that functions are named correctly.
 *
 * @package    local_codechecker
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\NamingConventions;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;

class ValidFunctionNameSniff extends AbstractScopeSniff {

    /** @var array A list of all PHP magic methods. */
    private $magicmethods = array(
        'construct',
        'destruct',
        'call',
        'callStatic',
        'get',
        'set',
        'isset',
        'unset',
        'sleep',
        'wakeup',
        'toString',
        'set_state',
        'clone',
    );

    /** @var array A list of all PHP magic functions. */
    private $magicfunctions = array(
        'autoload'
    );

    private $permittedmethods = array(
        'setUp',
        'tearDown', // Used by simpletest.
        'setUpBeforeClass',
        'offsetExists',
        'offsetGet',
        'offsetSet',
        'offsetUnset', // Defined by the PHP ArrayAccess interface.
        'tearDownAfterClass',
        'jsonSerialize',
    );

    /**
     * Constructs a moodle_sniffs_namingconventions_validfunctionnamesniff.
     */
    public function __construct() {
        parent::__construct(Tokens::$ooScopeTokens, array(T_FUNCTION), true);
    }

    /**
     * Processes the tokens within the scope.
     *
     * @param File $phpcsfile The file being processed.
     * @param int $stackptr  The position where this token was found.
     * @param int $currscope The position of the current scope.
     *
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsfile,
            $stackptr, $currscope) {
        $classname  = $phpcsfile->getDeclarationName($currscope);
        $methodname = $phpcsfile->getDeclarationName($stackptr);

        // Is this a magic method. IE. is prefixed with "__".
        if (preg_match('|^__|', $methodname) !== 0) {
            $magicpart = substr($methodname, 2);

            if (!in_array($magicpart, $this->magicmethods)) {
                 $error = "method name \"$classname::$methodname\" is invalid; " .
                          'only PHP magic methods should be prefixed with a double underscore';
                 $phpcsfile->addError($error, $stackptr, 'MagicLikeMethod');
            }

            return;
        }

        $methodprops    = $phpcsfile->getMethodProperties($stackptr);
        $scope          = $methodprops['scope'];
        $scopespecified = $methodprops['scope_specified'];

        // Only lower-case accepted.
        if (preg_match('/[A-Z]+/', $methodname) &&
                !in_array($methodname, $this->permittedmethods)) {

            if ($scopespecified === true) {
                $error = ucfirst($scope) . ' method name "' . $classname . '::' .
                        $methodname .'" must be in lower-case letters only';
            } else {
                $error = 'method name "' . $classname . '::' . $methodname .
                        '" must be in lower-case letters only';
            }

            $fix = $phpcsfile->addFixableError($error, $stackptr + 2, 'LowercaseMethod');
            if ($fix === true) {
                $phpcsfile->fixer->beginChangeset();
                $tokens = $phpcsfile->getTokens();
                $phpcsfile->fixer->replaceToken($stackptr + 2, strtolower($tokens[$stackptr + 2]['content']));
                $phpcsfile->fixer->endChangeset();
            }

            return;
        }
    }

    /**
     * Processes the tokens outside the scope.
     *
     * @param File $phpcsfile The file being processed.
     * @param int $stackptr  The position where this token was found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(File $phpcsfile, $stackptr) {
        $functionname = $phpcsfile->getDeclarationName($stackptr);

        // Is this a magic function. IE. is prefixed with "__".
        if (preg_match('|^__|', $functionname) !== 0) {
            $magicpart = substr($functionname, 2);

            if (in_array($magicpart, $this->magicfunctions) === false) {
                 $error = "Function name \"$functionname\" is invalid; " .
                          'only PHP magic methods should be prefixed with a double underscore';
                 $phpcsfile->adderror($error, $stackptr, 'MagicLikeFunction');
            }

            return;
        }

        // Only lower-case accepted.
        if (preg_match('/[A-Z]+/', $functionname)) {
            $error = "function name \"$functionname\" must be lower-case letters only";

            $fix = $phpcsfile->addFixableError($error, $stackptr, 'LowercaseFunction');
            if ($fix === true) {
                $phpcsfile->fixer->beginChangeset();
                $tokens = $phpcsfile->getTokens();
                $phpcsfile->fixer->replaceToken($stackptr + 2, strtolower($tokens[$stackptr + 2]['content']));
                $phpcsfile->fixer->endChangeset();
            }

            return;
        }
    }
}
