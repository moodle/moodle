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

defined('MOODLE_INTERNAL') || die();

/**
 * Library functions to facilitate the use of ajax JavaScript in Moodle.
 *
 * @package   core
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Starts capturing output whilst processing an AJAX request.
 *
 * This should be used in combination with ajax_check_captured_output to
 * report any captured output to the user.
 *
 * @return bool Returns true on success or false on failure.
 * @deprecated since Moodle 5.1, use \core\ajax::capture_output() instead.
 */
#[\core\attribute\deprecated(
    replacement: "\core\ajax::capture_output()",
    since: '5.1',
    mdl: 'MDL-86168',
)]
function ajax_capture_output(): bool {
    \core\deprecation::emit_deprecation(__FUNCTION__);
    return \core\ajax::capture_output();
}

/**
 * Check captured output for content. If the site has a debug level of
 * debugdeveloper set, and the content is non-empty, then throw a coding
 * exception which can be captured by the Y.IO request and displayed to the
 * user.
 *
 * @return bool|string Any output that was captured.
 * @deprecated since Moodle 5.1, use \core\ajax::check_captured_output() instead.
 */
#[\core\attribute\deprecated(
    replacement: "\core\ajax::check_captured_output()",
    since: '5.1',
    mdl: 'MDL-86168',
)]
function ajax_check_captured_output(): bool|string {
    \core\deprecation::emit_deprecation(__FUNCTION__);
    return \core\ajax::check_captured_output();
}
