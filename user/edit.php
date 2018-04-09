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
 * @package core_user
 */

require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/user/edit_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');

$userid = optional_param('id', $USER->id, PARAM_INT);    // User id.
$course = optional_param('course', SITEID, PARAM_INT);   // Course id (defaults to Site).
$returnto = optional_param('returnto', null, PARAM_ALPHA);  // Code determining where to return to after save.
$cancelemailchange = optional_param('cancelemailchange', 0, PARAM_INT);   // Course id (defaults to Site).

$PAGE->set_url('/user/edit.php', array('course' => $course, 'id' => $userid));

if (!$course = $DB->get_record('course', array('id' => $course))) {
    print_error('invalidcourseid');
}

if ($course->id != SITEID) {
    require_login($course);
} else if (!isloggedin()) {
    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = $CFG->wwwroot.'/user/edit.php';
    }
    redirect(get_login_url());
} else {
    $PAGE->set_context(context_system::instance());
}

// Guest can not edit.
if (isguestuser()) {
    print_error('guestnoeditprofile');
}

// The user profile we are editing.
if (!$user = $DB->get_record('user', array('id' => $userid))) {
    print_error('invaliduserid');
}

// Guest can not be edited.
if (isguestuser($user)) {
    print_error('guestnoeditprofile');
}

// User interests separated by commas.
$user->interests = core_tag_tag::get_item_tags_array('core', 'user', $user->id);

// Remote users cannot be edited. Note we have to perform the strict user_not_fully_set_up() check.
// Otherwise the remote user could end up in endless loop between user/view.php and here.
// Required custom fields are not supported in MNet environment anyway.
if (is_mnet_remote_user($user)) {
    if (user_not_fully_set_up($user, true)) {
        $hostwwwroot = $DB->get_field('mnet_host', 'wwwroot', array('id' => $user->mnethostid));
        print_error('usernotfullysetup', 'mnet', '', $hostwwwroot);
    }
    redirect($CFG->wwwroot . "/user/view.php?course={$course->id}");
}

// Load the appropriate auth plugin.
$userauth = get_auth_plugin($user->auth);

if (!$userauth->can_edit_profile()) {
    print_error('noprofileedit', 'auth');
}

if ($editurl = $userauth->edit_profile_url()) {
    // This internal script not used.
    redirect($editurl);
}

if ($course->id == SITEID) {
    $coursecontext = context_system::instance();   // SYSTEM context.
} else {
    $coursecontext = context_course::instance($course->id);   // Course context.
}
$systemcontext   = context_system::instance();
$personalcontext = context_user::instance($user->id);

// Check access control.
if ($user->id == $USER->id) {
    // Editing own profile - require_login() MUST NOT be used here, it would result in infinite loop!
    if (!has_capability('moodle/user:editownprofile', $systemcontext)) {
        print_error('cannotedityourprofile');
    }

} else {
    // Teachers, parents, etc.
    require_capability('moodle/user:editprofile', $personalcontext);
    // No editing of guest user account.
    if (isguestuser($user->id)) {
        print_error('guestnoeditprofileother');
    }
    // No editing of primary admin!
    if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins.
        print_error('useradmineditadmin');
    }
}

if ($user->deleted) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userdeleted'));
    echo $OUTPUT->footer();
    die;
}

$PAGE->set_pagelayout('admin');
$PAGE->set_context($personalcontext);
if ($USER->id != $user->id) {
    $PAGE->navigation->extend_for_user($user);
} else {
    if ($node = $PAGE->navigation->find('myprofile', navigation_node::TYPE_ROOTNODE)) {
        $node->force_open();
    }
}

// Process email change cancellation.
if ($cancelemailchange) {
    cancel_email_update($user->id);
}

// Load user preferences.
useredit_load_preferences($user);

// Load custom profile fields data.
profile_load_data($user);


// Prepare the editor and create form.
$editoroptions = array(
    'maxfiles'   => EDITOR_UNLIMITED_FILES,
    'maxbytes'   => $CFG->maxbytes,
    'trusttext'  => false,
    'forcehttps' => false,
    'context'    => $personalcontext
);

$user = file_prepare_standard_editor($user, 'description', $editoroptions, $personalcontext, 'user', 'profile', 0);
// Prepare filemanager draft area.
$draftitemid = 0;
$filemanagercontext = $editoroptions['context'];
$filemanageroptions = array('maxbytes'       => $CFG->maxbytes,
                             'subdirs'        => 0,
                             'maxfiles'       => 1,
                             'accepted_types' => 'web_image');
file_prepare_draft_area($draftitemid, $filemanagercontext->id, 'user', 'newicon', 0, $filemanageroptions);
$user->imagefile = $draftitemid;
// Create form.
$userform = new user_edit_form(new moodle_url($PAGE->url, array('returnto' => $returnto)), array(
    'editoroptions' => $editoroptions,
    'filemanageroptions' => $filemanageroptions,
    'user' => $user));

$emailchanged = false;

// Deciding where to send the user back in most cases.
if ($returnto === 'profile') {
    if ($course->id != SITEID) {
        $returnurl = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $course->id));
    } else {
        $returnurl = new moodle_url('/user/profile.php', array('id' => $user->id));
    }
} else {
    $returnurl = new moodle_url('/user/preferences.php', array('userid' => $user->id));
}

if ($userform->is_cancelled()) {
    redirect($returnurl);
} else if ($usernew = $userform->get_data()) {

    $emailchangedhtml = '';

    if ($CFG->emailchangeconfirmation) {
        // Users with 'moodle/user:update' can change their email address immediately.
        // Other users require a confirmation email.
        if (isset($usernew->email) and $user->email != $usernew->email && !has_capability('moodle/user:update', $systemcontext)) {
            $a = new stdClass();
            $emailchangedkey = random_string(20);
            set_user_preference('newemail', $usernew->email, $user->id);
            set_user_preference('newemailkey', $emailchangedkey, $user->id);
            set_user_preference('newemailattemptsleft', 3, $user->id);

            $a->newemail = $emailchanged = $usernew->email;
            $a->oldemail = $usernew->email = $user->email;

            $emailchangedhtml = $OUTPUT->box(get_string('auth_changingemailaddress', 'auth', $a), 'generalbox', 'notice');
            $emailchangedhtml .= $OUTPUT->continue_button($returnurl);
        }
    }

    $authplugin = get_auth_plugin($user->auth);

    $usernew->timemodified = time();

    // Description editor element may not exist!
    if (isset($usernew->description_editor) && isset($usernew->description_editor['format'])) {
        $usernew = file_postupdate_standard_editor($usernew, 'description', $editoroptions, $personalcontext, 'user', 'profile', 0);
    }

    // Pass a true old $user here.
    if (!$authplugin->user_update($user, $usernew)) {
        // Auth update failed.
        print_error('cannotupdateprofile');
    }

    // Update user with new profile data.
    user_update_user($usernew, false, false);

    // Update preferences.
    useredit_update_user_preference($usernew);

    // Update interests.
    if (isset($usernew->interests)) {
        useredit_update_interests($usernew, $usernew->interests);
    }

    // Update user picture.
    if (empty($CFG->disableuserimages)) {
        core_user::update_picture($usernew, $filemanageroptions);
    }

    // Update mail bounces.
    useredit_update_bounces($user, $usernew);

    // Update forum track preference.
    useredit_update_trackforums($user, $usernew);

    // Save custom profile fields data.
    profile_save_data($usernew);

    // Trigger event.
    \core\event\user_updated::create_from_userid($user->id)->trigger();

    // If email was changed and confirmation is required, send confirmation email now to the new address.
    if ($emailchanged !== false && $CFG->emailchangeconfirmation) {
        $tempuser = $DB->get_record('user', array('id' => $user->id), '*', MUST_EXIST);
        $tempuser->email = $emailchanged;

        $supportuser = core_user::get_support_user();

        $a = new stdClass();
        $a->url = $CFG->wwwroot . '/user/emailupdate.php?key=' . $emailchangedkey . '&id=' . $user->id;
        $a->site = format_string($SITE->fullname, true, array('context' => context_course::instance(SITEID)));
        $a->fullname = fullname($tempuser, true);
        $a->supportemail = $supportuser->email;

        $emailupdatemessage = get_string('emailupdatemessage', 'auth', $a);
        $emailupdatetitle = get_string('emailupdatetitle', 'auth', $a);

        // Email confirmation directly rather than using messaging so they will definitely get an email.
        $noreplyuser = core_user::get_noreply_user();
        if (!$mailresults = email_to_user($tempuser, $noreplyuser, $emailupdatetitle, $emailupdatemessage)) {
            die("could not send email!");
        }
    }

    // Reload from db, we need new full name on this page if we do not redirect.
    $user = $DB->get_record('user', array('id' => $user->id), '*', MUST_EXIST);

    if ($USER->id == $user->id) {
        // Override old $USER session variable if needed.
        foreach ((array)$user as $variable => $value) {
            if ($variable === 'description' or $variable === 'password') {
                // These are not set for security nad perf reasons.
                continue;
            }
            $USER->$variable = $value;
        }
        // Preload custom fields.
        profile_load_custom_fields($USER);
    }

    if (is_siteadmin() and empty($SITE->shortname)) {
        // Fresh cli install - we need to finish site settings.
        redirect(new moodle_url('/admin/index.php'));
    }

    if (!$emailchanged || !$CFG->emailchangeconfirmation) {
        redirect($returnurl);
    }
}


// Display page header.
$streditmyprofile = get_string('editmyprofile');
$strparticipants  = get_string('participants');
$userfullname     = fullname($user, true);

$PAGE->set_title("$course->shortname: $streditmyprofile");
$PAGE->set_heading($userfullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($userfullname);

if ($emailchanged) {
    echo $emailchangedhtml;
} else {
    // Finally display THE form.
    $userform->display();
}

// And proper footer.
echo $OUTPUT->footer();

