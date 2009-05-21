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
 * moodle_sniffs_functions_functiondeclarationsniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-functions
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}

/**
 * moodle_sniffs_functions_functiondeclarationsniff.
 *
 * Checks the function declaration is correct.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_functions_functiondeclarationsniff extends php_codesniffer_standards_abstractpatternsniff {


    /**
     * Returns an array of patterns to check are correct.
     *
     * @return array
     */
    protected function getpatterns() {
        return array(
                'function abc(...) {',
                'abstract function abc(...);'
               );
    }
}
