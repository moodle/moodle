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

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff as GenericForbiddenFunctionsSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Sniff for debugging and other functions that we don't want used in finished code.
 *
 * Note that strictly speaking we don't need to extend the Generic Sniff,
 * just configure it in the ruleset.xml like this, for example:
 *
 * <rule ref="Generic.PHP.ForbiddenFunctions">
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
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ForbiddenFunctionsSniff extends GenericForbiddenFunctionsSniff {

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array<string, string|null>
     */
    public $forbiddenFunctions = [
        // Usual development debugging functions.
        'sizeof'       => 'count',
        'delete'       => 'unset',
        'error_log'    => 'debugging',
        'print_r'      => null,
        'print_object' => null,
        // Dangerous functions. From coding style.
        'extract'      => null,
    ];
}
