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
 * @author Luis Rodrigues and Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package message
 */

require_once('../config.php');

$userid = optional_param('id', $USER->id, PARAM_INT);    // user id
$course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)

$url = new moodle_url('/message/edit.php');
if ($userid !== $USER->id) {
    $url->param('id', $userid);
}
if ($course !== SITEID) {
    $url->param('course', $course);
}
$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id' => $course))) {
    print_error('invalidcourseid');
}

if ($course->id != SITEID) {
    require_login($course);

} else {
    if (!isloggedin()) {
        if (empty($SESSION->wantsurl)) {
            $SESSION->wantsurl = $CFG->httpswwwroot.'/message/edit.php';
        }
        redirect(get_login_url());
    }
}

if (isguestuser()) {
    print_error('guestnoeditmessage', 'message');
}

if (!$user = $DB->get_record('user', array('id' => $userid))) {
    print_error('invaliduserid');
}

$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
$personalcontext = get_context_instance(CONTEXT_USER, $user->id);
$coursecontext   = get_context_instance(CONTEXT_COURSE, $course->id);

$PAGE->set_context(get_context_instance(CONTEXT_USER, $USER->id));
$PAGE->set_pagelayout('course');

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

/// Save new preferences if data was submited

if (($form = data_submitted()) && confirm_sesskey()) {
    $preferences = array();

/// Set all the preferences for all the message providers
    $providers = message_get_my_providers();
    foreach ( $providers as $providerid => $provider){
        foreach (array('loggedin', 'loggedoff') as $state){
            $linepref = '';
            $componentproviderstate = $provider->component.'_'.$provider->name.'_'.$state;
            if (array_key_exists($componentproviderstate, $form)) {
                foreach ($form->{$componentproviderstate} as $process=>$one){
                    if ($linepref == ''){
                        $linepref = $process;
                    } else {
                        $linepref .= ','.$process;
                    }
                }
            }
            $preferences['message_provider_'.$provider->component.'_'.$provider->name.'_'.$state] = $linepref;
        }
    }

/// Set all the processor options as well
    $processors = $DB->get_records('message_processors');
    foreach ( $processors as $processorid => $processor){
        $processorfile = $CFG->dirroot. '/message/output/'.$processor->name.'/message_output_'.$processor->name.'.php';
        if ( is_readable($processorfile) ) {
            include_once( $processorfile );

            $processclass = 'message_output_' . $processor->name;
            if ( class_exists($processclass) ){
                $pclass = new $processclass();
                $pclass->process_form($form, $preferences);
            } else{
                print_error('errorcallingprocessor', 'message');
            }
        }
    }

/// Save all the new preferences to the database
    if (!set_user_preferences( $preferences, $user->id ) ){
        print_error('cannotupdateusermsgpref');
    }

    redirect("$CFG->wwwroot/message/edit.php?id=$user->id&course=$course->id");
}

/// Load preferences
$preferences = new object();

/// Get providers preferences
$providers = message_get_my_providers();
foreach ( $providers as $providerid => $provider){
    foreach (array('loggedin', 'loggedoff') as $state){
        $linepref = get_user_preferences('message_provider_'.$provider->component.'_'.$provider->name.'_'.$state, '', $user->id);
        if ($linepref == ''){
            continue;
        }
        $lineprefarray = explode(',', $linepref);
        $preferences->{$provider->component.'_'.$provider->name.'_'.$state} = array();
        foreach ($lineprefarray as $pref){
            $preferences->{$provider->component.'_'.$provider->name.'_'.$state}[$pref] = 1;
        }
    }
}

/// For every processors put its options on the form (need to get function from processor's lib.php)
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
            print_error('errorcallingprocessor', 'message');
        }
    }
}

/// Display page header
$streditmymessage = get_string('editmymessage', 'message');
$strparticipants  = get_string('participants');
$userfullname     = fullname($user, true);

if ($user->id==$USER->id) {
    $PAGE->navigation->extend_for_user($USER);
} else {
    if (has_capability('moodle/course:viewparticipants', $coursecontext) ||
        has_capability('moodle/site:viewparticipants', $systemcontext)) {
        $PAGE->navbar->add($strparticipants, new moodle_url('/message/index.php', array('id'=>$course->id)));
    }
    $PAGE->navbar->add($userfullname, new moodle_url('/user/view.php', array('id'=>$user->id, 'course'=>$course->id)));
    $PAGE->navbar->add($streditmymessage);
}

$PAGE->set_title("$course->shortname: $streditmymessage");
if ($course->id != SITEID) {
    $PAGE->set_heading("$course->fullname: $streditmymessage");
} else {
    $PAGE->set_heading($course->fullname);
}
echo $OUTPUT->header();

/// Start the form.  We're not using mform here because of our special formatting needs ...
echo '<form class="mform" method="post" action="'.$CFG->wwwroot.'/message/edit.php">';

/// Settings table...
echo '<fieldset id="providers" class="clearfix">';
echo '<legend class="ftoggler">'.get_string('providers_config', 'message').'</legend>';
$providers = message_get_my_providers();
$processors = $DB->get_records('message_processors');
$number_procs = count($processors);
echo '<table cellpadding="2"><tr><td>&nbsp;</td>'."\n";
foreach ( $processors as $processorid => $processor){
    echo '<th align="center">'.get_string('pluignname', 'message_'.$processor->name).'</th>';
}
echo '</tr>';

foreach ( $providers as $providerid => $provider){
    $providername = get_string('messageprovider:'.$provider->name, $provider->component);

/// TODO XXX: This is only a quick hack ... helpfile locations should be provided as part of the provider definition
    if ($provider->component == 'moodle') {
        $helpbtn = $OUTPUT->old_help_icon('moodle_'.$provider->name, $providername, 'message');
    } else {
        $helpbtn = $OUTPUT->old_help_icon('message_'.$provider->name, $providername, basename($provider->component));
    }

    echo '<tr><th align="right">'.$providername.$helpbtn.'</th><td colspan="'.$number_procs.'"></td></tr>'."\n";
    foreach (array('loggedin', 'loggedoff') as $state){
        $state_res = get_string($state, 'message');
        echo '<tr><td align="right">'.$state_res.'</td>'."\n";
        foreach ( $processors as $processorid => $processor) {
            if (!isset($preferences->{$provider->component.'_'.$provider->name.'_'.$state})) {
                $checked = '';
            } else if (!isset($preferences->{$provider->component.'_'.$provider->name.'_'.$state}[$processor->name])) {
                $checked = '';
            } else {
                $checked = $preferences->{$provider->component.'_'.$provider->name.'_'.$state}[$processor->name]==1?" checked=\"checked\"":"";
            }
            echo '<td align="center"><input type="checkbox" name="'.$provider->component.'_'.$provider->name.'_'.$state.'['.$processor->name.']" '.$checked.' /></td>'."\n";
        }
        echo '</tr>'."\n";
    }
}
echo '</table>';
echo '</fieldset>';

/// Show all the message processors
$processors = $DB->get_records('message_processors');

foreach ($processors as $processorid => $processor) {
    $processorfile = $CFG->dirroot. '/message/output/'.$processor->name.'/message_output_'.$processor->name.'.php';
    if (is_readable($processorfile)) {
        include_once($processorfile);
        $processclass = 'message_output_' . $processor->name;

        if (class_exists($processclass)) {
            $pclass = new $processclass();
            echo '<fieldset id="messageprocessor_'.$processor->name.'" class="clearfix">';
            echo '<legend class="ftoggler">'.get_string('pluignname', 'message_'.$processor->name).'</legend>';

            echo $pclass->config_form($preferences);

            echo '</fieldset>';

        } else{
            print_error('errorcallingprocessor', 'message');
        }
    }
}

echo '<div><input type="hidden" name="sesskey" value="'.sesskey().'" /></div>';
echo '<div style="text-align:center"><input name="submit" value="'. get_string('updatemyprofile') .'" type="submit" /></div>';

echo "</form>";

echo $OUTPUT->footer();

