<?php   // $Id$

require_once('../config.php');
require_once($CFG->libdir.'/messagelib.php');


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
$coursecontext   = get_context_instance(CONTEXT_COURSE, $course->id);


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


/// Display page header
$streditmymessage = get_string('editmymessage', 'message');
$strparticipants  = get_string('participants');
$userfullname     = fullname($user, true);

$navlinks = array();
if (has_capability('moodle/course:viewparticipants', $coursecontext) || 
    has_capability('moodle/site:viewparticipants', $systemcontext)) {
    $navlinks[] = array('name' => $strparticipants, 'link' => "index.php?id=$course->id", 'type' => 'misc');
}
$navlinks[] = array('name' => $userfullname,
                    'link' => "view.php?id=$user->id&amp;course=$course->id",
                    'type' => 'misc');
$navlinks[] = array('name' => $streditmymessage, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

if ($course->id != SITEID) {
    print_header("$course->shortname: $streditmymessage", "$course->fullname: $streditmymessage", $navigation);
} else {
    print_header("$course->shortname: $streditmymessage", $course->fullname, $navigation);
}
/// Print tabs at the top
$showroles = 1;
$currenttab = 'editmessage';
require('../user/tabs.php');

notify('WARNING: This interface is still under construction!');



    echo '<h1>Private Messaging Options</h1>';
    echo '<table>';
    echo '<tr><td>Popup Windows on New Message</td><td><input type="checkbox" name=""></td></tr>';
    echo '<tr><td>lock unknown users</td><td><input type="checkbox" name=""></td></tr>';
    echo '<tr><td>Beep on new message</td><td><input type="checkbox" name=""></td></tr>';
    echo '<tr><td>No frames and JavaScript</td><td><input type="checkbox" name=""></td></tr>';
    echo '</table>';
    
    
    echo '<h1>Message Sources</h1>';
    // Get all the known providers
    $providers = message_get_my_providers();
    //get a listing of all the message processors
    $processors = $DB->get_records('message_processors');
    $number_procs = count($processors);
    echo '<table cellpadding="2"><tr><td>&nbsp;</td>';
    foreach ( $processors as $processorid => $processor){
        echo '<td>'.$processor->name.'</td>';
    }
    echo '</tr>';
    foreach ( $providers as $providerid => $provider){
        $providername = get_string('messageprovider:'.$provider->name, $provider->component);
        echo '<tr><td>'.$providername.'</td><td colspan="'.$number_procs.'"></td></tr>';
        foreach (array('loggedin', 'loggedoff') as $state){
            $state_res = get_string($state, 'message');
            echo '<tr><td align="right">'.$state_res.'</td>';
            foreach ( $processors as $processorid => $processor){
                echo '<td><input type="checkbox" name=""></td>';
            }
        }
    }
    echo '</table>';
    
    echo '<h2>Destination Configuration</h2>';
    
    echo "</form></center>";

/// and proper footer
print_footer($course);

?>

