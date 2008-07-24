<?php  // $Id$

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
 * messagelib.php - Contains the events handlers for the message system
 *
 * @author Luis Rodrigues
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package 
 */


define('TIMETOSHOWUSERS', 300);
 
/**
 * Is trigged by an events_trigger in the MODULE_install function when
 * a module wants to be a message provider provider.
 * @param object $eventdata the information about the message provider (name and file)
 * @return boolean success
 */
function message_provider_register_handler($eventdata) {
    global $DB;
    $return = true;

    $provider = new object();
    $provider->modulename  = $eventdata->modulename;
    $provider->modulefile  = $eventdata->modulefile;
    if (!$DB->insert_record('message_providers', $provider)) {
        $return = false;
    }

    // everything ok :-)
    return $return;
}

/**
 * To be used to ungegister a message provider (curently not used)
 * @param object $eventdata the information about the message provider (name and file)
 * @return boolean success
 */
function message_provider_unregister_handler($eventdata) {
    // everything ok :-)
    return true;
}

/**
 * Triggered when a message provider wants to send a message.
 * This functions checks the user's processor configuration to send the given type of message,
 * then tries to send it.
 * @param object $eventdata information about he message (origin, destination, type, content)
 * @return boolean success
 */
function message_send_handler($eventdata){
    global $CFG, $DB;

    if (isset($CFG->block_online_users_timetosee)) {
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
    } else {
        $timetoshowusers = TIMETOSHOWUSERS;
    }

/// Work out if the user is logged in or not
    if ((time() - $eventdata->userto->lastaccess) > $timetoshowusers) {
        $userstate = 'loggedoff';
    } else {
        $userstate = 'loggedin';
    }

/// Create the message object
    $savemessage = new object();
    $savemessage->useridfrom        = $eventdata->userfrom->id;
    $savemessage->useridto          = $eventdata->userto->id;
    $savemessage->subject           = $eventdata->subject;
    $savemessage->fullmessage       = $eventdata->fullmessage;
    $savemessage->fullmessageformat = $eventdata->fullmessageformat;
    $savemessage->fullmessagehtml   = $eventdata->fullmessagehtml;
    $savemessage->smallmessage      = $eventdata->smallmessage;
    $savemessage->timecreated       = time();

/// Find out what processors are defined currently

    // XXX TODO
    // Note this currently defaults to email all the time.  We need a better solution 
    // to be able to distinguish between a user who has no settings and one who doesn't want contact
    // ... perhaps a "none" setting

    $processor = get_user_preferences('message_provider_'.$eventdata->modulename.'_'.$userstate, 'email', $eventdata->userto->id);

/// Now process the message

    if (empty($processor)) {        // There is no processor so just mark it as read
        $savemessage->timeread = time();        
        $messageid = $message->id;
        unset($message->id);
        $DB->insert_record('message_read', $savemessage);

    } else {                        // Process the message

    /// Store unread message just in case we can not send it
        $savemessage->id = $DB->insert_record('message', $savemessage);


    /// Try to deliver the message to each processor
        $processorlist = explode(',', $processor);
        foreach ($processorlist as $procname) {
            $processorfile = $CFG->dirroot. '/message/output/'.$procname.'/message_output_'.$procname.'.php';

            if (is_readable($processorfile)) {        
                include_once( $processorfile );  // defines $module with version etc
                $processclass = 'message_output_' . $procname;
                
                if (class_exists($processclass)) {                    
                    $pclass = new $processclass();

                    if (! $pclass->send_message($savemessage)) {
                        debugging('Error calling message processor '.$procname);
                        return false;
                    }                    
                }
            } else {
                debugging('Error calling message processor '.$procname);
                return false;
            }
        }
    }

    return true;
}

?>
