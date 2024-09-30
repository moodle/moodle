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
 * Library functions to facilitate the use of ajax JavaScript in Moodle.
 *
 * @package   core
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @deprecated since Moodle 4.3
 */
#[\core\attribute\deprecated('\'core_user/repository\' module', since: '4.3', mdl: 'MDL-76974', final: true)]
function user_preference_allow_ajax_update() {
    \core\deprecation::emit_deprecation_if_present(__FUNCTION__);
}

/**
 * Starts capturing output whilst processing an AJAX request.
 *
 * This should be used in combination with ajax_check_captured_output to
 * report any captured output to the user.
 *
 * @return Boolean Returns true on success or false on failure.
 */
function ajax_capture_output() {
    // Start capturing output in case of broken plugins.
    return ob_start();
}

/**
 * Check captured output for content. If the site has a debug level of
 * debugdeveloper set, and the content is non-empty, then throw a coding
 * exception which can be captured by the Y.IO request and displayed to the
 * user.
 *
 * @return Any output that was captured.
 */
function ajax_check_captured_output() {
    global $CFG;

    // Retrieve the output - there should be none.
    $output = ob_get_contents();
    ob_end_clean();

    if (!empty($output)) {
        $message = 'Unexpected output whilst processing AJAX request. ' .
                'This could be caused by trailing whitespace. Output received: ' .
                var_export($output, true);
        if ($CFG->debugdeveloper && !empty($output)) {
            // Only throw an error if the site is in debugdeveloper.
            throw new coding_exception($message);
        }
        error_log('Potential coding error: ' . $message);
    }
    return $output;
}
