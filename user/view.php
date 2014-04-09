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
 * Display profile for a particular user
 *
 * @package core_user
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/tag/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/badgeslib.php');

$id        = optional_param('id', 0, PARAM_INT); // User id.
$courseid  = optional_param('course', SITEID, PARAM_INT); // course id (defaults to Site).

// See your own profile by default.
if (empty($id)) {
    require_login();
    $id = $USER->id;
}

if ($courseid == SITEID) {   // Since Moodle 2.0 all site-level profiles are shown by profile.php.
    redirect($CFG->wwwroot.'/user/profile.php?id='.$id);  // Immediate redirect.
}

$PAGE->set_url('/user/view.php', array('id' => $id, 'course' => $courseid));

$user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$currentuser = ($user->id == $USER->id);

$systemcontext = context_system::instance();
$coursecontext = context_course::instance($course->id);
$usercontext   = context_user::instance($user->id, IGNORE_MISSING);

// Require login first.
if (isguestuser($user)) {
    // Can not view profile of guest - thre is nothing to see there.
    print_error('invaliduserid');
}

if (!empty($CFG->forceloginforprofiles)) {
    require_login(); // We can not log in to course due to the parent hack below.

    // Guests do not have permissions to view anyone's profile if forceloginforprofiles is set.
    if (isguestuser()) {
        $SESSION->wantsurl = $PAGE->url->out(false);
        redirect(get_login_url());
    }
}

$PAGE->set_context($coursecontext);
$PAGE->set_course($course);
$PAGE->set_pagetype('course-view-' . $course->format);  // To get the blocks exactly like the course.
$PAGE->add_body_class('path-user');                     // So we can style it independently.
$PAGE->set_other_editing_capability('moodle/course:manageactivities');

$isparent = false;

if (!$currentuser and !$user->deleted
  and $DB->record_exists('role_assignments', array('userid' => $USER->id, 'contextid' => $usercontext->id))
  and has_capability('moodle/user:viewdetails', $usercontext)) {
    // TODO: very ugly hack - do not force "parents" to enrol into course their child is enrolled in,
    //       this way they may access the profile where they get overview of grades and child activity in course,
    //       please note this is just a guess!
    require_login();
    $isparent = true;
    $PAGE->navigation->set_userid_for_parent_checks($id);
} else {
    // Normal course.
    require_login($course);
    // What to do with users temporary accessing this course? should they see the details?
}

$strpersonalprofile = get_string('personalprofile');
$strparticipants = get_string("participants");
$struser = get_string("user");

$fullname = fullname($user, has_capability('moodle/site:viewfullnames', $coursecontext));

// Now test the actual capabilities and enrolment in course.
if ($currentuser) {
    if (!is_viewing($coursecontext) && !is_enrolled($coursecontext)) {
        // Need to have full access to a course to see the rest of own info.
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('notenrolled', '', $fullname));
        if (!empty($_SERVER['HTTP_REFERER'])) {
            echo $OUTPUT->continue_button($_SERVER['HTTP_REFERER']);
        }
        echo $OUTPUT->footer();
        die;
    }

} else {
    // Somebody else.
    $PAGE->set_title("$strpersonalprofile: ");
    $PAGE->set_heading("$strpersonalprofile: ");

    // Check course level capabilities.
    if (!has_capability('moodle/user:viewdetails', $coursecontext) && // Normal enrolled user or mnager.
        ($user->deleted or !has_capability('moodle/user:viewdetails', $usercontext))) {   // Usually parent.
        print_error('cannotviewprofile');
    }

    if (!is_enrolled($coursecontext, $user->id)) {
        // TODO: the only potential problem is that managers and inspectors might post in forum, but the link
        //       to profile would not work - maybe a new capability - moodle/user:freely_acessile_profile_for_anybody
        //       or test for course:inspect capability.
        if (has_capability('moodle/role:assign', $coursecontext)) {
            $PAGE->navbar->add($fullname);
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('notenrolled', '', $fullname));
        } else {
            echo $OUTPUT->header();
            $PAGE->navbar->add($struser);
            echo $OUTPUT->heading(get_string('notenrolledprofile'));
        }
        if (!empty($_SERVER['HTTP_REFERER'])) {
            echo $OUTPUT->continue_button($_SERVER['HTTP_REFERER']);
        }
        echo $OUTPUT->footer();
        exit;
    }

    // If groups are in use and enforced throughout the course, then make sure we can meet in at least one course level group.
    if (groups_get_course_groupmode($course) == SEPARATEGROUPS and $course->groupmodeforce
      and !has_capability('moodle/site:accessallgroups', $coursecontext) and !has_capability('moodle/site:accessallgroups', $coursecontext, $user->id)) {
        if (!isloggedin() or isguestuser()) {
            // Do not use require_login() here because we might have already used require_login($course).
            redirect(get_login_url());
        }
        $mygroups = array_keys(groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid, 'g.id, g.name'));
        $usergroups = array_keys(groups_get_all_groups($course->id, $user->id, $course->defaultgroupingid, 'g.id, g.name'));
        if (!array_intersect($mygroups, $usergroups)) {
            print_error("groupnotamember", '', "../course/view.php?id=$course->id");
        }
    }
}

$PAGE->set_title("$course->fullname: $strpersonalprofile: $fullname");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');

// Locate the users settings in the settings navigation and force it open.
// This MUST be done after we've set up the page as it is going to cause theme and output to initialise.
if (!$currentuser) {
    $PAGE->navigation->extend_for_user($user);
    if ($node = $PAGE->settingsnav->get('userviewingsettings'.$user->id)) {
        $node->forceopen = true;
    }
} else if ($node = $PAGE->settingsnav->get('usercurrentsettings', navigation_node::TYPE_CONTAINER)) {
    $node->forceopen = true;
}
if ($node = $PAGE->settingsnav->get('courseadmin')) {
    $node->forceopen = false;
}

echo $OUTPUT->header();

echo '<div class="userprofile">';

echo $OUTPUT->heading(fullname($user).' ('.format_string($course->shortname, true, array('context' => $coursecontext)).')');

if ($user->deleted) {
    echo $OUTPUT->heading(get_string('userdeleted'));
    if (!has_capability('moodle/user:update', $coursecontext)) {
        echo $OUTPUT->footer();
        die;
    }
}

// OK, security out the way, now we are showing the user.
// Trigger a user profile viewed event.
$event = \core\event\user_profile_viewed::create(array(
    'objectid' => $USER->id,
    'relateduserid' => $user->id,
    'context' => $usercontext,
    'other' => array(
        'courseid' => $course->id,
        'courseshortname' => $course->shortname,
        'coursefullname' => $course->fullname
    )
));
$event->add_record_snapshot('user', $user);
$event->trigger();

// Get the hidden field list.
if (has_capability('moodle/user:viewhiddendetails', $coursecontext)) {
    $hiddenfields = array();
} else {
    $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
}

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
echo $OUTPUT->user_picture($user, array('size' => 100));
echo '</div>';

// Print the description.
echo '<div class="descriptionbox"><div class="description">';
if ($user->description && !isset($hiddenfields['description'])) {
    if (!empty($CFG->profilesforenrolledusersonly) && !$DB->record_exists('role_assignments', array('userid' => $id))) {
        echo get_string('profilenotshown', 'moodle');
    } else {
        if ($courseid == SITEID) {
            $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $usercontext->id, 'user', 'profile', null);
        } else {
            // We have to make a little detour thought the course context to verify the access control for course profile.
            $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $coursecontext->id, 'user', 'profile', $user->id);
        }
        $options = array('overflowdiv' => true);
        echo format_text($user->description, $user->descriptionformat, $options);
    }
}
echo '</div>';


// Print all the little details in a list.

echo html_writer::start_tag('dl', array('class' => 'list'));
// Show email if any of the following conditions match.
// 1. User is viewing his own profile.
// 2. Has allowed everyone to see email
// 3. User has allowed course members to can see email and current user is in same course
// 4. Has either course:viewhiddenuserfields or site:viewuseridentity capability.
if ($currentuser
   or $user->maildisplay == 1
   or ($user->maildisplay == 2 && is_enrolled($coursecontext, $USER))
   or has_capability('moodle/course:viewhiddenuserfields', $coursecontext)
   or has_capability('moodle/site:viewuseridentity', $coursecontext)) {
    echo html_writer::tag('dt', get_string('email'));
    echo html_writer::tag('dd', obfuscate_mailto($user->email, ''));
}

// Show last time this user accessed this course.
if (!isset($hiddenfields['lastaccess'])) {
    if ($lastaccess = $DB->get_record('user_lastaccess', array('userid' => $user->id, 'courseid' => $course->id))) {
        $datestring = userdate($lastaccess->timeaccess)."&nbsp; (".format_time(time() - $lastaccess->timeaccess).")";
    } else {
        $datestring = get_string("never");
    }
    echo html_writer::tag('dt', get_string('lastaccess'));
    echo html_writer::tag('dd', $datestring);
}

// Show roles in this course.
if ($rolestring = get_user_roles_in_course($id, $course->id)) {
    echo html_writer::tag('dt', get_string('roles'));
    echo html_writer::tag('dd', $rolestring);
}

// Show groups this user is in.
if (!isset($hiddenfields['groups'])) {
    $accessallgroups = has_capability('moodle/site:accessallgroups', $coursecontext);
    if ($usergroups = groups_get_all_groups($course->id, $user->id)) {
        $groupstr = '';
        foreach ($usergroups as $group) {
            if ($course->groupmode == SEPARATEGROUPS and !$accessallgroups and $user->id != $USER->id) {
                if (!groups_is_member($group->id, $user->id)) {
                    continue;
                }
            }

            if ($course->groupmode != NOGROUPS) {
                $groupstr .= ' <a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$group->id.'">'.format_string($group->name).'</a>,';
            } else {
                $groupstr .= ' '.format_string($group->name); // The user/index.php shows groups only when course in group mode.
            }
        }
        if ($groupstr !== '') {
            echo html_writer::tag('dt', get_string('group'));
            echo html_writer::tag('dd', rtrim($groupstr, ', '));
        }
    }
}

// Show other courses they may be in.
if (!isset($hiddenfields['mycourses'])) {
    if ($mycourses = enrol_get_all_users_courses($user->id, true, null, 'visible DESC,sortorder ASC')) {
        $shown = 0;
        $courselisting = '';
        foreach ($mycourses as $mycourse) {
            if ($mycourse->category) {
                context_helper::preload_from_record($mycourse);
                $ccontext = context_course::instance($mycourse->id);
                $cfullname = $ccontext->get_context_name(false);
                if ($mycourse->id != $course->id) {
                    $class = '';
                    if ($mycourse->visible == 0) {
                        if (!has_capability('moodle/course:viewhiddencourses', $ccontext)) {
                            continue;
                        }
                        $class = 'class="dimmed"';
                    }
                    $courselisting .= "<a href=\"{$CFG->wwwroot}/user/view.php?id={$user->id}&amp;course={$mycourse->id}\" $class >"
                        . $cfullname . "</a>, ";
                } else {
                    $courselisting .= $cfullname . ", ";
                    $PAGE->navbar->add($cfullname);
                }
            }
            $shown++;
            if ($shown >= 20) {
                $courselisting .= "...";
                break;
            }
        }
        echo html_writer::tag('dt', get_string('courseprofiles'));
        echo html_writer::tag('dd', rtrim($courselisting, ', '));
    }
}

if (!empty($CFG->enablebadges) && !empty($CFG->badges_allowcoursebadges)) {
    profile_display_badges($user->id, $courseid);
}

if (!isset($hiddenfields['suspended'])) {
    if ($user->suspended) {
        echo html_writer::tag('dt', "&nbsp;");
        echo html_writer::tag('dd', get_string('suspended', 'auth'));
    }
}

if (has_capability('moodle/user:viewlastip', $usercontext) && !isset($hiddenfields['lastip'])) {
    if ($user->lastip) {
        $iplookupurl = new moodle_url('/iplookup/index.php', array('ip' => $user->lastip, 'user' => $USER->id));
        $ipstring = html_writer::link($iplookupurl, $user->lastip);
    } else {
        $ipstring = get_string("none");
    }
    echo html_writer::tag('dt', get_string('lastip'));
    echo html_writer::tag('dd', $ipstring);
}
echo html_writer::end_tag('dl');
echo "</div></div>"; // Closing desriptionbox and userprofilebox.
// Print messaging link if allowed.
if (isloggedin() && has_capability('moodle/site:sendmessage', $usercontext)
    && !empty($CFG->messaging) && !isguestuser() && !isguestuser($user) && ($USER->id != $user->id)) {
    echo '<div class="messagebox">';
    $sendmessageurl = new moodle_url('/message/index.php', array('id' => $user->id));
    if ($courseid) {
        $sendmessageurl->param('viewing', MESSAGE_VIEW_COURSE. $courseid);
    }
    echo html_writer::link($sendmessageurl, get_string('messageselectadd'));
    echo '</div>';
}

if ($currentuser || has_capability('moodle/user:viewdetails', $usercontext) || has_coursecontact_role($id)) {
    echo '<div class="fullprofilelink">';
    echo html_writer::link($CFG->wwwroot.'/user/profile.php?id='.$id, get_string('fullprofile'));
    echo '</div>';
}

// TODO Add more useful overview info for teachers here, see below.
// Show links to notes made about this student (must click to display, for privacy).
// Recent comments made in this course.
// Recent blogs associated with this course and items in it.



echo '</div>';  // Userprofile class.

echo $OUTPUT->footer();
