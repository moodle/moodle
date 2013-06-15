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
if ((!$user = $DB->get_record('user', array('id' => $userid))) || ($user->deleted)) {
    $PAGE->set_context(context_system::instance());
    echo $OUTPUT->header();
    if (!$user) {
        echo $OUTPUT->notification(get_string('invaliduser', 'error'));
    } else {
        echo $OUTPUT->notification(get_string('userdeleted'));
    }
    echo $OUTPUT->footer();
    die;
}

$currentuser = ($user->id == $USER->id);
$context = $usercontext = context_user::instance($userid, MUST_EXIST);

if (!$currentuser &&
    !empty($CFG->forceloginforprofiles) &&
    !has_capability('moodle/user:viewdetails', $context) &&
    !has_coursecontact_role($userid)) {

    // Course managers can be browsed at site level. If not forceloginforprofiles, allow access (bug #4366)
    $struser = get_string('user');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title("$SITE->shortname: $struser");  // Do not leak the name
    $PAGE->set_heading("$SITE->shortname: $struser");
    $PAGE->set_url('/user/profile.php', array('id'=>$userid));
    $PAGE->navbar->add($struser);
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('usernotavailable', 'error'));
    echo $OUTPUT->footer();
    exit;
}

// Get the profile page.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PUBLIC)) {
    print_error('mymoodlesetup');
}

if (!$currentpage->userid) {
    $context = context_system::instance();  // A trick so that we even see non-sticky blocks
}

$PAGE->set_context($context);
$PAGE->set_pagelayout('mypublic');
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

if (has_capability('moodle/site:viewuseridentity', $context)) {
    $identityfields = array_flip(explode(',', $CFG->showuseridentity));
} else {
    $identityfields = array();
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

echo html_writer::start_tag('dl', array('class'=>'list'));
if (!isset($hiddenfields['country']) && $user->country) {
    echo html_writer::tag('dt', get_string('country'));
    echo html_writer::tag('dd', get_string($user->country, 'countries'));
}

if (!isset($hiddenfields['city']) && $user->city) {
    echo html_writer::tag('dt', get_string('city'));
    echo html_writer::tag('dd', $user->city);
}

if (isset($identityfields['address']) && $user->address) {
    echo html_writer::tag('dt', get_string('address'));
    echo html_writer::tag('dd', $user->address);
}

if (isset($identityfields['phone1']) && $user->phone1) {
    echo html_writer::tag('dt', get_string('phone'));
    echo html_writer::tag('dd', $user->phone1);
}

if (isset($identityfields['phone2']) && $user->phone2) {
    echo html_writer::tag('dt', get_string('phone2'));
    echo html_writer::tag('dd', $user->phone2);
}

if (isset($identityfields['institution']) && $user->institution) {
    echo html_writer::tag('dt', get_string('institution'));
    echo html_writer::tag('dd', $user->institution);
}

if (isset($identityfields['department']) && $user->department) {
    echo html_writer::tag('dt', get_string('department'));
    echo html_writer::tag('dd', $user->department);
}

if (isset($identityfields['idnumber']) && $user->idnumber) {
    echo html_writer::tag('dt', get_string('idnumber'));
    echo html_writer::tag('dd', $user->idnumber);
}

if (isset($identityfields['email']) and ($currentuser
  or $user->maildisplay == 1
  or has_capability('moodle/course:useremail', $context)
  or ($user->maildisplay == 2 and enrol_sharing_course($user, $USER)))) {
    echo html_writer::tag('dt', get_string('email'));
    echo html_writer::tag('dd', obfuscate_mailto($user->email, ''));
}

if ($user->url && !isset($hiddenfields['webpage'])) {
    $url = $user->url;
    if (strpos($user->url, '://') === false) {
        $url = 'http://'. $url;
    }
    $webpageurl = new moodle_url($url);
    echo html_writer::tag('dt', get_string('webpage'));
    echo html_writer::tag('dd', html_writer::link($webpageurl, s($user->url)));
}

if ($user->icq && !isset($hiddenfields['icqnumber'])) {
    $imurl = new moodle_url('http://web.icq.com/wwp', array('uin'=>$user->icq) );
    $iconurl = new moodle_url('http://web.icq.com/whitepages/online', array('icq'=>$user->icq, 'img'=>'5'));
    $statusicon = html_writer::tag('img', '', array('src'=>$iconurl, 'class'=>'icon icon-post', 'alt'=>get_string('status')));
    echo html_writer::tag('dt', get_string('icqnumber'));
    echo html_writer::tag('dd', html_writer::link($imurl, s($user->icq) . $statusicon));
}

if ($user->skype && !isset($hiddenfields['skypeid'])) {
    $imurl = 'skype:'.urlencode($user->skype).'?call';
    $iconurl = new moodle_url('http://mystatus.skype.com/smallicon/'.$user->skype);
    if (strpos($CFG->httpswwwroot, 'https:') === 0) {
        // Bad luck, skype devs are lazy to set up SSL on their servers - see MDL-37233.
        $statusicon = '';
    } else {
        $statusicon = html_writer::empty_tag('img', array('src'=>$iconurl, 'class'=>'icon icon-post', 'alt'=>get_string('status')));
    }
    echo html_writer::tag('dt', get_string('skypeid'));
    echo html_writer::tag('dd', html_writer::link($imurl, s($user->skype) . $statusicon));
}
if ($user->yahoo && !isset($hiddenfields['yahooid'])) {
    $imurl = new moodle_url('http://edit.yahoo.com/config/send_webmesg', array('.target'=>$user->yahoo, '.src'=>'pg'));
    $iconurl = new moodle_url('http://opi.yahoo.com/online', array('u'=>$user->yahoo, 'm'=>'g', 't'=>'0'));
    $statusicon = html_writer::tag('img', '', array('src'=>$iconurl, 'class'=>'iconsmall icon-post', 'alt'=>get_string('status')));
    echo html_writer::tag('dt', get_string('yahooid'));
    echo html_writer::tag('dd', html_writer::link($imurl, s($user->yahoo) . $statusicon));
}
if ($user->aim && !isset($hiddenfields['aimid'])) {
    $imurl = 'aim:goim?screenname='.urlencode($user->aim);
    echo html_writer::tag('dt', get_string('aimid'));
    echo html_writer::tag('dd', html_writer::link($imurl, s($user->aim)));
}
if ($user->msn && !isset($hiddenfields['msnid'])) {
    echo html_writer::tag('dt', get_string('msnid'));
    echo html_writer::tag('dd', s($user->msn));
}

/// Print the Custom User Fields
profile_display_fields($user->id);


if (!isset($hiddenfields['mycourses'])) {
    if ($mycourses = enrol_get_all_users_courses($user->id, true, NULL, 'visible DESC,sortorder ASC')) {
        $shown=0;
        $courselisting = '';
        foreach ($mycourses as $mycourse) {
            if ($mycourse->category) {
                context_helper::preload_from_record($mycourse);
                $ccontext = context_course::instance($mycourse->id);
                $class = '';
                if ($mycourse->visible == 0) {
                    if (!has_capability('moodle/course:viewhiddencourses', $ccontext)) {
                        continue;
                    }
                    $class = 'class="dimmed"';
                }
                $courselisting .= "<a href=\"{$CFG->wwwroot}/user/view.php?id={$user->id}&amp;course={$mycourse->id}\" $class >" . $ccontext->get_context_name(false) . "</a>, ";
            }
            $shown++;
            if($shown==20) {
                $courselisting.= "...";
                break;
            }
        }
        echo html_writer::tag('dt', get_string('courseprofiles'));
        echo html_writer::tag('dd', rtrim($courselisting,', '));
    }
}
if (!isset($hiddenfields['firstaccess'])) {
    if ($user->firstaccess) {
        $datestring = userdate($user->firstaccess)."&nbsp; (".format_time(time() - $user->firstaccess).")";
    } else {
        $datestring = get_string("never");
    }
    echo html_writer::tag('dt', get_string('firstaccess'));
    echo html_writer::tag('dd', $datestring);
}
if (!isset($hiddenfields['lastaccess'])) {
    if ($user->lastaccess) {
        $datestring = userdate($user->lastaccess)."&nbsp; (".format_time(time() - $user->lastaccess).")";
    } else {
        $datestring = get_string("never");
    }
    echo html_writer::tag('dt', get_string('lastaccess'));
    echo html_writer::tag('dd', $datestring);
}

/// Printing tagged interests
if (!empty($CFG->usetags)) {
    if ($interests = tag_get_tags_csv('user', $user->id) ) {
        echo html_writer::tag('dt', get_string('interests'));
        echo html_writer::tag('dd', $interests);
    }
}

if (!isset($hiddenfields['suspended'])) {
    if ($user->suspended) {
        echo html_writer::tag('dt', '&nbsp;');
        echo html_writer::tag('dd', get_string('suspended', 'auth'));
    }
}

require_once($CFG->libdir . '/badgeslib.php');
if (!empty($CFG->enablebadges)) {
    profile_display_badges($user->id);
}

echo html_writer::end_tag('dl');
echo "</div></div>"; // Closing desriptionbox and userprofilebox.
echo '<div id="region-content" class="block-region"><div class="region-content">';
echo $OUTPUT->blocks_for_region('content');
echo '</div></div>';

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
