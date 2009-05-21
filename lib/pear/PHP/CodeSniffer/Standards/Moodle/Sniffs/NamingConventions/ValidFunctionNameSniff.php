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
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-namingconventions
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_namingconventions_validfunctionnamesniff extends php_codesniffer_standards_abstractscopesniff {

    /**
     * A list of all PHP magic methods.
     *
     * @var array
     */
    private $_magicmethods = array(
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
    private $_magicfunctions = array('autoload');


    /**
     * Constructs a moodle_sniffs_namingconventions_validfunctionnamesniff.
     */
    public function __construct() {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION), true);
    }


    /**
     * Processes the tokens within the scope.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being processed.
     * @param int                  $stackptr  The position where this token was
     *                                        found.
     * @param int                  $currscope The position of the current scope.
     *
     * @return void
     */
    protected function processtokenwithinscope(PHP_CodeSniffer_File $phpcsfile, $stackptr, $currscope) {
        $classname  = $phpcsfile->getdeclarationname($currscope);
        $methodname = $phpcsfile->getdeclarationname($stackptr);

        // Is this a magic method. IE. is prefixed with "__".
        if (preg_match('|^__|', $methodname) !== 0) {
            $magicpart = substr($methodname, 2);

            if (in_array($magicpart, $this->_magicmethods) === false) {
                 $error = "method name \"$classname::$methodname\" is invalid; " .
                          'only PHP magic methods should be prefixed with a double underscore';
                 $phpcsfile->adderror($error, $stackptr);
            }

            return;
        }

        // PHP4 constructors are allowed to break our rules.
        if ($methodname === $classname) {
            return;
        }

        // PHP4 destructors are allowed to break our rules.
        if ($methodname === '_'.$classname) {
            return;
        }

        $methodprops    = $phpcsfile->getmethodproperties($stackptr);
        $ispublic       = ($methodprops['scope'] === 'private') ? false : true;
        $scope          = $methodprops['scope'];
        $scopespecified = $methodprops['scope_specified'];

        // Only lower-case accepted
        if (preg_match('/[A-Z]+/', $methodname)) {

            if ($scopespecified === true) {
                $error = ucfirst($scope)." method name \"$classname::$methodname\" must be in lower-case letters only";
            } else {
                $error = "method name \"$classname::$methodname\" must be in lower-case letters only";
            }

            $phpcsfile->adderror($error, $stackptr);
            return;
        }

        // No numbers accepted
        if (preg_match('/[0-9]+/', $methodname)) {

            if ($scopespecified === true) {
                $error = ucfirst($scope)." method name \"$classname::$methodname\" must only contain letters";
            } else {
                $error = "Method name \"$classname::$methodname\" must only contain letters";
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
    protected function processtokenoutsidescope(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $functionname = $phpcsfile->getdeclarationname($stackptr);

        // Is this a magic function. IE. is prefixed with "__".
        if (preg_match('|^__|', $functionname) !== 0) {
            $magicpart = substr($functionname, 2);

            if (in_array($magicpart, $this->_magicfunctions) === false) {
                 $error = "Function name \"$functionname\" is invalid; " .
                          'only PHP magic methods should be prefixed with a double underscore';
                 $phpcsfile->adderror($error, $stackptr);
            }

            return;
        }

        // Only lower-case accepted
        if (preg_match('/[A-Z]+/', $functionname)) {
            $error = "function name \"$functionname\" must be lower-case letters only";

            $phpcsfile->adderror($error, $stackptr);
            return;
        }

        // Only letters accepted
        if (preg_match('/[0-9]+/', $functionname)) {
            $error = "function name \"$functionname\" must only contain letters";

            $phpcsfile->adderror($error, $stackptr);
            return;
        }

    }


}
