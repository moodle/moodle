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
require_once($CFG->dirroot . '/user/profile/lib.php');

$userid = optional_param('id', 0, PARAM_INT);
$edit   = optional_param('edit', null, PARAM_BOOL);    // Turn editing on and off

if (!empty($CFG->forceloginforprofiles)) {
    require_login();
    if (isguestuser()) {
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
    !has_coursemanager_role($userid)) {
    // Course managers can be browsed at site level. If not forceloginforprofiles, allow access (bug #4366)
    $struser = get_string('user');
    $PAGE->set_title("$SITE->shortname: $struser");
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



// Start setting up the page
$strpublicprofile = get_string('publicprofile');

$params = array('id'=>$userid);
$PAGE->set_url('/user/profile.php', $params);
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title("$SITE->shortname: $strpublicprofile");
$PAGE->set_heading("$SITE->shortname: $strpublicprofile");
$PAGE->navigation->extend_for_user($user);
if ($node = $PAGE->settingsnav->get('userviewingsettings')) {
    $node->forceopen = true;
    if ($node = $PAGE->settingsnav->get('root')) {
        $node->forceopen = false;
    }
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
    $sql = "
         SELECT DISTINCT h.id, h.name, h.wwwroot,
                a.name as application, a.display_name
           FROM {mnet_host} h, {mnet_application} a
          WHERE h.id = ? AND h.applicationid = a.id
       ORDER BY a.display_name, h.name";

    $remotehost = $DB->get_record_sql($sql, array($user->mnethostid));

    echo '<p class="errorboxcontent">'.get_string('remoteappuser', $remotehost->application)." <br />\n";
    if ($currentuser) {
        if ($remotehost->application =='moodle') {
            echo "Remote {$remotehost->display_name}: <a href=\"{$remotehost->wwwroot}/user/edit.php\">{$remotehost->name}</a> ".get_string('editremoteprofile')." </p>\n";
        } else {
            echo "Remote {$remotehost->display_name}: <a href=\"{$remotehost->wwwroot}/\">{$remotehost->name}</a> ".get_string('gotoyourserver')." </p>\n";
        }
    } else {
        echo "Remote {$remotehost->display_name}: <a href=\"{$remotehost->wwwroot}/\">{$remotehost->name}</a></p>\n";
    }
}

echo '<div class="profilepicture">';
echo $OUTPUT->user_picture($user, array('size'=>100));
echo '</div>';

echo '<div class="description">';
// Print the description

if ($user->description && !isset($hiddenfields['description'])) {
    if (!empty($CFG->profilesforenrolledusersonly) && !$currentuser && !$DB->record_exists('role_assignments', array('userid'=>$user->id))) {
        echo get_string('profilenotshown', 'moodle');
    } else {
        $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $usercontext->id, 'user_profile', $user->id);
        echo format_text($user->description, $user->descriptionformat);
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

if ($user->maildisplay == 1
   or ($user->maildisplay == 2 && !isguestuser())
   or has_capability('moodle/course:useremail', $context)) {

    $emailswitch = '';

    if ($currentuser or has_capability('moodle/course:useremail', $context)) {   /// Can use the enable/disable email stuff
        if (!empty($enable) and confirm_sesskey()) {     /// Recieved a parameter to enable the email address
            $DB->set_field('user', 'emailstop', 0, array('id'=>$user->id));
            $user->emailstop = 0;
        }
        if (!empty($disable) and confirm_sesskey()) {     /// Recieved a parameter to disable the email address
            $DB->set_field('user', 'emailstop', 1, array('id'=>$user->id));
            $user->emailstop = 1;
        }
    }

    if (has_capability('moodle/course:useremail', $context)) {   /// Can use the enable/disable email stuff
        if ($user->emailstop) {
            $switchparam = 'enable';
            $switchtitle = get_string('emaildisable');
            $switchclick = get_string('emailenableclick');
            $switchpix   = 't/emailno';
        } else {
            $switchparam = 'disable';
            $switchtitle = get_string('emailenable');
            $switchclick = get_string('emaildisableclick');
            $switchpix   = 't/email';
        }
        $emailswitch = "&nbsp;<a title=\"$switchclick\" ".
                       "href=\"profile.php?id=$user->id&amp;$switchparam=1&amp;sesskey=".sesskey()."\">".
                       "<img src=\"" . $OUTPUT->pix_url("$switchpix") . "\" alt=\"$switchclick\" /></a>";

    } else if ($currentuser) {         /// Can only re-enable an email this way
        if ($user->emailstop) {   // Include link that tells how to re-enable their email
            $switchparam = 'enable';
            $switchtitle = get_string('emaildisable');
            $switchclick = get_string('emailenableclick');

            $emailswitch = "&nbsp;(<a title=\"$switchclick\" ".
                           "href=\"profile.php?id=$user->id&amp;enable=1&amp;sesskey=".sesskey()."\">$switchtitle</a>)";
        }
    }

    print_row(get_string("email").":", obfuscate_mailto($user->email, '', $user->emailstop)."$emailswitch");
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
    if ($mycourses = get_my_courses($user->id, 'visible DESC,sortorder ASC', null, false, 21)) {
        $shown=0;
        $courselisting = '';
        foreach ($mycourses as $mycourse) {
            if ($mycourse->category) {
                $class = '';
                if ($mycourse->visible == 0) {
                    // get_my_courses will filter courses $USER cannot see
                    // if we get one with visible 0 it just means it's hidden
                    // ... but not from $USER
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

echo "</table>";


echo $OUTPUT->blocks_for_region('content');

if ($CFG->debugdisplay && debugging('', DEBUG_DEVELOPER) && $currentuser) {  // Show user object
    echo '<br /><br /><hr />';
    echo $OUTPUT->heading('DEBUG MODE:  User session variables');
    print_object($USER);
}

echo '</div>';  // userprofile class
echo $OUTPUT->footer();


function print_row($left, $right) {
    echo "\n<tr><td class=\"label c0\">$left</td><td class=\"info c1\">$right</td></tr>\n";
}
