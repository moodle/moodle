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
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package 
 */


define("JABBER_SERVER","jabber80.com");
define("JABBER_USERNAME","");
define("JABBER_PASSWORD","");

define("RUN_TIME",15);  // set a maximum run time of 15 seconds

require_once('../config.php'); //included from messagelib (how to fix?)
require_once($CFG->dirroot.'/message/output/lib.php');

class JabberMessenger {
    function JabberMessenger(&$jab, $message) {
        $this->jab = &$jab;
        $this->message = $message;
        $this->first_roster_update = true;
        $this->countdown = 0;
    }
    // called when a connection to the Jabber server is established
    function handleConnected() {
        $this->jab->login(JABBER_USERNAME,JABBER_PASSWORD);
    }
    // called after a login to indicate the the login was successful
    function handleAuthenticated() {
        $userfrom = $DB->get_record('user', array('id' => $this->message->useridfrom));
        $this->jab->message("lfrodrigues@gmail.com","chat",NULL,fullname($userfrom)."(SITENAME) says: ".$this->message->fullmessage);
        $this->jab->terminated = true;
    }
    // called after a login to indicate that the login was NOT successful
    function handleAuthFailure($code,$error) {
        // set terminated to TRUE in the Jabber class to tell it to exit
        $this->jab->terminated = true;
    }
}


class message_output_jabber extends message_output {
    
    /**
     * Processes the message (sends using jabber).
     * @param object $message the message to be sent
     * @return true if ok, false if error
     */
    function send_message($message){

        require_once("jabberclass/class_Jabber.php");

        $jab = new Jabber( false );
        // create an instance of our event handler class
        $test = new JabberMessenger($jab, $message);
        // set handlers for the events we wish to be notified about
        $jab->set_handler("connected",$test,"handleConnected");
        $jab->set_handler("authenticated",$test,"handleAuthenticated");
        $jab->set_handler("authfailure",$test,"handleAuthFailure");

        if (!$jab->connect(JABBER_SERVER)) {
            return false;
        }

        // now, tell the Jabber class to begin its execution loop
        // don't way for events
        $jab->execute(-1,RUN_TIME);
        $jab->disconnect();


        $f = fopen('/tmp/event_jabber', 'a+');
        fwrite($f, date('l dS \of F Y h:i:s A')."\n");
        fwrite($f, "from: $message->userfromid\n");
        fwrite($f, "userto: $message->usertoid\n");
        fwrite($f, "subject: $message->subject\n");
        fclose($f);

        return true;
    }

    /** 
     * Creates necessary fields in the messaging config form.
     * @param object $mform preferences form class
     */
    function config_form($preferences){
        $dest = get_string('jabber', 'messageprocessor_jabber');
        echo '<tr><td colspan="2"><b>'.get_string('processortag', 'message').$dest.'</b></td></tr>'."\n";
        echo '<tr><td align="right">Jabber ID</td><td><input name="jabber_jabberid" value="'.$preferences->jabber_jabberid.'" /></td></tr>'."\n";
        return true;
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
?>
