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
 * @package   lib-pear-php-codesniffer
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (is_file(dirname(__FILE__).'/../MoodleCodeSniffer.php') === true) {
    include_once(dirname(__FILE__).'/../MoodleCodeSniffer.php');
} else {
    include_once('PHP/MoodleCodeSniffer.php');
}

require_once('PHP/CodeSniffer/CLI.php');

/**
 * A class to process command line phpcs scripts. Modified for use within Moodle
 *
 * @category  lib-pear-php-codesniffer
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_codesniffer_cli extends php_codesniffer_cli {
    /**
     * Modified to return Moodle only
     *
     * @param string $standard The standard to validate.
     *
     * @return string
     */
    public function validatestandard($standard) {
        return 'Moodle';
    }

    /**
     * Prints out the usage information for this script.
     *
     * Modified by removing the --standard option
     *
     * @return void
     */
    public function printusage() {
        echo 'Usage: phpcs [-nwlvi] [--report=<report>]'.PHP_EOL;
        echo '    [--config-set key value] [--config-delete key] [--config-show]'.PHP_EOL;
        echo '    [--generator=<generator>] [--extensions=<extensions>]'.PHP_EOL;
        echo '    [--ignore=<patterns>] [--tab-width=<width>] <file> ...'.PHP_EOL;
        echo '        -n           Do not print warnings'.PHP_EOL;
        echo '        -w           Print both warnings and errors (on by default)'.PHP_EOL;
        echo '        -l           Local directory only, no recursion'.PHP_EOL;
        echo '        -v[v][v]     Print verbose output'.PHP_EOL;
        echo '        -i           Show a list of installed coding standards'.PHP_EOL;
        echo '        --help       Print this help message'.PHP_EOL;
        echo '        --version    Print version information'.PHP_EOL;
        echo '        <file>       One or more files and/or directories to check'.PHP_EOL;
        echo '        <extensions> A comma separated list of file extensions to check'.PHP_EOL;
        echo '                     (only valid if checking a directory)'.PHP_EOL;
        echo '        <patterns>   A comma separated list of patterns that are used'.PHP_EOL;
        echo '                     to ignore directories and files'.PHP_EOL;
        echo '        <width>      The number of spaces each tab represents'.PHP_EOL;
        echo '        <generator>  The name of a doc generator to use'.PHP_EOL;
        echo '                     (forces doc generation instead of checking)'.PHP_EOL;
        echo '        <report>     Print either the "full", "xml", "checkstyle",'.PHP_EOL;
        echo '                     "csv" or "summary" report'.PHP_EOL;
        echo '                     (the "full" report is printed by default)'.PHP_EOL;

    }//end printUsage()

}
