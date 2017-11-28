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
 * Functions for interacting with the message system
 *
 * @package   core_message
 * @copyright 2008 Luis Rodrigues and Martin Dougiamas
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../message/lib.php');

/**
 * Called when a message provider wants to send a message.
 * This functions checks the message recipient's message processor configuration then
 * sends the message to the configured processors
 *
 * Required parameters of the $eventdata object:
 *  component string component name. must exist in message_providers
 *  name string message type name. must exist in message_providers
 *  userfrom object|int the user sending the message
 *  userto object|int the message recipient
 *  subject string the message subject
 *  fullmessage string the full message in a given format
 *  fullmessageformat int the format if the full message (FORMAT_MOODLE, FORMAT_HTML, ..)
 *  fullmessagehtml string the full version (the message processor will choose with one to use)
 *  smallmessage string the small version of the message
 *
 * Optional parameters of the $eventdata object:
 *  notification bool should the message be considered as a notification rather than a personal message
 *  contexturl string if this is a notification then you can specify a url to view the event. For example the forum post the user is being notified of.
 *  contexturlname string the display text for contexturl
 *
 * Note: processor failure is is not reported as false return value,
 *       earlier versions did not do it consistently either.
 *
 * @todo MDL-55449 Drop support for stdClass in Moodle 3.6
 * @category message
 * @param \core\message\message $eventdata information about the message (component, userfrom, userto, ...)
 * @return mixed the integer ID of the new message or false if there was a problem with submitted data
 */
function message_send($eventdata) {
    global $CFG, $DB;

    // TODO MDL-55449 Drop support for stdClass in Moodle 3.6.
    if ($eventdata instanceof \stdClass) {
        if (!isset($eventdata->courseid)) {
            $eventdata->courseid = null;
        }

        debugging('eventdata as \stdClass is deprecated. Please use core\message\message instead.', DEBUG_DEVELOPER);
    }

    //new message ID to return
    $messageid = false;

    // Fetch default (site) preferences
    $defaultpreferences = get_message_output_default_preferences();
    $preferencebase = $eventdata->component.'_'.$eventdata->name;
    // If message provider is disabled then don't do any processing.
    if (!empty($defaultpreferences->{$preferencebase.'_disable'})) {
        return $messageid;
    }

    // By default a message is a notification. Only personal/private messages aren't notifications.
    if (!isset($eventdata->notification)) {
        $eventdata->notification = 1;
    }

    if (!is_object($eventdata->userto)) {
        $eventdata->userto = core_user::get_user($eventdata->userto);
    }
    if (!is_object($eventdata->userfrom)) {
        $eventdata->userfrom = core_user::get_user($eventdata->userfrom);
    }
    if (!$eventdata->userto) {
        debugging('Attempt to send msg to unknown user', DEBUG_NORMAL);
        return false;
    }
    if (!$eventdata->userfrom) {
        debugging('Attempt to send msg from unknown user', DEBUG_NORMAL);
        return false;
    }

    // Verify all necessary data fields are present.
    if (!isset($eventdata->userto->auth) or !isset($eventdata->userto->suspended)
            or !isset($eventdata->userto->deleted) or !isset($eventdata->userto->emailstop)) {

        debugging('Necessary properties missing in userto object, fetching full record', DEBUG_DEVELOPER);
        $eventdata->userto = core_user::get_user($eventdata->userto->id);
    }

    $usertoisrealuser = (core_user::is_real_user($eventdata->userto->id) != false);
    // If recipient is internal user (noreply user), and emailstop is set then don't send any msg.
    if (!$usertoisrealuser && !empty($eventdata->userto->emailstop)) {
        debugging('Attempt to send msg to internal (noreply) user', DEBUG_NORMAL);
        return false;
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
    $savemessage->courseid          = $eventdata->courseid;
    $savemessage->useridfrom        = $eventdata->userfrom->id;
    $savemessage->useridto          = $eventdata->userto->id;
    $savemessage->subject           = $eventdata->subject;
    $savemessage->fullmessage       = $eventdata->fullmessage;
    $savemessage->fullmessageformat = $eventdata->fullmessageformat;
    $savemessage->fullmessagehtml   = $eventdata->fullmessagehtml;
    $savemessage->smallmessage      = $eventdata->smallmessage;
    $savemessage->notification      = $eventdata->notification;
    $savemessage->eventtype         = $eventdata->name;
    $savemessage->component         = $eventdata->component;

    if (!empty($eventdata->contexturl)) {
        $savemessage->contexturl = (string)$eventdata->contexturl;
    } else {
        $savemessage->contexturl = null;
    }

    if (!empty($eventdata->contexturlname)) {
        $savemessage->contexturlname = (string)$eventdata->contexturlname;
    } else {
        $savemessage->contexturlname = null;
    }

    $savemessage->timecreated = time();

    if (PHPUNIT_TEST and class_exists('phpunit_util')) {
        // Add some more tests to make sure the normal code can actually work.
        $componentdir = core_component::get_component_directory($eventdata->component);
        if (!$componentdir or !is_dir($componentdir)) {
            throw new coding_exception('Invalid component specified in message-send(): '.$eventdata->component);
        }
        if (!file_exists("$componentdir/db/messages.php")) {
            throw new coding_exception("$eventdata->component does not contain db/messages.php necessary for message_send()");
        }
        $messageproviders = null;
        include("$componentdir/db/messages.php");
        if (!isset($messageproviders[$eventdata->name])) {
            throw new coding_exception("Missing messaging defaults for event '$eventdata->name' in '$eventdata->component' messages.php file");
        }
        unset($componentdir);
        unset($messageproviders);
        // Now ask phpunit if it wants to catch this message.
        if (phpunit_util::is_redirecting_messages()) {
            $savemessage->timeread = time();
            $messageid = $DB->insert_record('message_read', $savemessage);
            $message = $DB->get_record('message_read', array('id'=>$messageid));
            phpunit_util::message_sent($message);
            return $messageid;
        }
    }

    // Fetch enabled processors.
    // If we are dealing with a message some processors may want to handle it regardless of user and site settings.
    if (empty($savemessage->notification)) {
        $processors = array_filter(get_message_processors(false), function($processor) {
            if ($processor->object->force_process_messages()) {
                return true;
            }

            return ($processor->enabled && $processor->configured);
        });
    } else {
        $processors = get_message_processors(true);
    }

    // Preset variables
    $processorlist = array();
    // Fill in the array of processors to be used based on default and user preferences
    foreach ($processors as $processor) {
        // Skip adding processors for internal user, if processor doesn't support sending message to internal user.
        if (!$usertoisrealuser && !$processor->object->can_send_to_any_users()) {
            continue;
        }

        // First find out permissions
        $defaultpreference = $processor->name.'_provider_'.$preferencebase.'_permitted';
        if (isset($defaultpreferences->{$defaultpreference})) {
            $permitted = $defaultpreferences->{$defaultpreference};
        } else {
            // MDL-25114 They supplied an $eventdata->component $eventdata->name combination which doesn't
            // exist in the message_provider table (thus there is no default settings for them).
            $preferrormsg = "Could not load preference $defaultpreference. Make sure the component and name you supplied
                    to message_send() are valid.";
            throw new coding_exception($preferrormsg);
        }

        // Find out if user has configured this output
        // Some processors cannot function without settings from the user
        $userisconfigured = $processor->object->is_user_configured($eventdata->userto);

        // DEBUG: notify if we are forcing unconfigured output
        if ($permitted == 'forced' && !$userisconfigured) {
            debugging('Attempt to force message delivery to user who has "'.$processor->name.'" output unconfigured', DEBUG_NORMAL);
        }

        // Populate the list of processors we will be using
        if (empty($savemessage->notification) && $processor->object->force_process_messages()) {
            $processorlist[] = $processor->name;
        } else if ($permitted == 'forced' && $userisconfigured) {
            // An admin is forcing users to use this message processor. Use this processor unconditionally.
            $processorlist[] = $processor->name;
        } else if ($permitted == 'permitted' && $userisconfigured && !$eventdata->userto->emailstop) {
            // User has not disabled notifications
            // See if user set any notification preferences, otherwise use site default ones
            $userpreferencename = 'message_provider_'.$preferencebase.'_'.$userstate;
            if ($userpreference = get_user_preferences($userpreferencename, null, $eventdata->userto)) {
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

    // Only cache messages, not notifications.
    if (empty($savemessage->notification)) {
        // Cache the timecreated value of the last message between these two users.
        $cache = cache::make('core', 'message_time_last_message_between_users');
        $key = \core_message\helper::get_last_message_time_created_cache_key($savemessage->useridfrom,
            $savemessage->useridto);
        $cache->set($key, $savemessage->timecreated);
    }

    // Store unread message just in case we get a fatal error any time later.
    $savemessage->id = $DB->insert_record('message', $savemessage);
    $eventdata->savedmessageid = $savemessage->id;

    // Let the manager do the sending or buffering when db transaction in progress.
    return \core\message\manager::send_message($eventdata, $savemessage, $processorlist);
}


/**
 * Updates the message_providers table with the current set of message providers
 *
 * @param string $component For example 'moodle', 'mod_forum' or 'block_quiz_results'
 * @return boolean True on success
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
        cache_helper::invalidate_by_definition('core', 'config', array(), 'message');
    }

    return true;
}

/**
 * This function populates default message preferences for all existing providers
 * when the new message processor is added.
 *
 * @param string $processorname The name of message processor plugin (e.g. 'email', 'jabber')
 * @throws invalid_parameter_exception if $processorname does not exist in the database
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
 * Setting default messaging preferences for particular message provider
 *
 * @param  string $component   The name of component (e.g. moodle, mod_forum, etc.)
 * @param  string $messagename The name of message provider
 * @param  array  $fileprovider The value of $messagename key in the array defined in plugin messages.php
 * @param  string $processorname The optional name of message processor
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
 * Returns the active providers for the user specified, based on capability
 *
 * @param int $userid id of user
 * @return array An array of message providers
 */
function message_get_providers_for_user($userid) {
    global $DB, $CFG;

    $providers = get_message_providers();

    // Ensure user is not allowed to configure instantmessage if it is globally disabled.
    if (!$CFG->messaging) {
        foreach ($providers as $providerid => $provider) {
            if ($provider->name == 'instantmessage') {
                unset($providers[$providerid]);
                break;
            }
        }
    }

    // If the component is an enrolment plugin, check it is enabled
    foreach ($providers as $providerid => $provider) {
        list($type, $name) = core_component::normalize_component($provider->component);
        if ($type == 'enrol' && !enrol_is_enabled($name)) {
            unset($providers[$providerid]);
        }
    }

    // Now we need to check capabilities. We need to eliminate the providers
    // where the user does not have the corresponding capability anywhere.
    // Here we deal with the common simple case of the user having the
    // capability in the system context. That handles $CFG->defaultuserroleid.
    // For the remaining providers/capabilities, we need to do a more complex
    // query involving all overrides everywhere.
    $unsureproviders = array();
    $unsurecapabilities = array();
    $systemcontext = context_system::instance();
    foreach ($providers as $providerid => $provider) {
        if (empty($provider->capability) || has_capability($provider->capability, $systemcontext, $userid)) {
            // The provider is relevant to this user.
            continue;
        }

        $unsureproviders[$providerid] = $provider;
        $unsurecapabilities[$provider->capability] = 1;
        unset($providers[$providerid]);
    }

    if (empty($unsureproviders)) {
        // More complex checks are not required.
        return $providers;
    }

    // Now check the unsure capabilities.
    list($capcondition, $params) = $DB->get_in_or_equal(
            array_keys($unsurecapabilities), SQL_PARAMS_NAMED);
    $params['userid'] = $userid;

    $sql = "SELECT DISTINCT rc.capability, 1

              FROM {role_assignments} ra
              JOIN {context} actx ON actx.id = ra.contextid
              JOIN {role_capabilities} rc ON rc.roleid = ra.roleid
              JOIN {context} cctx ON cctx.id = rc.contextid

             WHERE ra.userid = :userid
               AND rc.capability $capcondition
               AND rc.permission > 0
               AND (".$DB->sql_concat('actx.path', "'/'")." LIKE ".$DB->sql_concat('cctx.path', "'/%'").
               " OR ".$DB->sql_concat('cctx.path', "'/'")." LIKE ".$DB->sql_concat('actx.path', "'/%'").")";

    if (!empty($CFG->defaultfrontpageroleid)) {
        $frontpagecontext = context_course::instance(SITEID);

        list($capcondition2, $params2) = $DB->get_in_or_equal(
                array_keys($unsurecapabilities), SQL_PARAMS_NAMED);
        $params = array_merge($params, $params2);
        $params['frontpageroleid'] = $CFG->defaultfrontpageroleid;
        $params['frontpagepathpattern'] = $frontpagecontext->path . '/';

        $sql .= "
             UNION

            SELECT DISTINCT rc.capability, 1

              FROM {role_capabilities} rc
              JOIN {context} cctx ON cctx.id = rc.contextid

             WHERE rc.roleid = :frontpageroleid
               AND rc.capability $capcondition2
               AND rc.permission > 0
               AND ".$DB->sql_concat('cctx.path', "'/'")." LIKE :frontpagepathpattern";
    }

    $relevantcapabilities = $DB->get_records_sql_menu($sql, $params);

    // Add back any providers based on the detailed capability check.
    foreach ($unsureproviders as $providerid => $provider) {
        if (array_key_exists($provider->capability, $relevantcapabilities)) {
            $providers[$providerid] = $provider;
        }
    }

    return $providers;
}

/**
 * Gets the message providers that are in the database for this component.
 *
 * This is an internal function used within messagelib.php
 *
 * @see message_update_providers()
 * @param string $component A moodle component like 'moodle', 'mod_forum', 'block_quiz_results'
 * @return array An array of message providers
 */
function message_get_providers_from_db($component) {
    global $DB;

    return $DB->get_records('message_providers', array('component'=>$component), '', 'name, id, component, capability');  // Name is unique per component
}

/**
 * Loads the messages definitions for a component from file
 *
 * If no messages are defined for the component, return an empty array.
 * This is an internal function used within messagelib.php
 *
 * @see message_update_providers()
 * @see message_update_processors()
 * @param string $component A moodle component like 'moodle', 'mod_forum', 'block_quiz_results'
 * @return array An array of message providers or empty array if not exists
 */
function message_get_providers_from_file($component) {
    $defpath = core_component::get_component_directory($component).'/db/messages.php';

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
 * Remove all message providers for particular component and corresponding settings
 *
 * @param string $component A moodle component like 'moodle', 'mod_forum', 'block_quiz_results'
 * @return void
 */
function message_provider_uninstall($component) {
    global $DB;

    $transaction = $DB->start_delegated_transaction();
    $DB->delete_records('message_providers', array('component' => $component));
    $DB->delete_records_select('config_plugins', "plugin = 'message' AND ".$DB->sql_like('name', '?', false), array("%_provider_{$component}_%"));
    $DB->delete_records_select('user_preferences', $DB->sql_like('name', '?', false), array("message_provider_{$component}_%"));
    $transaction->allow_commit();
    // Purge all messaging settings from the caches. They are stored by plugin so we have to clear all message settings.
    cache_helper::invalidate_by_definition('core', 'config', array(), 'message');
}

/**
 * Uninstall a message processor
 *
 * @param string $name A message processor name like 'email', 'jabber'
 */
function message_processor_uninstall($name) {
    global $DB;

    $transaction = $DB->start_delegated_transaction();
    $DB->delete_records('message_processors', array('name' => $name));
    $DB->delete_records_select('config_plugins', "plugin = ?", array("message_{$name}"));
    // delete permission preferences only, we do not care about loggedin/loggedoff
    // defaults, they will be removed on the next attempt to update the preferences
    $DB->delete_records_select('config_plugins', "plugin = 'message' AND ".$DB->sql_like('name', '?', false), array("{$name}_provider_%"));
    $transaction->allow_commit();
    // Purge all messaging settings from the caches. They are stored by plugin so we have to clear all message settings.
    cache_helper::invalidate_by_definition('core', 'config', array(), array('message', "message_{$name}"));
}
