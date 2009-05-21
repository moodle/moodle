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
 * Subclass of lib/pear/PHP/CodeSniffer/CLI.php
 *
 * Simple modifications to the CLI class to only use the Moodle Standard
 *
 * @package   lib-pear-php-codesniffer-standards-moodle
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * Moodle Coding Standard.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version Release: @package_version@
 */
class php_codesniffer_standards_moodle_moodlecodingstandard extends php_codesniffer_standards_codingstandard {
    /**
     * To include additional sniffs in this standard, add their paths to this method's return array
     *
     * @return array
     */
    public function getincludedsniffs() {
        return array();
    }

    /**
     * To exclude included sniffs from this standard, add their paths to this method's return array
     *
     * @return array
     */
    public function getexcludedsniffs() {
        return array('Moodle/Sniffs/CodeAnalysis');
    }
}
