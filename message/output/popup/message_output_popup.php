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
               '<tr><td align="right">'.get_string('showmessagewindow', 'message').':</td><td><input type="checkbox" name="showmessagewindow" '.($preferences->showmessagewindow==1?" checked=\"checked\"":"").' /></td></tr>'.
               '<tr><td align="right">'.get_string('blocknoncontacts', 'message').':</td><td><input type="checkbox" name="blocknoncontacts" '.($preferences->blocknoncontacts==1?" checked=\"checked\"":"").' /></td></tr>'.
               '<tr><td align="right">'.get_string('beepnewmessage', 'message').':</td><td><input type="checkbox" name="beepnewmessage" '.($preferences->beepnewmessage==1?" checked=\"checked\"":"").' /></td></tr>'.
               '<tr><td align="right">'.get_string('htmleditor').':</td><td><input type="checkbox" name="usehtmleditor" '.($preferences->usehtmleditor==1?" checked=\"checked\"":"").' /></td></tr>'.
               '<tr><td align="right">'.get_string('noframesjs', 'message').':</td><td><input type="checkbox" name="noframesjs" '.($preferences->noframesjs==1?" checked=\"checked\"":"").' /></td></tr>'.
               '<tr><td align="right">'.get_string('emailmessages', 'message').':</td><td><input type="checkbox" name="emailmessages" '.($preferences->emailmessages==1?" checked=\"checked\"":"").' /></td></tr>'.
               '<tr><td align="right">'.get_string('formorethan', 'message').':</td><td><input type="text" name="emailtimenosee" id="emailtimenosee" size="2" value="'.$preferences->emailtimenosee.'" /> '.get_string('mins').'</td></tr>'.
               '<tr><td align="right">'.get_string('email').':</td><td><input type="text" name="emailaddress" id="emailaddress" size="20" value="'.$preferences->emailaddress.'" /></td></tr>'.
               '<tr><td align="right">'.get_string('format').':</td><td>'.$preferences->formatselect.'</td></tr>'.
               '</table>';
    }

    public function process_form($form, &$preferences) {
        $preferences['message_showmessagewindow'] = !empty($form->showmessagewindow)?1:0;
        $preferences['message_blocknoncontacts']  = !empty($form->blocknoncontacts)?1:0;
        $preferences['message_beepnewmessage']    = !empty($form->beepnewmessage)?1:0;
        $preferences['message_usehtmleditor']     = !empty($form->usehtmleditor)?1:0;
        $preferences['message_noframesjs']        = !empty($form->noframesjs)?1:0;
        $preferences['message_emailmessages']     = !empty($form->emailmessages)?1:0;
        $preferences['message_emailtimenosee']    = $form->emailtimenosee;
        $preferences['message_emailaddress']      = $form->emailaddress;
        $preferences['message_emailformat']       = $form->emailformat;

        return true;
    }
    public function load_data(&$preferences, $userid) {
        global $USER;
        $preferences->showmessagewindow =  get_user_preferences( 'message_showmessagewindow', 1, $userid);
        $preferences->blocknoncontacts  =  get_user_preferences( 'message_blocknoncontacts', '', $userid);
        $preferences->beepnewmessage    =  get_user_preferences( 'message_beepnewmessage', '', $userid);
        $preferences->usehtmleditor     =  get_user_preferences( 'message_usehtmleditor', '', $userid);
        $preferences->noframesjs        =  get_user_preferences( 'message_noframesjs', '', $userid);
        $preferences->emailmessages     =  get_user_preferences( 'message_emailmessages', 1, $userid);
        $preferences->emailtimenosee    =  get_user_preferences( 'message_emailtimenosee', 10, $userid);
        $preferences->emailaddress      =  get_user_preferences( 'message_emailaddress', $USER->email, $userid);
        $preferences->formatselect      =  html_writer::select(array(FORMAT_PLAIN => get_string('formatplain'),
                                                                FORMAT_HTML  => get_string('formathtml')),
                                                                'emailformat', get_user_preferences('message_emailformat', FORMAT_PLAIN));

        return true;
    }
}