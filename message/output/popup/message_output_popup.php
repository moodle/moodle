<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Popup message processor - stores the message to be shown using the message popup
 *
 * @author Luis Rodrigues
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package 
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

        if ( !$DB->insert_record('message_working', $procmessage) ) {
            return false;
        }

        //should only save this message for later delivery
        return true;
    }
    
    function config_form($preferences) {
        echo '<fieldset id="messageprocessor_popup" class="clearfix">';
        echo '<legend class="ftoggler">'.get_string('popup', 'messageprocessor_popup').'</legend>';
        echo '<table>';
        echo '<tr><td>'.get_string('showmessagewindow', 'message').'</td><td><input type="checkbox" name="showmessagewindow" '.($preferences->showmessagewindow==1?" checked=\"checked\"":"").' /></td></tr>';
        echo '<tr><td>'.get_string('blocknoncontacts', 'message').'</td><td><input type="checkbox" name="blocknoncontacts" '.($preferences->blocknoncontacts==1?" checked=\"checked\"":"").' /></td></tr>';
        echo '<tr><td>'.get_string('beepnewmessage', 'message').'</td><td><input type="checkbox" name="beepnewmessage" '.($preferences->beepnewmessage==1?" checked=\"checked\"":"").' /></td></tr>';
        echo '<tr><td>'.get_string('noframesjs', 'message').'</td><td><input type="checkbox" name="noframesjs" '.($preferences->noframesjs==1?" checked=\"checked\"":"").' /></td></tr>';
        echo '</table>';
        echo '</fieldset>';
    }
    
    public function process_form($form, &$preferences) {
        $preferences['message_showmessagewindow'] = $form->showmessagewindow?1:0;
        $preferences['message_blocknoncontacts']  = $form->blocknoncontacts?1:0;
        $preferences['message_beepnewmessage']    = $form->beepnewmessage?1:0;
        $preferences['message_noframesjs']        = $form->noframesjs?1:0;
        return true;
    }
    public function load_data(&$preferences, $userid) {
        $preferences->showmessagewindow =  get_user_preferences( 'message_showmessagewindow', 1, $user->id);
        $preferences->blocknoncontacts  =  get_user_preferences( 'message_blocknoncontacts', '', $user->id);
        $preferences->beepnewmessage    =  get_user_preferences( 'message_beepnewmessage', '', $user->id);
        $preferences->noframesjs        =  get_user_preferences( 'message_noframesjs', '', $user->id);
        return true;
    }
}
?>
