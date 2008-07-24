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

require_once('../config.php'); //included from messagelib (how to fix?)
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
    public function send_message($message){
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
    
    public function process_form(&$form, &$preferences){
        return true;
    }
    public function load_data(&$preferences, $userid){
        return true;
    }
}
?>
