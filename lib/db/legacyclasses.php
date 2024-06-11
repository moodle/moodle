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
 * This file contains mappings for legacy classes that do not fit the standard class naming conventions.
 *
 * In time these classes should be renamed to fit the standard class naming conventions but this is not an overnight process.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Like other files in the db directory this file uses an array.
// The old class name is the key, the path to the file containing the class is the vlaue.
// The array must be called $legacyclasses.
$legacyclasses = [
    \bootstrap_renderer::class => 'output/bootstrap_renderer.php',
    \coding_exception::class => 'exception/coding_exception.php',
    \file_serving_exception::class => 'exception/file_serving_exception.php',
    \invalid_dataroot_permissions::class => 'exception/invalid_dataroot_permissions.php',
    \invalid_parameter_exception::class => 'exception/invalid_parameter_exception.php',
    \invalid_response_exception::class => 'exception/invalid_response_exception.php',
    \invalid_state_exception::class => 'exception/invalid_state_exception.php',
    \moodle_exception::class => 'exception/moodle_exception.php',
    \require_login_exception::class => 'exception/require_login_exception.php',
    \require_login_session_timeout_exception::class => 'exception/require_login_session_timeout_exception.php',
    \required_capability_exception::class => 'exception/required_capability_exception.php',
    \webservice_parameter_exception::class => 'exception/webservice_parameter_exception.php',
];
