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
 * You need to call this function if you wish to use the set_user_preference method in javascript_static.php, to white-list the
 * preference you want to update from JavaScript, and to specify the type of cleaning you expect to be done on values.
 *
 * @package  core
 * @category preference
 * @param    string          $name      the name of the user_perference we should allow to be updated by remote calls.
 * @param    integer         $paramtype one of the PARAM_{TYPE} constants, user to clean submitted values before set_user_preference is called.
 * @return   null
 */
function user_preference_allow_ajax_update($name, $paramtype) {
    global $USER, $PAGE;

    // Record in the session that this user_preference is allowed to updated remotely.
    $USER->ajax_updatable_user_prefs[$name] = $paramtype;
}

/**
 * Returns whether ajax is enabled/allowed or not.
 * @param array $browsers optional list of alowed browsers, empty means use default list
 * @return bool
 */
function ajaxenabled(array $browsers = null) {
    global $CFG;

    if (!empty($browsers)) {
        $valid = false;
        foreach ($browsers as $brand => $version) {
            if (check_browser_version($brand, $version)) {
                $valid = true;
            }
        }

        if (!$valid) {
            return false;
        }
    }

    $ie = check_browser_version('MSIE', 6.0);
    $ff = check_browser_version('Gecko', 20051106);
    $op = check_browser_version('Opera', 9.0);
    $sa = check_browser_version('Safari', 412);
    $ch = check_browser_version('Chrome', 6);

    if (!$ie && !$ff && !$op && !$sa && !$ch) {
        /** @see http://en.wikipedia.org/wiki/User_agent */
        // Gecko build 20051107 is what is in Firefox 1.5.
        // We still have issues with AJAX in other browsers.
        return false;
    }

    if (!empty($CFG->enableajax)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Starts capturing output whilst processing an AJAX request.
 *
 * This should be used in combination with ajax_check_captured_output to
 * report any captured output to the user.
 *
 * @retrun Boolean Returns true on success or false on failure.
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

    if ($CFG->debug == DEBUG_DEVELOPER && !empty($output)) {
        // Only throw an error if the site is in debugdeveloper.
        throw new coding_exception('Unexpected output whilst processing AJAX request. ' .
                'This could be caused by trailing whitespace. Output received: ' .
                var_export($output, true));
    }
    return $output;
}
