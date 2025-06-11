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

namespace enrol_manual;

/**
 * Hook callbacks to get the enrolment information.
 *
 * @package    enrol_manual
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_enrolment_callbacks {

    /**
     * Callback for the user_enrolment hook.
     *
     * @param \core_enrol\hook\after_user_enrolled $hook
     */
    public static function send_course_welcome_message(\core_enrol\hook\after_user_enrolled $hook): void {
        $instance = $hook->get_enrolinstance();
        // Send welcome message.
        if ($instance->enrol == 'manual' && $instance->customint1 && $instance->customint1 !== ENROL_DO_NOT_SEND_EMAIL) {
            $plugin = enrol_get_plugin($instance->enrol);
            $plugin->send_course_welcome_message_to_user(
                instance: $instance,
                userid: $hook->get_userid(),
                sendoption: $instance->customint1,
                message: $instance->customtext1,
                roleid: $hook->roleid,
            );
        }
    }
}
