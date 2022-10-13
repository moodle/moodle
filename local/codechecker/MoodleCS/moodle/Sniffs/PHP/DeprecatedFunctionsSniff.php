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

namespace MoodleHQ\MoodleCS\moodle\Sniffs\PHP;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DeprecatedFunctionsSniff as GenericDeprecatedFunctionsSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Sniff for various Moodle deprecated functions which uses should be replaced.
 *
 * Note that strictly speaking we don't need to extend the Generic Sniff,
 * just configure it in the ruleset.xml like this, for example:
 *
 * <rule ref="Generic.PHP.DeprecatedFunctions">
 *   <properties>
 *     <property name="forbiddenFunctions" type="array">
 *       <element key="xxx" value="yyy"/>
 *     </property>
 *   </properties>
 * </rule>
 *
 * But we have decided to, instead, extend and keep the functions
 * together with the Sniff. Also, this enables to test the Sniff
 * without having to perform any configuration in the fixture files.
 * (because unit tests DO NOT parse the ruleset.xml details, like
 * properties, excludes... and other info).
 *
 * @package    local_codechecker
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class DeprecatedFunctionsSniff extends GenericDeprecatedFunctionsSniff {

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var boolean
     */
    public $error = false; // Consider deprecations just warnings.

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array<string, string|null>
     */
    public $forbiddenFunctions = [
        // Moodle deprecated functions.
        'print_error' => 'throw new moodle_exception() (using lang strings only if meant to be shown to final user)',
        // Note that, apart from these Moodle explicitly set functions, also,  all the internal PHP functions
        // that are deprecated are detected automatically, {@see Generic\Sniffs\PHP\DeprecatedFunctionsSniff}.
    ];

    /**
     * Generates the error or warning for this sniff.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the forbidden function
     *                                               in the token array.
     * @param string                      $function  The name of the forbidden function.
     * @param string                      $pattern   The pattern used for the match.
     *
     * @return void
     *
     * @todo: This method can be removed once/if this PR accepted:
     *        https://github.com/squizlabs/PHP_CodeSniffer/pull/3295
     */
    protected function addError($phpcsFile, $stackPtr, $function, $pattern=null) {
        $data  = [$function];
        $error = 'Function %s() has been deprecated';
        $type  = 'Deprecated';

        if ($pattern === null) {
            $pattern = strtolower($function);
        }

        if ($this->forbiddenFunctions[$pattern] !== null
            && $this->forbiddenFunctions[$pattern] !== 'null'
        ) {
            $type  .= 'WithAlternative';
            $data[] = $this->forbiddenFunctions[$pattern];
            $error .= '; use %s() instead';
        }

        if ($this->error === true) {
            $phpcsFile->addError($error, $stackPtr, $type, $data);
        } else {
            $phpcsFile->addWarning($error, $stackPtr, $type, $data);
        }

    }//end addError()
}
