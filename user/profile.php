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
 * Public Profile -- a user's public profile page
 *
 * - each user can currently have their own page (cloned from system and then customised)
 * - users can add any blocks they want
 * - the administrators can define a default site public profile for users who have
 *   not created their own public profile
 *
 * This script implements the user's view of the public profile, and allows editing
 * of the public profile.
 *
 * @package    moodlecore
 * @subpackage my
 * @copyright  2010 Remote-Learner.net
 * @author     Hubert Chathi <hubert@remote-learner.net>
 * @author     Olav Jordan <olav.jordan@remote-learner.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/tag/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->libdir.'/filelib.php');

$userid = optional_param('id', 0, PARAM_INT);
$edit   = optional_param('edit', null, PARAM_BOOL);    // Turn editing on and off

$PAGE->set_url('/user/profile.php', array('id'=>$userid));

if (!empty($CFG->forceloginforprofiles)) {
    require_login();
    if (isguestuser()) {
        $SESSION->wantsurl = $PAGE->url->out(false);
        redirect(get_login_url());
    }
} else if (!empty($CFG->forcelogin)) {
    require_login();
}

$userid = $userid ? $userid : $USER->id;       // Owner of the page
$user = $DB->get_record('user', array('id' => $userid));
$currentuser = ($user->id == $USER->id);
$context = $usercontext = get_context_instance(CONTEXT_USER, $userid, MUST_EXIST);

if (!$currentuser &&
    !empty($CFG->forceloginforprofiles) &&
    !has_capability('moodle/user:viewdetails', $context) &&
    !has_coursecontact_role($userid)) {

    // Course managers can be browsed at site level. If not forceloginforprofiles, allow access (bug #4366)
    $struser = get_string('user');
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
    $PAGE->set_title("$SITE->shortname: $struser");  // Do not leak the name
    $PAGE->set_heading("$SITE->shortname: $struser");
    $PAGE->set_url('/user/profile.php', array('id'=>$userid));
    $PAGE->navbar->add($struser);
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('usernotavailable', 'error'));
    echo $OUTPUT->footer();
    exit;
}

// Get the profile page.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PUBLIC)) {
    print_error('mymoodlesetup');
}

if (!$currentpage->userid) {
    $context = get_context_instance(CONTEXT_SYSTEM);  // A trick so that we even see non-sticky blocks
}

$PAGE->set_context($context);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('user-profile');

// Set up block editing capabilities
if (isguestuser()) {     // Guests can never edit their profile
    $USER->editing = $edit = 0;  // Just in case
    $PAGE->set_blocks_editing_capability('moodle/my:configsyspages');  // unlikely :)
} else {
    if ($currentuser) {
        $PAGE->set_blocks_editing_capability('moodle/user:manageownblocks');
    } else {
        $PAGE->set_blocks_editing_capability('moodle/user:manageblocks');
    }
}

if (has_capability('moodle/user:viewhiddendetails', $context)) {
    $hiddenfields = array();
} else {
    $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
}

// Start setting up the page
$strpublicprofile = get_string('publicprofile');

$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title(fullname($user).": $strpublicprofile");
$PAGE->set_heading(fullname($user).": $strpublicprofile");

if (!$currentuser) {
    $PAGE->navigation->extend_for_user($user);
    if ($node = $PAGE->settingsnav->get('userviewingsettings'.$user->id)) {
        $node->forceopen = true;
    }
} else if ($node = $PAGE->settingsnav->get('usercurrentsettings', navigation_node::TYPE_CONTAINER)) {
    $node->forceopen = true;
}
if ($node = $PAGE->settingsnav->get('root')) {
    $node->forceopen = false;
}


// Toggle the editing state and switches
if ($PAGE->user_allowed_editing()) {
    if ($edit !== null) {             // Editing state was specified
        $USER->editing = $edit;       // Change editing state
        if (!$currentpage->userid && $edit) {
            // If we are viewing a system page as ordinary user, and the user turns
            // editing on, copy the system pages as new user pages, and get the
            // new page record
            if (!$currentpage = my_copy_page($USER->id, MY_PAGE_PUBLIC, 'user-profile')) {
                print_error('mymoodlesetup');
            }
            $PAGE->set_context($usercontext);
            $PAGE->set_subpage($currentpage->id);
        }
    } else {                          // Editing state is in session
        if ($currentpage->userid) {   // It's a page we can edit, so load from session
            if (!empty($USER->editing)) {
                $edit = 1;
            } else {
                $edit = 0;
            }
        } else {                      // It's a system page and they are not allowed to edit system pages
            $USER->editing = $edit = 0;          // Disable editing completely, just to be safe
        }
    }

    // Add button for editing page
    $params = array('edit' => !$edit);

    if (!$currentpage->userid) {
        // viewing a system page -- let the user customise it
        $editstring = get_string('updatemymoodleon');
        $params['edit'] = 1;
    } else if (empty($edit)) {
        $editstring = get_string('updatemymoodleon');
    } else {
        $editstring = get_string('updatemymoodleoff');
    }

    $url = new moodle_url("$CFG->wwwroot/user/profile.php", $params);
    $button = $OUTPUT->single_button($url, $editstring);
    $PAGE->set_button($button);

} else {
    $USER->editing = $edit = 0;
}

// HACK WARNING!  This loads up all this page's blocks in the system context
if ($currentpage->userid == 0) {
    $CFG->blockmanagerclass = 'my_syspage_block_manager';
}

// TODO WORK OUT WHERE THE NAV BAR IS!

echo $OUTPUT->header();
echo '<div class="userprofile">';


// Print the standard content of this page, the basic profile info

echo $OUTPUT->heading(fullname($user));

if (is_mnet_remote_user($user)) {
    $sql = "SELECT h.id, h.name, h.wwwroot,
                   a.name as application, a.display_name
              FROM {mnet_host} h, {mnet_application} a
             WHERE h.id = ? AND h.applicationid = a.id";

    $remotehost = $DB->get_record_sql($sql, array($user->mnethostid));
    $a = new stdclass();
    $a->remotetype = $remotehost->display_name;
    $a->remotename = $remotehost->name;
    $a->remoteurl  = $remotehost->wwwroot;

    echo $OUTPUT->box(get_string('remoteuserinfo', 'mnet', $a), 'remoteuserinfo');
}

echo '<div class="userprofilebox clearfix"><div class="profilepicture">';
echo $OUTPUT->user_picture($user, array('size'=>100));
echo '</div>';

echo '<div class="descriptionbox"><div class="description">';
// Print the description

if ($user->description && !isset($hiddenfields['description'])) {
    if (!empty($CFG->profilesforenrolledusersonly) && !$currentuser && !$DB->record_exists('role_assignments', array('userid'=>$user->id))) {
        echo get_string('profilenotshown', 'moodle');
    } else {
        $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $usercontext->id, 'user', 'profile', null);
        $options = array('overflowdiv'=>true);
        echo format_text($user->description, $user->descriptionformat, $options);
    }
}
echo '</div>';


// Print all the little details in a list

echo '<table class="list" summary="">';

if (! isset($hiddenfields['country']) && $user->country) {
    print_row(get_string('country') . ':', get_string($user->country, 'countries'));
}

if (! isset($hiddenfields['city']) && $user->city) {
    print_row(get_string('city') . ':', $user->city);
}

if (has_capability('moodle/user:viewhiddendetails', $context)) {
    if ($user->address) {
        print_row(get_string("address").":", "$user->address");
    }
    if ($user->phone1) {
        print_row(get_string("phone").":", "$user->phone1");
    }
    if ($user->phone2) {
        print_row(get_string("phone2").":", "$user->phone2");
    }
}

if ($currentuser
  or $user->maildisplay == 1
  or has_capability('moodle/course:useremail', $context)
  or ($user->maildisplay == 2 and enrol_sharing_course($user, $USER))) {

    print_row(get_string("email").":", obfuscate_mailto($user->email, ''));
}

if ($user->url && !isset($hiddenfields['webpage'])) {
    $url = $user->url;
    if (strpos($user->url, '://') === false) {
        $url = 'http://'. $url;
    }
    print_row(get_string("webpage") .":", '<a href="'.s($url).'">'.s($user->url).'</a>');
}

if ($user->icq && !isset($hiddenfields['icqnumber'])) {
    print_row(get_string('icqnumber').':',"<a href=\"http://web.icq.com/wwp?uin=".urlencode($user->icq)."\">".s($user->icq)." <img src=\"http://web.icq.com/whitepages/online?icq=".urlencode($user->icq)."&amp;img=5\" alt=\"\" /></a>");
}

if ($user->skype && !isset($hiddenfields['skypeid'])) {
    print_row(get_string('skypeid').':','<a href="callto:'.urlencode($user->skype).'">'.s($user->skype).
        ' <img src="http://mystatus.skype.com/smallicon/'.urlencode($user->skype).'" alt="'.get_string('status').'" '.
        ' /></a>');
}
if ($user->yahoo && !isset($hiddenfields['yahooid'])) {
    print_row(get_string('yahooid').':', '<a href="http://edit.yahoo.com/config/send_webmesg?.target='.urlencode($user->yahoo).'&amp;.src=pg">'.s($user->yahoo)." <img src=\"http://opi.yahoo.com/online?u=".urlencode($user->yahoo)."&m=g&t=0\" alt=\"\"></a>");
}
if ($user->aim && !isset($hiddenfields['aimid'])) {
    print_row(get_string('aimid').':', '<a href="aim:goim?screenname='.urlencode($user->aim).'">'.s($user->aim).'</a>');
}
if ($user->msn && !isset($hiddenfields['msnid'])) {
    print_row(get_string('msnid').':', s($user->msn));
}

/// Print the Custom User Fields
profile_display_fields($user->id);


if (!isset($hiddenfields['mycourses'])) {
    if ($mycourses = enrol_get_all_users_courses($user->id, true, NULL, 'visible DESC,sortorder ASC')) {
        $shown=0;
        $courselisting = '';
        foreach ($mycourses as $mycourse) {
            if ($mycourse->category) {
                $class = '';
                if ($mycourse->visible == 0) {
                    $ccontext = get_context_instance(CONTEXT_COURSE, $mycourse->id);
                    if (!has_capability('moodle/course:viewhiddencourses', $ccontext)) {
                        continue;
                    }
                    $class = 'class="dimmed"';
                }
                $courselisting .= "<a href=\"{$CFG->wwwroot}/user/view.php?id={$user->id}&amp;course={$mycourse->id}\" $class >" . format_string($mycourse->fullname) . "</a>, ";
            }
            $shown++;
            if($shown==20) {
                $courselisting.= "...";
                break;
            }
        }
        print_row(get_string('courseprofiles').':', rtrim($courselisting,', '));
    }
}
if (!isset($hiddenfields['firstaccess'])) {
    if ($user->firstaccess) {
        $datestring = userdate($user->firstaccess)."&nbsp; (".format_time(time() - $user->firstaccess).")";
    } else {
        $datestring = get_string("never");
    }
    print_row(get_string("firstaccess").":", $datestring);
}
if (!isset($hiddenfields['lastaccess'])) {
    if ($user->lastaccess) {
        $datestring = userdate($user->lastaccess)."&nbsp; (".format_time(time() - $user->lastaccess).")";
    } else {
        $datestring = get_string("never");
    }
    print_row(get_string("lastaccess").":", $datestring);
}

/// Printing tagged interests
if (!empty($CFG->usetags)) {
    if ($interests = tag_get_tags_csv('user', $user->id) ) {
        print_row(get_string('interests') .": ", $interests);
    }
}

echo "</table></div></div>";


echo $OUTPUT->blocks_for_region('content');

// Print messaging link if allowed
if (isloggedin() && has_capability('moodle/site:sendmessage', $context)
    && !empty($CFG->messaging) && !isguestuser() && !isguestuser($user) && ($USER->id != $user->id)) {
    echo '<div class="messagebox">';
    echo '<a href="'.$CFG->wwwroot.'/message/index.php?id='.$user->id.'">'.get_string('messageselectadd').'</a>';
    echo '</div>';
}

if ($CFG->debugdisplay && debugging('', DEBUG_DEVELOPER) && $currentuser) {  // Show user object
    echo '<br /><br /><hr />';
    echo $OUTPUT->heading('DEBUG MODE:  User session variables');
    print_object($USER);
}

echo '</div>';  // userprofile class
echo $OUTPUT->footer();


function print_row($left, $right) {
    echo "\n<tr><th class=\"label c0\">$left</th><td class=\"info c1\">$right</td></tr>\n";
}
