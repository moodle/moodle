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

require_once(dirname(dirname(__FILE__)) . '/message/lib.php');

/**
 * Called when a message provider wants to send a message.
 * This functions checks the user's processor configuration to send the given type of message,
 * then tries to send it.
 *
 * Required parameter $eventdata structure:
 *  component string component name. must exist in message_providers
 *  name string message type name. must exist in message_providers
 *  userfrom object|int the user sending the message
 *  userto object|int the message recipient
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
        $eventdata->userto = $DB->get_record('user', array('id' => $eventdata->userto));
    }
    if (is_int($eventdata->userfrom)) {
        $eventdata->userfrom = $DB->get_record('user', array('id' => $eventdata->userfrom));
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

    // Fetch enabled processors
    $processors = get_message_processors(true);
    // Fetch default (site) preferences
    $defaultpreferences = get_message_output_default_preferences();

    // Preset variables
    $processorlist = array();
    $preferencebase = $eventdata->component.'_'.$eventdata->name;
    // Fill in the array of processors to be used based on default and user preferences
    foreach ($processors as $processor) {
        // First find out permissions
        $defaultpreference = $processor->name.'_provider_'.$preferencebase.'_permitted';
        if (isset($defaultpreferences->{$defaultpreference})) {
            $permitted = $defaultpreferences->{$defaultpreference};
        } else {
            //MDL-25114 They supplied an $eventdata->component $eventdata->name combination which doesn't
            //exist in the message_provider table (thus there is no default settings for them)
            $preferrormsg = get_string('couldnotfindpreference', 'message', $preferencename);
            throw new coding_exception($preferrormsg,'blah');
        }

        // Find out if user has configured this output
        // Some processors cannot function without settings from the user
        $userisconfigured = $processor->object->is_user_configured($eventdata->userto);

        // DEBUG: notify if we are forcing unconfigured output
        if ($permitted == 'forced' && !$userisconfigured) {
            debugging('Attempt to force message delivery to user who has "'.$processor->name.'" output unconfigured', DEBUG_NORMAL);
        }

        //warn developers that necessary data is missing regardless of how the processors are configured
        if (!isset($eventdata->userto->emailstop)) {
            debugging('userto->emailstop is not set. Retreiving it from the user table');
            $eventdata->userto->emailstop = $DB->get_field('user', 'emailstop', array('id'=>$eventdata->userto->id));
        }

        // Populate the list of processors we will be using
        if ($permitted == 'forced' && $userisconfigured) {
            // An admin is forcing users to use this message processor. Use this processor unconditionally.
            $processorlist[] = $processor->name;
        } else if ($permitted == 'permitted' && $userisconfigured && !$eventdata->userto->emailstop) {
            //user has not disabled notifications
            //see if user set any notification preferences, otherwise use site default ones
            $userpreferencename = 'message_provider_'.$preferencebase.'_'.$userstate;
            if ($userpreference = get_user_preferences($userpreferencename, null, $eventdata->userto->id)) {
                if (in_array($processor->name, explode(',', $userpreference))) {
                    $processorlist[] = $processor->name;
                }
            } else if (isset($defaultpreferences->{$userpreferencename})) {
                if (in_array($processor->name, explode(',', $defaultpreferences->{$userpreferencename}))) {
                    $processorlist[] = $processor->name;
                }
            }
        }
    }

    if (empty($processorlist) && $savemessage->notification) {
        //if they have deselected all processors and its a notification mark it read. The user doesnt want to be bothered
        $savemessage->timeread = time();
        $messageid = $DB->insert_record('message_read', $savemessage);
    } else {                        // Process the message
        // Store unread message just in case we can not send it
        $messageid = $savemessage->id = $DB->insert_record('message', $savemessage);
        $eventdata->savedmessageid = $savemessage->id;

        // Try to deliver the message to each processor
        if (!empty($processorlist)) {
            foreach ($processorlist as $procname) {
                if (!$processors[$procname]->object->send_message($eventdata)) {
                    debugging('Error calling message processor '.$procname);
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
 *
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
            // check if capability has changed
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

            $transaction = $DB->start_delegated_transaction();
            $DB->insert_record('message_providers', $provider);
            message_set_default_message_preference($component, $messagename, $fileprovider);
            $transaction->allow_commit();
        }
    }

    foreach ($dbproviders as $dbprovider) {  // Delete old ones
        $DB->delete_records('message_providers', array('id' => $dbprovider->id));
        $DB->delete_records_select('config_plugins', "plugin = 'message' AND ".$DB->sql_like('name', '?', false), array("%_provider_{$component}_{$dbprovider->name}_%"));
        $DB->delete_records_select('user_preferences', $DB->sql_like('name', '?', false), array("message_provider_{$component}_{$dbprovider->name}_%"));
    }

    return true;
}

/**
 * This function populates default message preferences for all existing providers
 * when the new message processor is added.
 *
 * @param string $processorname The name of message processor plugin (e.g. 'email', 'jabber')
 * @return void
 * @throws invalid_parameter_exception if $processorname does not exist
 */
function message_update_processors($processorname) {
    global $DB;

    // validate if our processor exists
    $processor = $DB->get_records('message_processors', array('name' => $processorname));
    if (empty($processor)) {
        throw new invalid_parameter_exception();
    }

    $providers = $DB->get_records_sql('SELECT DISTINCT component FROM {message_providers}');

    $transaction = $DB->start_delegated_transaction();
    foreach ($providers as $provider) {
        // load message providers from files
        $fileproviders = message_get_providers_from_file($provider->component);
        foreach ($fileproviders as $messagename => $fileprovider) {
            message_set_default_message_preference($provider->component, $messagename, $fileprovider, $processorname);
        }
    }
    $transaction->allow_commit();
}

/**
 * Setting default messaging preference for particular message provider
 *
 * @param  string $component   The name of component (e.g. moodle, mod_forum, etc.)
 * @param  string $messagename The name of message provider
 * @param  array  $fileprovider The value of $messagename key in the array defined in plugin messages.php
 * @param  string $processorname The optinal name of message processor
 * @return void
 */
function message_set_default_message_preference($component, $messagename, $fileprovider, $processorname='') {
    global $DB;

    // Fetch message processors
    $condition = null;
    // If we need to process a particular processor, set the select condition
    if (!empty($processorname)) {
       $condition = array('name' => $processorname);
    }
    $processors = $DB->get_records('message_processors', $condition);

    // load default messaging preferences
    $defaultpreferences = get_message_output_default_preferences();

    // Setting default preference
    $componentproviderbase = $component.'_'.$messagename;
    $loggedinpref = array();
    $loggedoffpref = array();
    // set 'permitted' preference first for each messaging processor
    foreach ($processors as $processor) {
        $preferencename = $processor->name.'_provider_'.$componentproviderbase.'_permitted';
        // if we do not have this setting yet, set it
        if (!isset($defaultpreferences->{$preferencename})) {
            // determine plugin default settings
            $plugindefault = 0;
            if (isset($fileprovider['defaults'][$processor->name])) {
                $plugindefault = $fileprovider['defaults'][$processor->name];
            }
            // get string values of the settings
            list($permitted, $loggedin, $loggedoff) = translate_message_default_setting($plugindefault, $processor->name);
            // store default preferences for current processor
            set_config($preferencename, $permitted, 'message');
            // save loggedin/loggedoff settings
            if ($loggedin) {
                $loggedinpref[] = $processor->name;
            }
            if ($loggedoff) {
                $loggedoffpref[] = $processor->name;
            }
        }
    }
    // now set loggedin/loggedoff preferences
    if (!empty($loggedinpref)) {
        $preferencename = 'message_provider_'.$componentproviderbase.'_loggedin';
        if (isset($defaultpreferences->{$preferencename})) {
            // We have the default preferences for this message provider, which
            // likely means that we have been adding a new processor. Add defaults
            // to exisitng preferences.
            $loggedinpref = array_merge($loggedinpref, explode(',', $defaultpreferences->{$preferencename}));
        }
        set_config($preferencename, join(',', $loggedinpref), 'message');
    }
    if (!empty($loggedoffpref)) {
        $preferencename = 'message_provider_'.$componentproviderbase.'_loggedoff';
        if (isset($defaultpreferences->{$preferencename})) {
            // We have the default preferences for this message provider, which
            // likely means that we have been adding a new processor. Add defaults
            // to exisitng preferences.
            $loggedoffpref = array_merge($loggedoffpref, explode(',', $defaultpreferences->{$preferencename}));
        }
        set_config($preferencename, join(',', $loggedoffpref), 'message');
    }
}

/**
 * Returns the active providers for the current user, based on capability
 *
 * This function has been deprecated please use {@see message_get_providers_for_user()} instead.
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
 * Returns the active providers for the user specified, based on capability
 *
 * @param int $userid id of user
 * @return array of message providers
 */
function message_get_providers_for_user($userid) {
    global $DB, $CFG;

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    $providers = $DB->get_records('message_providers', null, 'name');

    // Remove all the providers we aren't allowed to see now
    foreach ($providers as $providerid => $provider) {
        if (!empty($provider->capability)) {
            if (!has_capability($provider->capability, $systemcontext, $userid)) {
                unset($providers[$providerid]);   // Not allowed to see this
            }
        }
        // Ensure user is not allowed to configure instantmessage if it is globally disabled.
        if (!$CFG->messaging && $provider->name == 'instantmessage') {
            unset($providers[$providerid]);
        }
    }

    return $providers;
}

/**
 * Gets the message providers that are in the database for this component.
 *
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
 *
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
        if (empty($messageprovider['defaults'])) {
            $messageproviders[$name]['defaults'] = array();
        }
    }

    return $messageproviders;
}

/**
 * Remove all message providers for particular plugin and corresponding settings
 *
 * @param string $component - examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @return void
 */
function message_provider_uninstall($component) {
    global $DB;

    $transaction = $DB->start_delegated_transaction();
    $DB->delete_records('message_providers', array('component' => $component));
    $DB->delete_records_select('config_plugins', "plugin = 'message' AND ".$DB->sql_like('name', '?', false), array("%_provider_{$component}_%"));
    $DB->delete_records_select('user_preferences', $DB->sql_like('name', '?', false), array("message_provider_{$component}_%"));
    $transaction->allow_commit();
}

/**
 * Remove message processor
 *
 * @param string $name - examples: 'email', 'jabber'
 * @return void
 */
function message_processor_uninstall($name) {
    global $DB;

    $transaction = $DB->start_delegated_transaction();
    $DB->delete_records('message_processors', array('name' => $name));
    // delete permission preferences only, we do not care about loggedin/loggedoff
    // defaults, they will be removed on the next attempt to update the preferences
    $DB->delete_records_select('config_plugins', "plugin = 'message' AND ".$DB->sql_like('name', '?', false), array("{$name}_provider_%"));
    $transaction->allow_commit();
}
