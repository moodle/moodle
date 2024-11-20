<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
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
 * Popup message processor
 *
 * @package   message_popup
 * @copyright 2008 Luis Rodrigues
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

require_once(__DIR__ . '/../../../config.php'); //included from messagelib (how to fix?)
require_once($CFG->dirroot.'/message/output/lib.php');

/**
 * The popup message processor
 *
 * @package   message_popup
 * @copyright 2008 Luis Rodrigues and Martin Dougiamas
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_output_popup extends message_output {

    /**
     * Adds notifications to the 'message_popup_notifications' table if applicable.
     *
     * The reason for this is because we may not want to show all notifications in the notification popover. This
     * can happen if the popup processor was disabled when the notification was sent. If the processor is disabled this
     * function is never called so the notification will never be added to the 'message_popup_notifications' table.
     * Essentially this table is used to filter what notifications to display from the 'notifications' table.
     *
     * @param object $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     * @return true if ok, false if error
     */
    public function send_message($eventdata) {
        global $DB;

        // Prevent users from getting popup notifications from themselves (happens with forum notifications).
        if ($eventdata->notification) {
            if (($eventdata->userfrom->id != $eventdata->userto->id) ||
                (isset($eventdata->anonymous) && $eventdata->anonymous)) {
                if (!$DB->record_exists('message_popup_notifications', ['notificationid' => $eventdata->savedmessageid])) {
                    $record = new stdClass();
                    $record->notificationid = $eventdata->savedmessageid;

                    $DB->insert_record('message_popup_notifications', $record);
                }
            }
        }

        return true;
    }

    /**
     * Creates necessary fields in the messaging config form.
     *
     * @param array $preferences An array of user preferences
     */
    function config_form($preferences) {
        return null;
    }

    /**
     * Parses the submitted form data and saves it into preferences array.
     *
     * @param stdClass $form preferences form class
     * @param array $preferences preferences array
     */
    public function process_form($form, &$preferences) {
        return true;
    }

    /**
     * Loads the config data from database to put on the form during initial form display
     *
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    public function load_data(&$preferences, $userid) {
        global $USER;
        return true;
    }

    /**
     * Don't show this processor on the message preferences page. The user can't disable
     * the notifications for user-to-user messaging.
     *
     * @return bool
     */
    public function has_message_preferences() {
        return false;
    }

    /**
     * Determines if this processor should process a message regardless of user preferences or site settings.
     *
     * @return bool
     */
    public function force_process_messages() {
        global $CFG;

        return !empty($CFG->messaging);
    }

    /**
     * Remove all popup notifications up to specified time
     *
     * @param int $notificationdeletetime
     * @return void
     */
    public function cleanup_all_notifications(int $notificationdeletetime): void {
        global $DB;

        $DB->delete_records_select('message_popup_notifications',
            'notificationid IN (SELECT id FROM {notifications} WHERE timecreated < ?)', [$notificationdeletetime]);
    }

    /**
     * Remove read popup notifications up to specified time
     *
     * @param int $notificationdeletetime
     * @return void
     */
    public function cleanup_read_notifications(int $notificationdeletetime): void {
        global $DB;

        $DB->delete_records_select('message_popup_notifications',
            'notificationid IN (SELECT id FROM {notifications} WHERE timeread < ?)', [$notificationdeletetime]);
    }
}
