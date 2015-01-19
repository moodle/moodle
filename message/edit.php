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
 * Edit user message preferences
 *
 * @package    core_message
 * @copyright  2008 Luis Rodrigues and Martin Dougiamas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/message/lib.php');

$userid = optional_param('id', 0, PARAM_INT);    // User id.
$disableall = optional_param('disableall', 0, PARAM_BOOL); //disable all of this user's notifications

if (!$userid) {
    $userid = $USER->id;
}

$url = new moodle_url('/message/edit.php');
$url->param('id', $userid);

$PAGE->set_url($url);
$PAGE->set_popup_notification_allowed(false); // We are within the messaging system so don't show message popups

require_login();

if (isguestuser()) {
    print_error('guestnoeditmessage', 'message');
}

if (!$user = $DB->get_record('user', array('id' => $userid))) {
    print_error('invaliduserid');
}

$systemcontext   = context_system::instance();
$personalcontext = context_user::instance($user->id);

$PAGE->set_context($personalcontext);
$PAGE->set_pagelayout('admin');
$PAGE->requires->js_init_call('M.core_message.init_editsettings');

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
    // no editing of admins by non admins!
    if (is_siteadmin($user) and !is_siteadmin($USER)) {
        print_error('useradmineditadmin');
    }
    $PAGE->navigation->extend_for_user($user);
}

// Fetch message providers
$providers = message_get_providers_for_user($user->id);

/// Save new preferences if data was submitted

if (($form = data_submitted()) && confirm_sesskey()) {
    $preferences = array();

    //only update the user's "emailstop" if its actually changed
    if ( $user->emailstop != $disableall ) {
        $user->emailstop = $disableall;
        $DB->set_field('user', 'emailstop', $user->emailstop, array("id"=>$user->id));
    }

    // Turning on emailstop disables the preference checkboxes in the browser.
    // Disabled checkboxes may not be submitted with the form making them look (incorrectly) like they've been unchecked.
    // Only alter the messaging preferences if emailstop is turned off
    if (!$user->emailstop) {
        foreach ($providers as $provider) {
            $componentproviderbase = $provider->component.'_'.$provider->name;
            foreach (array('loggedin', 'loggedoff') as $state) {
                $linepref = '';
                $componentproviderstate = $componentproviderbase.'_'.$state;
                if (array_key_exists($componentproviderstate, $form)) {
                    foreach (array_keys($form->{$componentproviderstate}) as $process) {
                        if ($linepref == ''){
                            $linepref = $process;
                        } else {
                            $linepref .= ','.$process;
                        }
                    }
                }
                if (empty($linepref)) {
                    $linepref = 'none';
                }
                $preferences['message_provider_'.$provider->component.'_'.$provider->name.'_'.$state] = $linepref;
            }
        }
    }

/// Set all the processor options as well
    $processors = get_message_processors(true);
    foreach ($processors as $processor) {
        $processor->object->process_form($form, $preferences);
    }

    //process general messaging preferences
    $preferences['message_blocknoncontacts'] = !empty($form->blocknoncontacts)?1:0;
    $preferences['message_beepnewmessage']   = !empty($form->beepnewmessage)?1:0;

    // Save all the new preferences to the database
    if (!set_user_preferences($preferences, $user->id)) {
        print_error('cannotupdateusermsgpref');
    }

    redirect("$CFG->wwwroot/message/edit.php?id=$user->id");
}

/// Load preferences
$preferences = new stdClass();
$preferences->userdefaultemail = $user->email;//may be displayed by the email processor

/// Get providers preferences
foreach ($providers as $provider) {
    foreach (array('loggedin', 'loggedoff') as $state) {
        $linepref = get_user_preferences('message_provider_'.$provider->component.'_'.$provider->name.'_'.$state, '', $user->id);
        if ($linepref == ''){
            continue;
        }
        $lineprefarray = explode(',', $linepref);
        $preferences->{$provider->component.'_'.$provider->name.'_'.$state} = array();
        foreach ($lineprefarray as $pref) {
            $preferences->{$provider->component.'_'.$provider->name.'_'.$state}[$pref] = 1;
        }
    }
}

// Load all processors
$processors = get_message_processors();
/// For every processors put its options on the form (need to get function from processor's lib.php)
foreach ($processors as $processor) {
    $processor->object->load_data($preferences, $user->id);
}

//load general messaging preferences
$preferences->blocknoncontacts  =  get_user_preferences( 'message_blocknoncontacts', '', $user->id);
$preferences->beepnewmessage    =  get_user_preferences( 'message_beepnewmessage', '', $user->id);

/// Display page header
$strmessaging = get_string('messaging', 'message');
$PAGE->set_title($strmessaging);
$PAGE->set_heading($strmessaging);

// Grab the renderer
$renderer = $PAGE->get_renderer('core', 'message');
// Fetch default (site) preferences
$defaultpreferences = get_message_output_default_preferences();

$messagingoptions = $renderer->manage_messagingoptions($processors, $providers, $preferences, $defaultpreferences, $user->emailstop);

echo $OUTPUT->header();
echo $messagingoptions;
echo $OUTPUT->footer();

