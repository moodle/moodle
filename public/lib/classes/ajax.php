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

namespace core;

use core\exception\coding_exception;

/**
 * Ajax helpers.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ajax {
    /**
     * Starts capturing output whilst processing an AJAX request.
     *
     * This should be used in combination with ajax_check_captured_output to
     * report any captured output to the user.
     *
     * @return bool Returns true on success or false on failure.
     */
    public static function capture_output(): bool {
        // Start capturing output in case of broken plugins.
        return ob_start();
    }

    /**
     * Check captured output for content. If the site has a debug level of
     * debugdeveloper set, and the content is non-empty, then throw a coding
     * exception which can be captured by the Y.IO request and displayed to the
     * user.
     *
     * @return bool|string Any output that was captured.
     * @throws coding_exception If unexpected output is found and the debug level is set to debugdeveloper.
     */
    public static function check_captured_output(): bool|string {
        global $CFG;

        // Retrieve the output - there should be none.
        $output = ob_get_contents();
        ob_end_clean();

        if (!empty($output)) {
            $message = 'Unexpected output whilst processing AJAX request. ';
            $message .= 'This could be caused by trailing whitespace. Output received: ';
            $message .= var_export($output, true);
            if ($CFG->debugdeveloper) {
                // Only throw an error if the site is in debugdeveloper.
                throw new coding_exception($message);
            }
            error_log('Potential coding error: ' . $message); // phpcs:ignore moodle.PHP.ForbiddenFunctions.FoundWithAlternative
        }

        return $output;
    }
}
