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
 * Email message processor - send a given message by email
 *
 * @author Luis Rodrigues
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package
 */
require_once($CFG->dirroot.'/message/output/lib.php');

class message_output_email extends message_output {
    /**
     * Processes the message (sends by email).
     * @param object $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     */
    function send_message($eventdata) {
        global $CFG;

        if (!empty($CFG->noemailever)) {
            // hidden setting for development sites, set in config.php if needed
            debugging('$CFG->noemailever active, no email message sent.', DEBUG_MINIMAL);
            return true;
        }

        // skip any messaging suspended and deleted users
        if ($eventdata->userto->auth === 'nologin' or $eventdata->userto->suspended or $eventdata->userto->deleted) {
            return true;
        }

        //the user the email is going to
        $recipient = null;

        //check if the recipient has a different email address specified in their messaging preferences Vs their user profile
        $emailmessagingpreference = get_user_preferences('message_processor_email_email', null, $eventdata->userto);
        $emailmessagingpreference = clean_param($emailmessagingpreference, PARAM_EMAIL);
        if (!empty($emailmessagingpreference)) {
            //clone to avoid altering the actual user object
            $recipient = clone($eventdata->userto);
            $recipient->email = $emailmessagingpreference;
        } else {
            $recipient = $eventdata->userto;
        }
        $result = email_to_user($recipient, $eventdata->userfrom, $eventdata->subject, $eventdata->fullmessage, $eventdata->fullmessagehtml);

        return $result;
    }

    /**
     * Creates necessary fields in the messaging config form.
     * @param object $mform preferences form class
     */
    function config_form($preferences){
        global $USER, $OUTPUT;

        $inputattributes = array('size'=>'30', 'name'=>'email_email', 'value'=>$preferences->email_email);
        $string = get_string('email','message_email') . ': ' . html_writer::empty_tag('input', $inputattributes);

        if (empty($preferences->email_email) && !empty($preferences->userdefaultemail)) {
            $string .= ' ('.get_string('default').': '.s($preferences->userdefaultemail).')';
        }

        if (!empty($preferences->email_email) && !validate_email($preferences->email_email)) {
            $string .= $OUTPUT->container(get_string('invalidemail'), 'error');
        }

        return $string;
    }

    /**
     * Parses the form submitted data and saves it into preferences array.
     * @param object $mform preferences form class
     * @param array $preferences preferences array
     */
    function process_form($form, &$preferences){
        if (isset($form->email_email)) {
            $preferences['message_processor_email_email'] = $form->email_email;
        }
    }

    /**
     * @return int the Default message output settings for this output, for
     * message providers that do not specify what the settings should be for
     * this output in the messages.php file.
     */
    public function get_default_messaging_settings() {
        return MESSAGE_PERMITTED + MESSAGE_DEFAULT_LOGGEDIN + MESSAGE_DEFAULT_LOGGEDOFF;
    }

    /**
     * Loads the config data from database to put on the form (initial load)
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    function load_data(&$preferences, $userid){
        $preferences->email_email = get_user_preferences( 'message_processor_email_email', '', $userid);
    }
}
