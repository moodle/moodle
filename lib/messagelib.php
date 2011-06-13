<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
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
 * messagelib.php - Contains generic messaging functions for the message system
 *
 * @package    core
 * @subpackage message
 * @copyright  Luis Rodrigues and Martin Dougiamas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Called when a message provider wants to send a message.
 * This functions checks the user's processor configuration to send the given type of message,
 * then tries to send it.
 *
 * Required parameter $eventdata structure:
 *  component string component name. must exist in message_providers
 *  name string message type name. must exist in message_providers
 *  userfrom object the user sending the message
 *  userto object the message recipient
 *  subject string the message subject
 *  fullmessage - the full message in a given format
 *  fullmessageformat  - the format if the full message (FORMAT_MOODLE, FORMAT_HTML, ..)
 *  fullmessagehtml  - the full version (the message processor will choose with one to use)
 *  smallmessage - the small version of the message
 *  contexturl - if this is a notification then you can specify a url to view the event. For example the forum post the user is being notified of.
 *  contexturlname - the display text for contexturl
 *
 * @param object $eventdata information about the message (component, userfrom, userto, ...)
 * @return int|false the ID of the new message or false if there was a problem with a processor
 */
function message_send($eventdata) {
    global $CFG, $DB;

    //new message ID to return
    $messageid = false;

    //TODO: we need to solve problems with database transactions here somehow, for now we just prevent transactions - sorry
    $DB->transactions_forbidden();

    if (is_int($eventdata->userto)) {
        mtrace('message_send() userto is a user ID when it should be a user object');
        $eventdata->userto = $DB->get_record('user', array('id' => $eventdata->useridto));
    }
    if (is_int($eventdata->userfrom)) {
        mtrace('message_send() userfrom is a user ID when it should be a user object');
        $eventdata->userfrom = $DB->get_record('user', array('id' => $message->userfrom));
    }

    //after how long inactive should the user be considered logged off?
    if (isset($CFG->block_online_users_timetosee)) {
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
    } else {
        $timetoshowusers = 300;//5 minutes
    }

    // Work out if the user is logged in or not
    if (!empty($eventdata->userto->lastaccess) && (time()-$timetoshowusers) < $eventdata->userto->lastaccess) {
        $userstate = 'loggedin';
    } else {
        $userstate = 'loggedoff';
    }

    // Create the message object
    $savemessage = new stdClass();
    $savemessage->useridfrom        = $eventdata->userfrom->id;
    $savemessage->useridto          = $eventdata->userto->id;
    $savemessage->subject           = $eventdata->subject;
    $savemessage->fullmessage       = $eventdata->fullmessage;
    $savemessage->fullmessageformat = $eventdata->fullmessageformat;
    $savemessage->fullmessagehtml   = $eventdata->fullmessagehtml;
    $savemessage->smallmessage      = $eventdata->smallmessage;

    if (!empty($eventdata->notification)) {
        $savemessage->notification = $eventdata->notification;
    } else {
        $savemessage->notification = 0;
    }

    if (!empty($eventdata->contexturl)) {
        $savemessage->contexturl = $eventdata->contexturl;
    } else {
        $savemessage->contexturl = null;
    }

    if (!empty($eventdata->contexturlname)) {
        $savemessage->contexturlname = $eventdata->contexturlname;
    } else {
        $savemessage->contexturlname = null;
    }

    $savemessage->timecreated = time();

    // Find out what processors are defined currently
    // When a user doesn't have settings none gets return, if he doesn't want contact "" gets returned
    $preferencename = 'message_provider_'.$eventdata->component.'_'.$eventdata->name.'_'.$userstate;

    $processor = get_user_preferences($preferencename, null, $eventdata->userto->id);
    if ($processor == NULL) { //this user never had a preference, save default
        if (!message_set_default_message_preferences($eventdata->userto)) {
            print_error('cannotsavemessageprefs', 'message');
        }
        $processor = get_user_preferences($preferencename, NULL, $eventdata->userto->id);
        if (empty($processor)) {
            //MDL-25114 They supplied an $eventdata->component $eventdata->name combination which doesn't
            //exist in the message_provider table
            $preferrormsg = get_string('couldnotfindpreference', 'message', $preferencename);
            throw new coding_exception($preferrormsg,'blah');
        }
    }

    if ($processor=='none' && $savemessage->notification) {
        //if they have deselected all processors and its a notification mark it read. The user doesnt want to be bothered
        $savemessage->timeread = time();
        $messageid = $DB->insert_record('message_read', $savemessage);
    } else {                        // Process the message
        // Store unread message just in case we can not send it
        $messageid = $savemessage->id = $DB->insert_record('message', $savemessage);
        $eventdata->savedmessageid = $savemessage->id;

        // Try to deliver the message to each processor
        if ($processor!='none') {
            $processorlist = explode(',', $processor);
            foreach ($processorlist as $procname) {
                $processorfile = $CFG->dirroot. '/message/output/'.$procname.'/message_output_'.$procname.'.php';

                if (is_readable($processorfile)) {
                    include_once($processorfile);  // defines $module with version etc
                    $processclass = 'message_output_' . $procname;

                    if (class_exists($processclass)) {
                        $pclass = new $processclass();

                        if (!$pclass->send_message($eventdata)) {
                            debugging('Error calling message processor '.$procname);
                            $messageid = false;
                        }
                    }
                } else {
                    debugging('Error finding message processor '.$procname);
                    $messageid = false;
                }
            }
            
            //if messaging is disabled and they previously had forum notifications handled by the popup processor
            //or any processor that puts a row in message_working then the notification will remain forever
            //unread. To prevent this mark the message read if messaging is disabled
            if (empty($CFG->messaging)) {
                require_once($CFG->dirroot.'/message/lib.php');
                $messageid = message_mark_message_read($savemessage, time());
            } else if ( $DB->count_records('message_working', array('unreadmessageid' => $savemessage->id)) == 0){
                //if there is no more processors that want to process this we can move message to message_read
                require_once($CFG->dirroot.'/message/lib.php');
                $messageid = message_mark_message_read($savemessage, time(), true);
            }
        }
    }

    return $messageid;
}


/**
 * This code updates the message_providers table with the current set of providers
 * @param $component - examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @return boolean
 */
function message_update_providers($component='moodle') {
    global $DB;

    // load message providers from files
    $fileproviders = message_get_providers_from_file($component);

    // load message providers from the database
    $dbproviders = message_get_providers_from_db($component);

    foreach ($fileproviders as $messagename => $fileprovider) {

        if (!empty($dbproviders[$messagename])) {   // Already exists in the database

            if ($dbproviders[$messagename]->capability == $fileprovider['capability']) {  // Same, so ignore
                // exact same message provider already present in db, ignore this entry
                unset($dbproviders[$messagename]);
                continue;

            } else {                                // Update existing one
                $provider = new stdClass();
                $provider->id         = $dbproviders[$messagename]->id;
                $provider->capability = $fileprovider['capability'];
                $DB->update_record('message_providers', $provider);
                unset($dbproviders[$messagename]);
                continue;
            }

        } else {             // New message provider, add it

            $provider = new stdClass();
            $provider->name       = $messagename;
            $provider->component  = $component;
            $provider->capability = $fileprovider['capability'];

            $DB->insert_record('message_providers', $provider);
        }
    }

    foreach ($dbproviders as $dbprovider) {  // Delete old ones
        $DB->delete_records('message_providers', array('id' => $dbprovider->id));
    }

    return true;
}

/**
 * Returns the active providers for the current user, based on capability
 *
 * @deprecated since 2.1
 * @todo Remove in 2.2
 * @return array of message providers
 */
function message_get_my_providers() {
    global $USER;
    return message_get_providers_for_user($USER->id);
}

/**
 * Returns the active providers for the requested user, based on capability
 *
 * @param int $userid id of user
 * @return array of message providers
 */
function message_get_providers_for_user($userid) {
    global $DB;

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    $providers = $DB->get_records('message_providers', null, 'name');

    // Remove all the providers we aren't allowed to see now
    foreach ($providers as $providerid => $provider) {
        if (!empty($provider->capability)) {
            if (!has_capability($provider->capability, $systemcontext, $userid)) {
                unset($providers[$providerid]);   // Not allowed to see this
            }
        }
    }

    return $providers;
}

/**
 * Gets the message providers that are in the database for this component.
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return array of message providers
 *
 * INTERNAL - to be used from messagelib only
 */
function message_get_providers_from_db($component) {
    global $DB;

    return $DB->get_records('message_providers', array('component'=>$component), '', 'name, id, component, capability');  // Name is unique per component
}

/**
 * Loads the messages definitions for the component (from file). If no
 * messages are defined for the component, we simply return an empty array.
 * @param $component - examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @return array of message providerss or empty array if not exists
 *
 * INTERNAL - to be used from messagelib only
 */
function message_get_providers_from_file($component) {
    $defpath = get_component_directory($component).'/db/messages.php';

    $messageproviders = array();

    if (file_exists($defpath)) {
        require($defpath);
    }

    foreach ($messageproviders as $name => $messageprovider) {   // Fix up missing values if required
        if (empty($messageprovider['capability'])) {
            $messageproviders[$name]['capability'] = NULL;
        }
    }

    return $messageproviders;
}

/**
 * Remove all message providers
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 */
function message_uninstall($component) {
    global $DB;
    return $DB->delete_records('message_providers', array('component' => $component));
}

/**
 * Set default message preferences.
 * @param $user - User to set message preferences
 */
function message_set_default_message_preferences($user) {
    global $DB;

    //check for the pre 2.0 disable email setting
    $useemail = empty($user->emailstop);

    //look for the pre-2.0 preference if it exists
    $oldpreference = get_user_preferences('message_showmessagewindow', -1, $user->id);
    //if they elected to see popups or the preference didnt exist
    $usepopups = (intval($oldpreference)==1 || intval($oldpreference)==-1);

    $defaultonlineprocessor = 'none';
    $defaultofflineprocessor = 'none';
    
    if ($useemail) {
        $defaultonlineprocessor = 'email';
        $defaultofflineprocessor = 'email';
    } else if ($usepopups) {
        $defaultonlineprocessor = 'popup';
        $defaultofflineprocessor = 'popup';
    }

    $offlineprocessortouse = $onlineprocessortouse = null;

    $providers = $DB->get_records('message_providers');
    $preferences = array();

    foreach ($providers as $providerid => $provider) {

        //force some specific defaults for IMs
        if ($provider->name=='instantmessage' && $usepopups && $useemail) {
            $onlineprocessortouse = 'popup';
            $offlineprocessortouse = 'email,popup';
        } else {
            $onlineprocessortouse = $defaultonlineprocessor;
            $offlineprocessortouse = $defaultofflineprocessor;
        }
        
        $preferences['message_provider_'.$provider->component.'_'.$provider->name.'_loggedin'] = $onlineprocessortouse;
        $preferences['message_provider_'.$provider->component.'_'.$provider->name.'_loggedoff'] = $offlineprocessortouse;
    }
    return set_user_preferences($preferences, $user->id);
}
