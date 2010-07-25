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
 *  modulename     -
 *  userfrom
 *  userto
 *  subject
 *  fullmessage - the full message in a given format
 *  fullmessageformat  - the format if the full message (FORMAT_MOODLE, FORMAT_HTML, ..)
 *  fullmessagehtml  - the full version (the message processor will choose with one to use)
 *  smallmessage - the small version of the message
 *
 * @param object $eventdata information about the message (modulename, userfrom, userto, ...)
 * @return boolean success
 */
function message_send($eventdata) {
    global $CFG, $DB;

    //TODO: this function is very slow and inefficient, it would be a major bottleneck in cron processing, this has to be improved in 2.0
    //      probably we could add two parameters with user messaging preferences and we could somehow preload/cache them in cron

    //TODO: we need to solve problems with database transactions here somehow, for now we just prevent transactions - sorry
    $DB->transactions_forbidden();

    //after how long inactive should the user be considered logged off?
    if (isset($CFG->block_online_users_timetosee)) {
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
    } else {
        $timetoshowusers = 300;//5 minutes
    }

    // Work out if the user is logged in or not
    if ((time() - $timetoshowusers) < $eventdata->userto->lastaccess) {
        $userstate = 'loggedin';
    } else {
        $userstate = 'loggedoff';
    }

    // Create the message object
    $savemessage = new object();
    $savemessage->useridfrom        = $eventdata->userfrom->id;
    $savemessage->useridto          = $eventdata->userto->id;
    $savemessage->subject           = $eventdata->subject;
    $savemessage->fullmessage       = $eventdata->fullmessage;
    $savemessage->fullmessageformat = $eventdata->fullmessageformat;
    $savemessage->fullmessagehtml   = $eventdata->fullmessagehtml;
    $savemessage->smallmessage      = $eventdata->smallmessage;
    $savemessage->timecreated       = time();

    // Find out what processors are defined currently
    // When a user doesn't have settings none gets return, if he doesn't want contact "" gets returned
    $preferencename = 'message_provider_'.$eventdata->component.'_'.$eventdata->name.'_'.$userstate;
    $processor = get_user_preferences($preferencename, NULL, $eventdata->userto->id);

    if ($processor == NULL) { //this user never had a preference, save default
        if (!message_set_default_message_preferences($eventdata->userto)) {
            print_error('cannotsavemessageprefs', 'message');
        }
        if ($userstate == 'loggedin') {
            $processor = 'popup';
        }
        if ($userstate == 'loggedoff') {
            $processor = 'email';
        }
    }

    // if we are suposed to do something with this message
    // No processor for this message, mark it as read
    if ($processor == "") {  //this user cleared all the preferences
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
                include_once($processorfile);  // defines $module with version etc
                $processclass = 'message_output_' . $procname;

                if (class_exists($processclass)) {
                    $pclass = new $processclass();

                    if (!$pclass->send_message($savemessage)) {
                        debugging('Error calling message processor '.$procname);
                        return false;
                    }
                }
            } else {
                debugging('Error calling message processor '.$procname);
                return false;
            }
        }

            $savemessage->timeread = time();
            $messageid = $savemessage->id;
            unset($savemessage->id);

            //if there is no more processors that want to process this we can move message to message_read
            if ( $DB->count_records('message_working', array('unreadmessageid' => $messageid)) == 0){
                if ($DB->insert_record('message_read', $savemessage)) {
                    $DB->delete_records('message', array('id' => $messageid));
                }
            }
    }

    return true;
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
                $provider = new object();
                $provider->id         = $dbproviders[$messagename]->id;
                $provider->capability = $fileprovider['capability'];
                $DB->update_record('message_providers', $provider);
                unset($dbproviders[$messagename]);
                continue;
            }

        } else {             // New message provider, add it

            $provider = new object();
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
 * @return array of message providers
 */
function message_get_my_providers() {
    global $DB;

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    $providers = $DB->get_records('message_providers');

    // Remove all the providers we aren't allowed to see now
    foreach ($providers as $providerid => $provider) {
        if (!empty($provider->capability)) {
            if (!has_capability($provider->capability, $systemcontext)) {
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
    return $DB->delete_records('message_providers', array('component' => $component));
}

/**
 * Set default message preferences.
 * @param $user - User to set message preferences
 */
function message_set_default_message_preferences($user) {
    global $DB;

    $providers = $DB->get_records('message_providers');
    $preferences = array();
    foreach ($providers as $providerid => $provider) {
        $preferences['message_provider_'.$provider->component.'_'.$provider->name.'_loggedin'] = 'popup';
        $preferences['message_provider_'.$provider->component.'_'.$provider->name.'_loggedoff'] = 'email';
    }
    return set_user_preferences($preferences, $user->id);
}
