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
 * @package   lib-pear-php-codesniffer-standards-moodle-sniffs-namingconventions
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * moodle_sniffs_namingconventions_validfunctionnamesniff.
 *
 * Ensures method names are correct depending on whether they are public
 * or private, and that functions are named correctly.
 *
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_namingconventions_validfunctionnamesniff extends php_codesniffer_standards_abstractscopesniff
{

    /**
     * A list of all PHP magic methods.
     *
     * @var array
     */
    private $_magicMethods = array(
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

    /**
     * A list of all PHP magic functions.
     *
     * @var array
     */
    private $_magicFunctions = array(
                                'autoload',
                               );


    /**
     * Constructs a moodle_sniffs_namingconventions_validfunctionnamesniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION), true);

    }


    /**
     * Processes the tokens within the scope.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being processed.
     * @param int                  $stackptr  The position where this token was
     *                                        found.
     * @param int                  $currScope The position of the current scope.
     *
     * @return void
     */
    protected function processtokenwithinScope(PHP_CodeSniffer_File $phpcsfile, $stackptr, $currScope)
    {
        $className  = $phpcsfile->getDeclarationName($currScope);
        $methodName = $phpcsfile->getDeclarationName($stackptr);

        // Is this a magic method. IE. is prefixed with "__".
        if (preg_match('|^__|', $methodName) !== 0) {
            $magicPart = substr($methodName, 2);
            if (in_array($magicPart, $this->_magicMethods) === false) {
                 $error = "Method name \"$className::$methodName\" is invalid; only PHP magic methods should be prefixed with a double underscore";
                 $phpcsfile->adderror($error, $stackptr);
            }

            return;
        }

        // PHP4 constructors are allowed to break our rules.
        if ($methodName === $className) {
            return;
        }

        // PHP4 destructors are allowed to break our rules.
        if ($methodName === '_'.$className) {
            return;
        }

        $methodProps    = $phpcsfile->getMethodProperties($stackptr);
        $isPublic       = ($methodProps['scope'] === 'private') ? false : true;
        $scope          = $methodProps['scope'];
        $scopeSpecified = $methodProps['scope_specified'];

        // Only lower-case accepted
        if (preg_match('/[A-Z]+/', $methodName)) {
            if ($scopeSpecified === true) {
                $error = ucfirst($scope)." method name \"$className::$methodName\" must be in lower-case letters only";
            } else {
                $error = "Method name \"$className::$methodName\" must be in lower-case letters only";
            }

            $phpcsfile->adderror($error, $stackptr);
            return;
        }

        // No numbers accepted
        if (preg_match('/[0-9]+/', $methodName)) {
            if ($scopeSpecified === true) {
                $error = ucfirst($scope)." method name \"$className::$methodName\" must only contain letters";
            } else {
                $error = "Method name \"$className::$methodName\" must only contain letters";
            }

            $phpcsfile->adderror($error, $stackptr);
            return;
        }

    }


    /**
     * Processes the tokens outside the scope.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being processed.
     * @param int                  $stackptr  The position where this token was
     *                                        found.
     *
     * @return void
     */
    protected function processtokenOutsideScope(PHP_CodeSniffer_File $phpcsfile, $stackptr)
    {
        $functionName = $phpcsfile->getDeclarationName($stackptr);

        // Is this a magic function. IE. is prefixed with "__".
        if (preg_match('|^__|', $functionName) !== 0) {
            $magicPart = substr($functionName, 2);
            if (in_array($magicPart, $this->_magicFunctions) === false) {
                 $error = "Function name \"$functionName\" is invalid; only PHP magic methods should be prefixed with a double underscore";
                 $phpcsfile->adderror($error, $stackptr);
            }

            return;
        }

        // Only lower-case accepted
        if (preg_match('/[A-Z]+/', $functionName)) {
            $error = "function name \"$functionName\" must be lower-case letters only";

            $phpcsfile->adderror($error, $stackptr);
            return;
        }

        // Only letters accepted
        if (preg_match('/[0-9]+/', $functionName)) {
            $error = "function name \"$functionName\" must only contain letters";

            $phpcsfile->adderror($error, $stackptr);
            return;
        }

    }


}

?>
