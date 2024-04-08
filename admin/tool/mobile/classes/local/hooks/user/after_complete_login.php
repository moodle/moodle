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

namespace tool_mobile\local\hooks\user;
use core\session\utility\cookie_helper;

/**
 * Handles mobile app launches when a third-party auth plugin did not properly set $SESSION->wantsurl.
 *
 * @package    tool_mobile
 * @copyright  2024 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class after_complete_login {
    /**
     * Callback to recover $SESSION->wantsurl.
     *
     * @param \core\hook\user\after_complete_login $hook
     */
    public static function callback(\core\hook\user\after_complete_login $hook): void {
        global $SESSION, $CFG;

        // Check if the user is doing a mobile app launch, if that's the case, ensure $SESSION->wantsurl is correctly set.
        if (!NO_MOODLE_COOKIES && !empty($_COOKIE['tool_mobile_launch'])) {
            if (empty($SESSION->wantsurl) || strpos($SESSION->wantsurl, '/tool/mobile/launch.php') === false) {
                $params = json_decode($_COOKIE['tool_mobile_launch'], true);
                $SESSION->wantsurl = (new \moodle_url("/$CFG->admin/tool/mobile/launch.php", $params))->out(false);
            }
        }

        // Set Partitioned and Secure attributes to the MoodleSession cookie if the user is using the Moodle app.
        if (\core_useragent::is_moodle_app()) {
            cookie_helper::add_attributes_to_cookie_response_header('MoodleSession'.$CFG->sessioncookie, ['Secure', 'Partitioned']);
        }
    }
}
