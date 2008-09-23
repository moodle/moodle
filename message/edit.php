<?php   // $Id$

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

//save new preferences if data was submited
if ( ($form = data_submitted()) && confirm_sesskey()) {
    $preferences = array();

    /// Set the overall preferences
    $preferences['message_showmessagewindow'] = $form->showmessagewindow?1:0;
    $preferences['message_blocknoncontacts']  = $form->blocknoncontacts?1:0;
    $preferences['message_beepnewmessage']    = $form->beepnewmessage?1:0;
    $preferences['message_noframesjs']        = $form->noframesjs?1:0;
    
    /// Set all the preferences for all the message providers
    $providers = message_get_my_providers();
    foreach ( $providers as $providerid => $provider){
        foreach (array('loggedin', 'loggedoff') as $state){
            $linepref = '';
            foreach ($form->{$provider->component.'_'.$provider->name.'_'.$state} as $process=>$one){                
                if ($linepref == ''){ 
                    $linepref = $process;
                } else { 
                    $linepref .= ','.$process;
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
                error('Error calling defined processor');
            }
        }
    }
    /// Save all the new preferences to the database
    if (!set_user_preferences( $preferences, $user->id ) ){
        error('Error updating user message preferences');
    }
    
    redirect("$CFG->wwwroot/message/edit.php?id=$user->id&course=$course->id");
}

//load preferences so show
$preferences = new object();

//get the message general preferences
$preferences->showmessagewindow =  get_user_preferences( 'message_showmessagewindow', 1, $user->id);
$preferences->blocknoncontacts  =  get_user_preferences( 'message_blocknoncontacts', '', $user->id);
$preferences->beepnewmessage    =  get_user_preferences( 'message_beepnewmessage', '', $user->id);
$preferences->noframesjs        =  get_user_preferences( 'message_noframesjs', '', $user->id);
//get providers preferences
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

echo '<form method="post" action="'.$CFG->wwwroot.'/message/edit.php">';

echo '<div class="generalbox">';
echo '<table>';
echo '<tr><td colspan="2"><h3>'.get_string('private_config', 'message').'</h3></td></tr>';
echo '<tr><td>'.get_string('showmessagewindow', 'message').'</td><td><input type="checkbox" name="showmessagewindow" '.($preferences->showmessagewindow==1?" checked=\"checked\"":"").' /></td></tr>';
echo '<tr><td>'.get_string('blocknoncontacts', 'message').'</td><td><input type="checkbox" name="blocknoncontacts" '.($preferences->blocknoncontacts==1?" checked=\"checked\"":"").' /></td></tr>';
echo '<tr><td>'.get_string('beepnewmessage', 'message').'</td><td><input type="checkbox" name="beepnewmessage" '.($preferences->beepnewmessage==1?" checked=\"checked\"":"").' /></td></tr>';
echo '<tr><td>'.get_string('noframesjs', 'message').'</td><td><input type="checkbox" name="noframesjs" '.($preferences->noframesjs==1?" checked=\"checked\"":"").' /></td></tr>';
echo '</table>';
echo '</div>';

//output settings table
echo '<div class="generalbox">';
echo '<table>';
echo '<tr><td><h3>'.get_string('providers_config', 'message').'</h3></td></tr>'."\n";
$providers = message_get_my_providers();
$processors = $DB->get_records('message_processors');
$number_procs = count($processors);
echo '<tr><td>'; echo '<table cellpadding="2"><tr><td>&nbsp;</td>'."\n";
foreach ( $processors as $processorid => $processor){
    echo '<td align="center" style="width:120px">'.get_string($processor->name, 'messageprocessor_'.$processor->name).'</td>';
}
echo '</tr>';

///  TODO:  (from martin)
///         1) Can we show the popuyp first (it's the default and always there)
///         2) Can we NOT show plugins here unless they have been configured in the section below

foreach ( $providers as $providerid => $provider){
    $providername = get_string('messageprovider:'.$provider->name, $provider->component);
    echo '<tr><td align="right">'.$providername.'</td><td colspan="'.$number_procs.'"></td></tr>'."\n";
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
echo '</td></tr></table>';
echo '</div>';

echo '<div class="generalbox">';
echo '<table>';
echo '<tr><td colspan="2"><h3>'.get_string('processor_config', 'message').'</h3></td></tr>'."\n";
//get a listing of all the message processors
$processors = $DB->get_records('message_processors');

///  TODO:  (from martin)
///         1) For email plugin, if the email is blank can we make it default to profile email address?   Show this to use by adding "Default: martin@moodle.com" after the actual field for setting a new one.

foreach ( $processors as $processorid => $processor){
    $processorfile = $CFG->dirroot. '/message/output/'.$processor->name.'/message_output_'.$processor->name.'.php';    
    if ( is_readable($processorfile) ) {        
        include_once( $processorfile );                
        $processclass = 'message_output_' . $processor->name;                
        if (class_exists($processclass)) {                    
            $pclass = new $processclass();
            $pclass->config_form($preferences); 
        } else{ 
            error('Error calling defined processor');
        }
    }
}
echo '</table>';
echo '</div>';

echo '<p><input type="hidden" name="sesskey" value="'.sesskey().'" /> </p>';
echo '<div style="text-align:center"><input name="submit" value="'. get_string('updatemyprofile') .'" type="submit" /></div>';

echo "</form>";

/// and proper footer
print_footer($course);

?>
