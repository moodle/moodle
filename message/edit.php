<?php // $Id$

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
 * Edit user message preferences
 *
 * @author Luis Rodrigues
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package
 */

require_once('../config.php');

require_once($CFG->dirroot.'/message/edit_form.php');


httpsrequired();

$userid = optional_param('id', $USER->id, PARAM_INT);    // user id
$course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)

if (!$course = $DB->get_record('course', array('id' => $course))) {
    error('Course ID was incorrect');
}

if ($course->id != SITEID) {
    require_login($course);
} else { 
    if (!isloggedin()) {
        if (empty($SESSION->wantsurl)) {
            $SESSION->wantsurl = $CFG->httpswwwroot.'/message/edit.php';
        }
        redirect($CFG->httpswwwroot.'/login/index.php');
    }
}

if (isguestuser()) {
    print_error('guestnoeditmessage', 'message');
}

if (!$user = $DB->get_record('user', array('id' => $userid))) {
    error('User ID was incorrect');
}

$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
$personalcontext = get_context_instance(CONTEXT_USER, $user->id);


// check access control
if ($user->id == $USER->id) {
    //editing own message profile
    require_capability('moodle/user:editownmessageprofile', $systemcontext);

} else {
    // teachers, parents, etc.
    require_capability('moodle/user:editmessageprofile', $personalcontext);
    // no editing of guest user account
    if (isguestuser($user->id)) {
        print_error('guestnoeditmessageother', 'message');
    }
    // no editing of primary admin!
    $mainadmin = get_admin();
    if ($user->id == $mainadmin->id) {
        print_error('adminprimarynoedit');
    }
}


//retrieve preferences from db
$preferences = new object();
$preferences->id = $user->id;

//get the message general preferences
$preferences->showmessagewindow =  get_user_preferences( 'message_showmessagewindow', '', $user->id);
$preferences->blocknoncontacts  =  get_user_preferences( 'message_blocknoncontacts', '', $user->id);
$preferences->beepnewmessage    =  get_user_preferences( 'message_beepnewmessage', '', $user->id);
$preferences->noframesjs        =  get_user_preferences( 'message_noframesjs', '', $user->id);

//for every message provider get preferences for the form
$providers = $DB->get_records('message_providers');
foreach ( $providers as $providerid => $provider){
    foreach (array('loggedin', 'loggedoff') as $state){
        $linepref = get_user_preferences('message_provider_'.$provider->modulename.'_'.$state, '', $user->id);
        if ($linepref == ''){ 
            continue;
        }
        $lineprefarray = explode(',', $linepref);
        $preferences->{$provider->modulename.'_'.$state} = array();
        foreach ($lineprefarray as $pref){
            $preferences->{$provider->modulename.'_'.$state}[$provider->modulename.'_'.$state.'_'.$pref] = 1;
        }
    }
}

//for every processors put its options on the form (need to get funcion from processor's lib.php)
$processors = $DB->get_records('message_processors');
foreach ( $processors as $processorid => $processor){    
    $processorfile = $CFG->dirroot. '/message/output/'.$processor->name.'/message_output_'.$processor->name.'.php';
    if ( is_readable($processorfile) ) {
        include_once( $processorfile );        
        $processclass = 'message_output_' . $processor->name;                
        if ( class_exists($processclass) ){                    
            $pclass = new $processclass();
            $pclass->load_data($preferences, $user->id);                    
        } else{ 
            error('Error calling defined processor');
        }
    }
}


//create form
$userform = new user_edit_form();
$userform->set_data($preferences);

if ($messageconf = $userform->get_data()) {

    add_to_log($course->id, 'message', 'update', "edit.php?id=$user->id&course=$course->id", '');

    $preferences = array();

    //get the list of normal preferences
    $preferences['message_showmessagewindow'] = $messageconf->showmessagewindow?1:0;
    $preferences['message_blocknoncontacts']  = $messageconf->blocknoncontacts?1:0;
    $preferences['message_beepnewmessage']    = $messageconf->beepnewmessage?1:0;
    $preferences['message_noframesjs']        = $messageconf->noframesjs?1:0;

    //get a listing of all the message processors and process the form
    $providers = $DB->get_records('message_providers');
    foreach ( $providers as $providerid => $provider){
        foreach (array('loggedin', 'loggedoff') as $state){
            $linepref = '';
            foreach ($messageconf->{$provider->modulename.'_'.$state} as $process=>$one){
                $parray = explode( '_', $process);
                if ($linepref == ''){ 
                    $linepref = $parray[2];
                } else { 
                    $linepref .= ','.$parray[2];
                }
            }
            $preferences[ 'message_provider_'.$provider->modulename.'_'.$state  ] = $linepref;
        }
    }

    //list all the processors options (need to get funcion from processor's lib.php)
    $processors = $DB->get_records('message_processors');
    foreach ( $processors as $processorid => $processor){
        $processorfile = $CFG->dirroot. '/message/output/'.$processor->name.'/lib.php';
        if ( is_readable($processorfile) ) {
            include_once( $processorfile );
            
            $processclass = 'message_output_' . $processor->name;                
            if ( class_exists($processclass) ){                    
                $pclass = new $processclass();
                $pclass->process_form($messageconf, $preferences);                    
            } else{ 
                error('Error calling defined processor');
            }
        }
    }

    //set the user preferences
    if (!set_user_preferences( $preferences, $user->id ) ){
        error('Error updating user message preferences');
    }

    redirect("$CFG->wwwroot/message/edit.php?id=$user->id&course=$course->id");
}


/// Display page header
$streditmymessage = get_string('editmymessage', 'message');
$strparticipants  = get_string('participants');
$userfullname     = fullname($user, true);
if ($course->id != SITEID) {
    print_header("$course->shortname: $streditmymessage", "$course->fullname: $streditmessage",
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>
                  -> <a href=\"index.php?id=$course->id\">$strparticipants</a>
                  -> <a href=\"view.php?id=$user->id&amp;course=$course->id\">$userfullname</a>
                  -> $streditmymessage", "");
} else {
    print_header("$course->shortname: $streditmymessage", $course->fullname,
                 "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$userfullname</a>
                  -> $streditmymessage", "");
}
/// Print tabs at the top
$showroles = 1;
$currenttab = 'editmessage';
require('../user/tabs.php');

notify('WARNING: This interface is still under construction!');

/// Finally display THE form
$userform->display();

/// and proper footer
print_footer($course);

?>
