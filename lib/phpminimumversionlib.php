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

// MOODLE_INTERNAL check intentionally missing to allow this to be used more widely!

/**
 * A set of PHP-compatible convenience functions to check Moodle minimum PHP version in
 * a unified place.
 *
 * PLEASE NOTE: This file is made to be both php-version compatible and without requirement on
 * any moodle functions or installation so it can be used in installer or incompatible PHP versions.
 *
 * @package    core
 * @copyright  2017 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Require our minimum php version or halt execution if requirement not met.
 * @return void Execution is halted if version is not met.
 */
function moodle_require_minimum_php_version() {
    // PLEASE NOTE THIS FUNCTION MUST BE COMPATIBLE WITH OLD UNSUPPORTED VERSIONS OF PHP!
    moodle_minimum_php_version_is_met(true);
}

/**
 * Tests the current PHP version against Moodle's minimum requirement. When requirement
 * is not met returns false or halts execution depending $haltexecution param.
 *
 * @param bool $haltexecution Should execution be halted when requirement not met? Defaults to false.
 * @return bool returns true if requirement is met (false if not)
 */
function moodle_minimum_php_version_is_met($haltexecution = false) {
    // PLEASE NOTE THIS FUNCTION MUST BE COMPATIBLE WITH OLD UNSUPPORTED VERSIONS OF PHP.
    // Do not use modern php features or Moodle convenience functions (e.g. localised strings).

    $minimumversion = '7.1.0';
    $moodlerequirementchanged = '3.7';

    if (version_compare(PHP_VERSION, $minimumversion) < 0) {
        if ($haltexecution) {
            $error = "Moodle ${moodlerequirementchanged} or later requires at least PHP ${minimumversion} "
                . "(currently using version " . PHP_VERSION .").\n"
                . "Some servers may have multiple PHP versions installed, are you using the correct executable?\n";

            // Our CLI scripts define CLI_SCRIPT before running this test, so make use of
            // to send error on STDERR.
            if (defined('CLI_SCRIPT') && defined('STDERR')) {
                fwrite(STDERR, $error);
            } else {
                echo $error;
            }
            exit(1);
        } else {
            return false;
        }
    }
    return true;
}

// DO NOT ADD EXTRA FUNCTIONS TO THIS FILE!!
// This file must be functioning on all versions of PHP, extra functions belong elsewhere.
