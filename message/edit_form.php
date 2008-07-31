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
 * Edit user message form
 *
 * @author Luis Rodrigues
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package
 */

require_once($CFG->dirroot.'/lib/formslib.php');

class user_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $CFG, $COURSE, $DB;

        $mform =& $this->_form;
        $strrequired = get_string('required');

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id'); //the userid
        $mform->addElement('hidden', 'course', $COURSE->id); //courseid

        //get a listing of all the message processors
        $processors = $DB->get_records('message_processors');

        //create the general config section
        $mform->addElement('header', 'general_config',  get_string('general_config', 'message') );
        $mform->addElement('checkbox', 'showmessagewindow', get_string('showmessagewindow', 'message') );
        $mform->addElement('checkbox', 'blocknoncontacts', get_string('blocknoncontacts', 'message') );
        $mform->addElement('checkbox', 'beepnewmessage', get_string('beepnewmessage', 'message') );
        $mform->addElement('checkbox', 'noframesjs', get_string('noframesjs', 'message') );


        //create the providers config section
        $mform->addElement('header', 'providers_config',  get_string('providers_config', 'message') );
        $providers = message_get_my_providers();
        foreach ( $providers as $providerid => $provider){

            $providername = get_string('messageprovider:'.$provider->name, $provider->component);

            $mform->addElement('static', 'label'.$provider->component, $providername, '');

            $test = array();
            foreach ( $processors as $processorid => $processor){
                $test[] = &$mform->createElement('checkbox', $provider->component.'_loggedin_'.$processor->name, $processor->name, $processor->name);
            }
            $mform->addGroup($test, $provider->component.'_loggedin', get_string('loggedin', 'message'));

            $test = array();
            foreach ( $processors as $processorid => $processor){
                $test[] = &$mform->createElement('checkbox', $provider->component.'_loggedoff_'.$processor->name, $processor->name, $processor->name);
            }
            $mform->addGroup($test, $provider->component.'_loggedoff', get_string('loggedoff', 'message'));
        }

        //create the processors config section (need to get config items from processor's lib.php
        $mform->addElement('header', 'processors_config',  get_string('processor_config', 'message') );
        foreach ( $processors as $processorid => $processor){
            $processorfile = $CFG->dirroot. '/message/output/'.$processor->name.'/lib.php';
            if ( is_readable($processorfile) ) {
                include_once( $processorfile );
                $processfunc = $processor->name .'_config_form';
                if ( function_exists($processfunc) ){
                    $processfunc($mform);
                }
            }
        }

        $this->add_action_buttons(false, get_string('updatemyprofile'));
    }

    function definition_after_data() {
      return true;
    }

    function validation ($messageconf) {
      return true;
    }

}

?>
