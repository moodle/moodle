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
 * Allows you to edit a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package user
 */

require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/user/edit_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

//HTTPS is required in this page when $CFG->loginhttps enabled
$PAGE->https_required();

$userid = optional_param('id', $USER->id, PARAM_INT);    // user id
$course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)
$cancelemailchange = optional_param('cancelemailchange', 0, PARAM_INT);   // course id (defaults to Site)

$PAGE->set_url('/user/edit.php', array('course'=>$course, 'id'=>$userid));

if (!$course = $DB->get_record('course', array('id'=>$course))) {
    print_error('invalidcourseid');
}

if ($course->id != SITEID) {
    require_login($course);
} else if (!isloggedin()) {
    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = $CFG->httpswwwroot.'/user/edit.php';
    }
    redirect(get_login_url());
} else {
    $PAGE->set_context(get_system_context());
    $PAGE->set_pagelayout('standard');
}

// Guest can not edit
if (isguestuser()) {
    print_error('guestnoeditprofile');
}

// The user profile we are editing
if (!$user = $DB->get_record('user', array('id'=>$userid))) {
    print_error('invaliduserid');
}

// Guest can not be edited
if (isguestuser($user)) {
    print_error('guestnoeditprofile');
}

// User interests separated by commas
if (!empty($CFG->usetags)) {
    require_once($CFG->dirroot.'/tag/lib.php');
    $user->interests = tag_get_tags_array('user', $user->id);
}

// remote users cannot be edited
if (is_mnet_remote_user($user)) {
    if (user_not_fully_set_up($user)) {
        $hostwwwroot = $DB->get_field('mnet_host', 'wwwroot', array('id'=>$user->mnethostid));
        print_error('usernotfullysetup', 'mnet', '', $hostwwwroot);
    }
    redirect($CFG->wwwroot . "/user/view.php?course={$course->id}");
}

// load the appropriate auth plugin
$userauth = get_auth_plugin($user->auth);

if (!$userauth->can_edit_profile()) {
    print_error('noprofileedit', 'auth');
}

if ($editurl = $userauth->edit_profile_url()) {
    // this internal script not used
    redirect($editurl);
}

if ($course->id == SITEID) {
    $coursecontext = get_context_instance(CONTEXT_SYSTEM);   // SYSTEM context
} else {
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context
}
$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
$personalcontext = get_context_instance(CONTEXT_USER, $user->id);

// check access control
if ($user->id == $USER->id) {
    //editing own profile - require_login() MUST NOT be used here, it would result in infinite loop!
    if (!has_capability('moodle/user:editownprofile', $systemcontext)) {
        print_error('cannotedityourprofile');
    }

} else {
    // teachers, parents, etc.
    require_capability('moodle/user:editprofile', $personalcontext);
    // no editing of guest user account
    if (isguestuser($user->id)) {
        print_error('guestnoeditprofileother');
    }
    // no editing of primary admin!
    if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins
        print_error('useradmineditadmin');
    }
}

if ($user->deleted) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userdeleted'));
    echo $OUTPUT->footer();
    die;
}

// Process email change cancellation
if ($cancelemailchange) {
    cancel_email_update($user->id);
}

//load user preferences
useredit_load_preferences($user);

//Load custom profile fields data
profile_load_data($user);


// Prepare the editor and create form
$editoroptions = array(
    'maxfiles'   => EDITOR_UNLIMITED_FILES,
    'maxbytes'   => $CFG->maxbytes,
    'trusttext'  => false,
    'forcehttps' => false,
    'context'    => $personalcontext
);

$user = file_prepare_standard_editor($user, 'description', $editoroptions, $personalcontext, 'user', 'profile', 0);
$userform = new user_edit_form(null, array('editoroptions'=>$editoroptions));
if (empty($user->country)) {
    // MDL-16308 - we must unset the value here so $CFG->country can be used as default one
    unset($user->country);
}
$userform->set_data($user);

$email_changed = false;

if ($usernew = $userform->get_data()) {

    add_to_log($course->id, 'user', 'update', "view.php?id=$user->id&course=$course->id", '');

    $email_changed_html = '';

    if ($CFG->emailchangeconfirmation) {
        // Handle change of email carefully for non-trusted users
        if (isset($usernew->email) and $user->email != $usernew->email && !has_capability('moodle/user:update', $systemcontext)) {
            $a = new stdClass();
            $a->newemail = $usernew->preference_newemail = $usernew->email;
            $usernew->preference_newemailkey = random_string(20);
            $usernew->preference_newemailattemptsleft = 3;
            $a->oldemail = $usernew->email = $user->email;

            $email_changed_html = $OUTPUT->box(get_string('auth_changingemailaddress', 'auth', $a), 'generalbox', 'notice');
            $email_changed_html .= $OUTPUT->continue_button("$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id");
            $email_changed = true;
        }
    }

    $authplugin = get_auth_plugin($user->auth);

    $usernew->timemodified = time();

    // description editor element may not exist!
    if (isset($usernew->description_editor)) {
        $usernew = file_postupdate_standard_editor($usernew, 'description', $editoroptions, $personalcontext, 'user', 'profile', 0);
    }

    $DB->update_record('user', $usernew);

    // pass a true $userold here
    if (! $authplugin->user_update($user, $usernew)) {
        // auth update failed, rollback for moodle
        $DB->update_record('user', $user);
        print_error('cannotupdateprofile');
    }

    //update preferences
    useredit_update_user_preference($usernew);

    //update interests
    if (!empty($CFG->usetags)) {
        useredit_update_interests($usernew, $usernew->interests);
    }

    //update user picture
    if (!empty($CFG->gdversion) and empty($CFG->disableuserimages)) {
        useredit_update_picture($usernew, $userform);
    }

    // update mail bounces
    useredit_update_bounces($user, $usernew);

    /// update forum track preference
    useredit_update_trackforums($user, $usernew);

    // save custom profile fields data
    profile_save_data($usernew);

    // If email was changed, send confirmation email now
    if ($email_changed && $CFG->emailchangeconfirmation) {
        $temp_user = fullclone($user);
        $temp_user->email = $usernew->preference_newemail;

        $a = new stdClass();
        $a->url = $CFG->wwwroot . '/user/emailupdate.php?key=' . $usernew->preference_newemailkey . '&id=' . $user->id;
        $a->site = format_string($SITE->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, SITEID)));
        $a->fullname = fullname($user, true);

        $emailupdatemessage = get_string('emailupdatemessage', 'auth', $a);
        $emailupdatetitle = get_string('emailupdatetitle', 'auth', $a);

        //email confirmation directly rather than using messaging so they will definitely get an email
        if (!$mail_results = email_to_user($temp_user, get_admin(), $emailupdatetitle, $emailupdatemessage)) {
            die("could not send email!");
        }
    }

    // reload from db
    $usernew = $DB->get_record('user', array('id'=>$user->id));
    events_trigger('user_updated', $usernew);

    if ($USER->id == $user->id) {
        // Override old $USER session variable if needed
        foreach ((array)$usernew as $variable => $value) {
            $USER->$variable = $value;
        }
        // preload custom fields
        profile_load_custom_fields($USER);
    }

    if (is_siteadmin() and empty($SITE->shortname)) {
        // fresh cli install - we need to finish site settings
        redirect(new moodle_url('/admin/index.php'));
    }

    if (!$email_changed || !$CFG->emailchangeconfirmation) {
        redirect("$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id");
    }
}

// make sure we really are on the https page when https login required
$PAGE->verify_https_required();


/// Display page header
$streditmyprofile = get_string('editmyprofile');
$strparticipants  = get_string('participants');
$userfullname     = fullname($user, true);

$PAGE->set_title("$course->shortname: $streditmyprofile");
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

if ($email_changed) {
    echo $email_changed_html;
} else {
/// Finally display THE form
    $userform->display();
}

/// and proper footer
echo $OUTPUT->footer();

