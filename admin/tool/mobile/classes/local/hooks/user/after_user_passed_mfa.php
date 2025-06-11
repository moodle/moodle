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

/**
 * Handles mobile app launches when third-party auth plugins are put in front of MFA.
 *
 * @package    tool_mobile
 * @copyright  2024 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class after_user_passed_mfa {

    /**
     * Callback to recover $SESSION->wantsurl.
     *
     * @param \tool_mfa\hook\after_user_passed_mfa $hook
     */
    public static function callback(\tool_mfa\hook\after_user_passed_mfa $hook): void {
        global $SESSION, $CFG;

        // Check if the user is doing a mobile app launch, if that's the case, ensure $SESSION->wantsurl is correctly set.
        if (!NO_MOODLE_COOKIES && !empty($_COOKIE['tool_mobile_launch'])) {
            if (empty($SESSION->wantsurl) || strpos($SESSION->wantsurl, '/tool/mobile/launch.php') === false) {

                $params = json_decode($_COOKIE['tool_mobile_launch'], true);
                $SESSION->wantsurl = (new \moodle_url("/$CFG->admin/tool/mobile/launch.php", $params))->out(false);
                $SESSION->tool_mfa_has_been_redirected = true;  // Indicate MFA that they need to follow $SESSION->wantsurl.
            }
            // Invalidate cookie as we won't be needing it anymore.
            unset($_COOKIE['tool_mobile_launch']);
            if (!headers_sent()) {  // Just be very cautios as this is a critical code.
                setcookie('tool_mobile_launch', '', -1, $CFG->sessioncookiepath);
            }
        }
    }
}
