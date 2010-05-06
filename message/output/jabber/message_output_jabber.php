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
 * Jabber message processor - send a given message by jabber
 *
 * @author Luis Rodrigues
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package
 */


define("JABBER_SERVER","jabber80.com");
define("JABBER_USERNAME","");
define("JABBER_PASSWORD","");

define("RUN_TIME",15);  // set a maximum run time of 15 seconds

require_once($CFG->dirroot.'/message/output/lib.php');
require_once($CFG->libdir.'/jabber/XMPP/XMPP.php');

class message_output_jabber extends message_output {

    /**
     * Processes the message (sends using jabber).
     * @param object $message the message to be sent
     * @return true if ok, false if error
     */
    function send_message($message){
        global $DB;

        if (!$userfrom = $DB->get_record('user', array('id' => $message->useridfrom))) {
            return false;
        }
        if (!$userto = $DB->get_record('user', array('id' => $this->message->useridto))) {
            return false;
        }
        if (!$jabberaddress = get_user_preferences('message_processor_jabber_jabberid', $userto->email, $userto->id)) {
            $jabberaddress = $userto->email;
        }
        $jabbermessage = fullname($userfrom).': '.$message->fullmessage;

        $conection = new XMPPHP_XMPP(JABBER_SERVER, 5222, JABBER_USERNAME, JABBER_PASSWORD, 'moodle', JABBER_SERVER);

        try {
            $conn->connect();
            $conn->processUntil('session_start');
            $conn->presence();
            $conn->message($jabberaddress, $jabbermessage);
            $conn->disconnect();
        } catch(XMPPHP_Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Creates necessary fields in the messaging config form.
     * @param object $mform preferences form class
     */
    function config_form($preferences){
        return get_string('jabberid', 'message_jabber').': <input size="30" name="jabber_jabberid" value="'.$preferences->jabber_jabberid.'" />';
    }

    /**
     * Parses the form submited data and saves it into preferences array.
     * @param object $mform preferences form class
     * @param array $preferences preferences array
     */
    function process_form($form, &$preferences){
        $preferences['message_processor_jabber_jabberid'] = $form->jabber_jabberid;
    }

    /**
     * Loads the config data from database to put on the form (initial load)
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    function load_data(&$preferences, $userid){
        $preferences->jabber_jabberid = get_user_preferences( 'message_processor_jabber_jabberid', '', $userid);
    }

}

/*
 *
 *         $f = fopen('/tmp/event_jabberx', 'a+');
        fwrite($f, date('l dS \of F Y h:i:s A')."\n");
        fwrite($f, "from: $message->userfromid\n");
        fwrite($f, "userto: $message->usertoid\n");
        fwrite($f, "subject: $message->subject\n");
        fclose($f);


$savemessage = new object();
    $savemessage->useridfrom        = 3;
    $savemessage->useridto          = 2;
    $savemessage->subject           = 'IM';
    $savemessage->fullmessage       = 'full';
    $savemessage->timecreated       = time();


$a = new message_output_jabber();

$a->send_message($savemessage);
* */

