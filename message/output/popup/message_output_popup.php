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
     * Do nothing on send_message.
     *
     * @param object $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     * @return true if ok, false if error
     */
    public function send_message($eventdata) {
        global $DB;

        //hold onto the popup processor id because /admin/cron.php sends a lot of messages at once
        static $processorid = null;

        //prevent users from getting popup notifications of messages to themselves (happens with forum notifications)
        if ($eventdata->userfrom->id != $eventdata->userto->id) {
            if (empty($processorid)) {
                $processor = $DB->get_record('message_processors', array('name'=>'popup'));
                $processorid = $processor->id;
            }
            $procmessage = new stdClass();
            $procmessage->unreadmessageid = $eventdata->savedmessageid;
            $procmessage->processorid     = $processorid;

            //save this message for later delivery
            $DB->insert_record('message_working', $procmessage);

            if ($eventdata->notification) {
                if (!$DB->record_exists('message_popup', ['messageid' => $eventdata->savedmessageid, 'isread' => 0])) {
                    $record = new StdClass();
                    $record->messageid = $eventdata->savedmessageid;
                    $record->isread = 0;

                    $DB->insert_record('message_popup', $record);
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
     * Handles the message_viewed event to keep data in sync.
     *
     * @param \core\event\base $event The event data
     */
    public static function message_viewed(\core\event\base $event) {
        global $DB;

        if ($record = $DB->get_record('message_popup', ['messageid' => $event->other['messageid']])) {
            // The id can change when the moving to the message_read table.
            $record->messageid = $event->objectid;
            $record->isread = 1;
            $DB->update_record('message_popup', $record);
        }
    }
}
