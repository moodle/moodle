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
 * Contains a base class for extension by message processors
 *
 * @package   core_message
 * @copyright Luis Rodrigues
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Base message processor class for extension by message processors
 *
 * @package   core_message
 * @copyright 2008 Luis Rodrigues
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class message_output {
    /**
     * Process a message received by a user
     *
     * @see message_send()
     * @param stdClass $message The event data submitted by the message provider to message_send() plus $eventdata->savedmessageid
     */
    abstract public function send_message($message);

    /**
     * Load the config data from database to put on the config form on the messaging preferences page
     *
     * @param array $preferences Array of user preferences
     * @param int $userid The user ID
     */
    abstract public function load_data(&$preferences, $userid);

    /**
     * Create necessary fields in the config form on the messaging preferences page
     *
     * @param array $preferences An array of user preferences
     */
    abstract public function config_form($preferences);

    /**
     * Parse the submitted form and save data into an array of user preferences
     *
     * @param stdClass $form preferences form class
     * @param array $preferences preferences array
     */
    abstract public function process_form($form, &$preferences);

    /**
     * Are the message processor's system settings configured?
     *
     * @return bool True if all necessary config settings been entered
     */
    public function is_system_configured() {
        return true;
    }

    /**
     * Are the message processor's user specific settings configured?
     *
     * @param  stdClass $user the user object, defaults to $USER.
     * @return bool True if the user has all necessary settings in their messaging preferences
     */
    public function is_user_configured($user = null) {
        return true;
    }

    /**
     * Returns the message processors default settings
     * Should the processor be enabled in users by default?
     * Is enabling it disallowed, permitted or forced?
     *
     * @return int The Default message output settings expressed as a bit mask
     *         MESSAGE_DEFAULT_ENABLED + MESSAGE_PERMITTED
     */
    public function get_default_messaging_settings() {
        return MESSAGE_PERMITTED;
    }

    /**
     * Returns true if message can be sent to fake/internal user as well.
     * If message_output support message to be sent to fake user, then it should return true, like email.
     *
     * @return bool
     */
    public function can_send_to_any_users() {
        return false;
    }

    /**
     * Returns true if this processor has configurable message preferences. This is
     * distinct from notification preferences.
     *
     * @return bool
     */
    public function has_message_preferences() {
        return true;
    }

    /**
     * Determines if this processor should process a message regardless of user preferences or site settings.
     *
     * @return bool
     */
    public function force_process_messages() {
        return false;
    }

    /**
     * Allow processors to perform cleanup tasks for all notifications by overriding this method
     *
     * @since Moodle 3.9
     * @param int $notificationdeletetime
     * @return void
     */
    public function cleanup_all_notifications(int $notificationdeletetime): void {
        return;
    }

    /**
     * Allow processors to perform cleanup tasks for read notifications by overriding this method
     *
     * @since Moodle 3.9
     * @param int $notificationdeletetime
     * @return void
     */
    public function cleanup_read_notifications(int $notificationdeletetime): void {
        return;
    }
}
