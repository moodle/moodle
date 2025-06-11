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

namespace tool_mobile\local\hooks\output;
use core\session\utility\cookie_helper;

/**
 * Allows plugins to modify headers.
 *
 * @package    tool_mobile
 * @copyright  2024 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_http_headers {
    /**
     * Callback to allow modify headers.
     *
     * @param \core\hook\output\before_http_headers $hook
     */
    public static function callback(\core\hook\output\before_http_headers $hook): void {
        global $CFG;

        // Set Partitioned and Secure attributes to the MoodleSession cookie if the user is using the Moodle app.
        if (\core_useragent::is_moodle_app()) {
            cookie_helper::add_attributes_to_cookie_response_header('MoodleSession'.$CFG->sessioncookie, ['Secure', 'Partitioned']);
        }
    }
}
