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
 * Popup message processor - stores the message to be shown using the message popup
 *
 * @copyright Luis Rodrigues
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 * @package message
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php'); //included from messagelib (how to fix?)
require_once($CFG->dirroot.'/message/output/lib.php');

class message_output_popup extends message_output{

    /**
     * Process the popup message.
     * The popup doesn't send data only saves in the database for later use,
     * the popup_interface.php takes the message from the message table into
     * the message_read.
     * @param object $message the message to be sent
     * @return true if ok, false if error
     */
    public function send_message($message) {
        global $DB;

        //put the process record into db
        $processor = $DB->get_record('message_processors', array('name'=>'popup'));
        $procmessage = new object();
        $procmessage->unreadmessageid = $message->id;
        $procmessage->processorid     = $processor->id;

        $DB->insert_record('message_working', $procmessage);

        //should only save this message for later delivery
        return true;
    }

    function config_form($preferences) {
        return '<table>'.
               '<tr><td align="right">'.get_string('blocknoncontacts', 'message').':</td><td><input type="checkbox" name="blocknoncontacts" '.($preferences->blocknoncontacts==1?" checked=\"checked\"":"").' /></td></tr>'.
               '<tr><td align="right">'.get_string('beepnewmessage', 'message').':</td><td><input type="checkbox" name="beepnewmessage" '.($preferences->beepnewmessage==1?" checked=\"checked\"":"").' /></td></tr>'.
               '</table>';
    }

    public function process_form($form, &$preferences) {
        $preferences['message_blocknoncontacts']  = !empty($form->blocknoncontacts)?1:0;
        $preferences['message_beepnewmessage']    = !empty($form->beepnewmessage)?1:0;

        return true;
    }
    public function load_data(&$preferences, $userid) {
        global $USER;

        $preferences->blocknoncontacts  =  get_user_preferences( 'message_blocknoncontacts', '', $userid);
        $preferences->beepnewmessage    =  get_user_preferences( 'message_beepnewmessage', '', $userid);

        return true;
    }
}