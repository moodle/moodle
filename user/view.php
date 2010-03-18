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
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package user
 */

require_once("../config.php");
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/tag/lib.php');

$id      = optional_param('id',     0,      PARAM_INT);   // user id
$course  = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)
$enable  = optional_param('enable', 0, PARAM_BOOL);       // enable email
$disable = optional_param('disable', 0, PARAM_BOOL);      // disable email

if (empty($id)) {         // See your own profile by default
    require_login();
    $id = $USER->id;
}

$url = new moodle_url('/user/view.php', array('id'=>$id));
if ($course != SITEID) {
    $url->param('course', $course);
}
if ($enable !== 0) {
    $url->param('enable', $enable);
}
if ($disable !== 0) {
    $url->param('disable', $disable);
}
$PAGE->set_url($url);

if (! $user = $DB->get_record("user", array("id"=>$id))) {
    print_error('invaliduserid');
}

if (! $course = $DB->get_record("course", array("id"=>$course))) {
    print_error('invalidcourseid');
}

// special hack for cli installer - continue to site settings
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
if ($SITE->shortname === '' and has_capability('moodle/site:config', $systemcontext)) {
    redirect($CFG->wwwroot .'/'. $CFG->admin .'/index.php');
}

/// Make sure the current user is allowed to see this user

if (empty($USER->id)) {
   $currentuser = false;
} else {
   $currentuser = ($user->id == $USER->id);
}

if ($course->id == SITEID) {
    $coursecontext = $systemcontext;   // SYSTEM context
} else {
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context
}
$usercontext   = get_context_instance(CONTEXT_USER, $user->id);       // User context

$PAGE->set_context($usercontext);

if (!empty($CFG->forcelogin) || $course->id != SITEID) {
    // do not force parents to enrol
    if (!$DB->get_record('role_assignments', array('userid'=>$USER->id, 'contextid'=>$usercontext->id))) {
        require_login($course->id);
    }
}

if (!empty($CFG->forceloginforprofiles)) {
    require_login();
    if (has_capability('moodle/legacy:guest', $systemcontext, 0, false)) {
        redirect(get_login_url());
    }
}

$strpersonalprofile = get_string('personalprofile');
$strparticipants = get_string("participants");
$struser = get_string("user");

$fullname = fullname($user, has_capability('moodle/site:viewfullnames', $coursecontext));

$link = null;
if (has_capability('moodle/course:viewparticipants', $coursecontext) || has_capability('moodle/site:viewparticipants', $systemcontext)) {
    $link = new moodle_url("/user/index.php", array('id'=>$course->id));
}

/// If the user being shown is not ourselves, then make sure we are allowed to see them!
if (!$currentuser) {

    $PAGE->set_title("$strpersonalprofile: ");
    $PAGE->set_heading("$strpersonalprofile: ");

    if ($course->id == SITEID) {  // Reduce possibility of "browsing" userbase at site level
        if ($CFG->forceloginforprofiles and !isteacherinanycourse()
                and !isteacherinanycourse($user->id)
                and !has_capability('moodle/user:viewdetails', $usercontext)) {  // Teachers can browse and be browsed at site level. If not forceloginforprofiles, allow access (bug #4366)

            $PAGE->navbar->add($struser);
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('usernotavailable', 'error'));
            echo $OUTPUT->footer();
            exit;
        }
    } else {   // Normal course
        // check capabilities
        if (!has_capability('moodle/user:viewdetails', $coursecontext) &&
            !has_capability('moodle/user:viewdetails', $usercontext)) {
            print_error('cannotviewprofile');
        }

        if (!has_capability('moodle/course:view', $coursecontext, $user->id, false)) {
            if (has_capability('moodle/role:assign', $coursecontext)) {
                $PAGE->navbar->add($fullname);
                echo $OUTPUT->heading(get_string('notenrolled', $fullname));
            } else {
                $PAGE->navbar->add($struser);
                echo $OUTPUT->heading(get_string('notenrolledprofile'));
            }
            echo $OUTPUT->continue_button($_SERVER['HTTP_REFERER']);
            echo $OUTPUT->footer();
            exit;
        }
    }


    // If groups are in use, make sure we can see that group
    if (groups_get_course_groupmode($course) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $coursecontext)) {
        require_login();
        ///this is changed because of mygroupid
        $gtrue = (bool)groups_get_all_groups($course->id, $user->id);
        if (!$gtrue) {
            print_error("groupnotamember", '', "../course/view.php?id=$course->id");
        }
    }
}


/// We've established they can see the user's name at least, so what about the rest?

$PAGE->navigation->extend_for_user($user);
$PAGE->set_title("$course->fullname: $strpersonalprofile: $fullname");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();

if (($course->id != SITEID) and ! has_capability('moodle/legacy:guest', $systemcontext, 0, false) ) {   // Need to have access to a course to see that info
    if (!has_capability('moodle/course:view', $coursecontext, $user->id)) {
        echo $OUTPUT->heading(get_string('notenrolled', '', $fullname));
        echo $OUTPUT->footer();
        die;
    }
}

if ($user->deleted) {
    echo $OUTPUT->heading(get_string('userdeleted'));
    if (!has_capability('moodle/user:update', $coursecontext)) {
        echo $OUTPUT->footer();
        die;
    }
}

/// OK, security out the way, now we are showing the user

add_to_log($course->id, "user", "view", "view.php?id=$user->id&course=$course->id", "$user->id");

if ($course->id != SITEID) {
    $user->lastaccess = false;
    if ($lastaccess = $DB->get_record('user_lastaccess', array('userid'=>$user->id, 'courseid'=>$course->id))) {
        $user->lastaccess = $lastaccess->timeaccess;
    }
}


/// Get the hidden field list
if (has_capability('moodle/user:viewhiddendetails', $coursecontext)) {
    $hiddenfields = array();
} else {
    $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
}

/// Print tabs at top
/// This same call is made in:
///     /user/view.php
///     /user/edit.php
///     /course/user.php

$currenttab = 'profile';
$showroles = 1;
if (!$user->deleted) {
    include('tabs.php');
}

if (is_mnet_remote_user($user)) {
    $sql = "
         SELECT DISTINCT h.id, h.name, h.wwwroot,
                         a.name as application, a.display_name
           FROM {mnet_host} h, {mnet_application} a
          WHERE h.id = ? AND h.applicationid = a.id
       ORDER BY a.display_name, h.name";

    $remotehost = $DB->get_record_sql($sql, array($user->mnethostid));

    echo '<p class="errorboxcontent">'.get_string('remoteappuser', $remotehost->application)." <br />\n";
    if ($USER->id == $user->id) {
        if ($remotehost->application =='moodle') {
            echo "Remote {$remotehost->display_name}: <a href=\"{$remotehost->wwwroot}/user/edit.php\">{$remotehost->name}</a> ".get_string('editremoteprofile')." </p>\n";
        } else {
            echo "Remote {$remotehost->display_name}: <a href=\"{$remotehost->wwwroot}/\">{$remotehost->name}</a> ".get_string('gotoyourserver')." </p>\n";
        }
    } else {
        echo "Remote {$remotehost->display_name}: <a href=\"{$remotehost->wwwroot}/\">{$remotehost->name}</a></p>\n";
    }
}

echo '<table width="80%" class="userinfobox" summary="">';
echo '<tr>';
echo '<td class="side">';
echo $OUTPUT->user_picture($user, array('courseid'=>$course->id, 'size'=>100));
echo '</td><td class="content">';

// Print the description

if ($user->description && !isset($hiddenfields['description'])) {
    $has_courseid = ($course->id != SITEID);
    if (!$has_courseid && !empty($CFG->profilesforenrolledusersonly) && !$DB->record_exists('role_assignments', array('userid'=>$id))) {
        echo get_string('profilenotshown', 'moodle').'<hr />';
    } else {

        $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $usercontext->id, 'user_profile', $id);
        echo format_text($user->description, $user->descriptionformat)."<hr />";
    }
}

// Print all the little details in a list

echo '<table class="list">';

if (! isset($hiddenfields['country']) && $user->country) {
    $countries = get_list_of_countries();
    print_row(get_string('country') . ':', $countries[$user->country]);
}

if (! isset($hiddenfields['city']) && $user->city) {
    print_row(get_string('city') . ':', $user->city);
}

if (has_capability('moodle/user:viewhiddendetails', $coursecontext)) {
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

if ($user->maildisplay == 1 or
   ($user->maildisplay == 2 and ($course->id != SITEID) and !isguestuser()) or
   has_capability('moodle/course:useremail', $coursecontext)) {

    $emailswitch = '';

    if (has_capability('moodle/course:useremail', $coursecontext) or $currentuser) {   /// Can use the enable/disable email stuff
        if (!empty($enable) and confirm_sesskey()) {     /// Recieved a parameter to enable the email address
            $DB->set_field('user', 'emailstop', 0, array('id'=>$user->id));
            $user->emailstop = 0;
        }
        if (!empty($disable) and confirm_sesskey()) {     /// Recieved a parameter to disable the email address
            $DB->set_field('user', 'emailstop', 1, array('id'=>$user->id));
            $user->emailstop = 1;
        }
    }

    if (has_capability('moodle/course:useremail', $coursecontext)) {   /// Can use the enable/disable email stuff
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
                       "href=\"view.php?id=$user->id&amp;course=$course->id&amp;$switchparam=1&amp;sesskey=".sesskey()."\">".
                       "<img src=\"" . $OUTPUT->pix_url("$switchpix") . "\" alt=\"$switchclick\" /></a>";

    } else if ($currentuser) {         /// Can only re-enable an email this way
        if ($user->emailstop) {   // Include link that tells how to re-enable their email
            $switchparam = 'enable';
            $switchtitle = get_string('emaildisable');
            $switchclick = get_string('emailenableclick');

            $emailswitch = "&nbsp;(<a title=\"$switchclick\" ".
                           "href=\"view.php?id=$user->id&amp;course=$course->id&amp;enable=1&amp;sesskey=".sesskey()."\">$switchtitle</a>)";
        }
    }

    print_row(get_string("email").":", obfuscate_mailto($user->email, '', $user->emailstop)."$emailswitch");
}

if ($user->url && !isset($hiddenfields['webpage'])) {
    $url = $user->url;
    if (strpos($user->url, '://') === false) {
        $url = 'http://'. $url;
    }
    print_row(get_string("webpage") .":", "<a href=\"$url\">$user->url</a>");
}

if ($user->icq && !isset($hiddenfields['icqnumber'])) {
    print_row(get_string('icqnumber').':',"<a href=\"http://web.icq.com/wwp?uin=$user->icq\">$user->icq <img src=\"http://web.icq.com/whitepages/online?icq=$user->icq&amp;img=5\" alt=\"\" /></a>");
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
    print_row(get_string('aimid').':', '<a href="aim:goim?screenname='.s($user->aim).'">'.s($user->aim).'</a>');
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
                if ($mycourse->id != $course->id){
                    $class = '';
                    if ($mycourse->visible == 0) {
                        // get_my_courses will filter courses $USER cannot see
                        // if we get one with visible 0 it just means it's hidden
                        // ... but not from $USER
                        $class = 'class="dimmed"';
                    }
                    $courselisting .= "<a href=\"{$CFG->wwwroot}/user/view.php?id={$user->id}&amp;course={$mycourse->id}\" $class >"
                        . format_string($mycourse->fullname) . "</a>, ";
                }
                else {
                    $courselisting .= format_string($mycourse->fullname) . ", ";
                }
            }
            $shown++;
            if($shown==20) {
                $courselisting.= "...";
                break;
            }
        }
        print_row(get_string('courses').':', rtrim($courselisting,', '));
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
/// printing roles

if ($rolestring = get_user_roles_in_context($id, $coursecontext)) {
    print_row(get_string('roles').':', format_string($rolestring, false));
}

/// Printing groups
if (!isset($hiddenfields['groups'])) {
    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $coursecontext));
    if (!$isseparategroups){
        if ($usergroups = groups_get_all_groups($course->id, $user->id)){
            $groupstr = '';
            foreach ($usergroups as $group){
                $groupstr .= ' <a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$group->id.'">'.format_string($group->name).'</a>,';
            }
            print_row(get_string("group").":", rtrim($groupstr, ', '));
        }
    }
}
/// End of printing groups

/// Printing Interests
    if( !empty($CFG->usetags)) {
        if ( $interests = tag_get_tags_csv('user', $user->id) ) {
        print_row(get_string('interests') .": ", $interests);
    }
}
/// End of Printing Interests

echo "</table>";

echo "</td></tr></table>";

$userauth = get_auth_plugin($user->auth);

$passwordchangeurl = false;
if ($currentuser and $userauth->can_change_password() and !isguestuser() and has_capability('moodle/user:changeownpassword', $systemcontext)) {
    if (!$passwordchangeurl = $userauth->change_password_url()) {
        if (empty($CFG->loginhttps)) {
            $passwordchangeurl = "$CFG->wwwroot/login/change_password.php";
        } else {
            $passwordchangeurl = str_replace('http:', 'https:', $CFG->wwwroot.'/login/change_password.php');
        }
    }
}

//  Print other functions
echo '<div class="buttons">';

if ($passwordchangeurl) {
    $params = array('id'=>$course->id);

    if (session_is_loggedinas()) {
        $passwordchangeurl = ''; // do not use actual change password url - might contain sensitive data
    } else {
        $parts = explode('?', $passwordchangeurl);
        $passwordchangeurl = reset($parts);
        $after = next($parts);
        preg_match_all('/([^&=]+)=([^&=]+)/', $after, $matches);
        if (count($matches)) {
            foreach($matches[0] as $key=>$match) {
                $params[$matches[1][$key]] = $matches[2][$key];
            }
        }
    }
    echo "<form action=\"$passwordchangeurl\" method=\"get\">";
    echo "<div>";
    foreach($params as $key=>$value) {
        echo '<input type="hidden" name="'.$key.'" value="'.s($value).'" />';
    }
    if (session_is_loggedinas()) {
        // changing of password when "Logged in as" is not allowed
        echo "<input type=\"submit\" value=\"".get_string("changepassword")."\" disabled=\"disabled\" />";
    } else {
        echo "<input type=\"submit\" value=\"".get_string("changepassword")."\" />";
    }
    echo "</div>";
    echo "</form>";
}

if ($course->id != SITEID && empty($course->metacourse)) {   // Mostly only useful at course level

    $canunenrol = false;

    if ($user->id == $USER->id) { // Myself
        $canunenrol = has_capability('moodle/course:view', $coursecontext, NULL) &&              // Course participant
                      has_capability('moodle/role:unassignself', $coursecontext, NULL, false) && // Can unassign myself
                      get_user_roles($coursecontext, $user->id, false);                          // Must have role in course

    } else if (has_capability('moodle/role:assign', $coursecontext, NULL)) { // I can assign roles
        if ($roles = get_user_roles($coursecontext, $user->id, false)) {
            $canunenrol = true;
            foreach($roles as $role) {
                if (!user_can_assign($coursecontext, $role->roleid)) {
                    $canunenrol = false; // I can not unassign all roles in this course :-(
                    break;
                }
            }
        }
    }

    if ($canunenrol) {
        echo '<form action="'.$CFG->wwwroot.'/course/unenrol.php" method="get">';
        echo '<div>';
        echo '<input type="hidden" name="id" value="'.$course->id.'" />';
        echo '<input type="hidden" name="user" value="'.$user->id.'" />';
        echo '<input type="submit" value="'.s(get_string('unenrolme', '', $course->shortname)).'" />';
        echo '</div>';
        echo '</form>';
    }
}

if (!$user->deleted and $USER->id != $user->id  && !session_is_loggedinas() && has_capability('moodle/user:loginas', $coursecontext) &&
                             ! has_capability('moodle/site:doanything', $coursecontext, $user->id, false)) {
    echo '<form action="'.$CFG->wwwroot.'/course/loginas.php" method="get">';
    echo '<div>';
    echo '<input type="hidden" name="id" value="'.$course->id.'" />';
    echo '<input type="hidden" name="user" value="'.$user->id.'" />';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<input type="submit" value="'.get_string('loginas').'" />';
    echo '</div>';
    echo '</form>';
}

if (!$user->deleted and !empty($CFG->messaging) and !isguestuser() and has_capability('moodle/site:sendmessage', $systemcontext)) {
    if (!empty($USER->id) and ($USER->id == $user->id)) {
        if ($countmessages = $DB->count_records('message', array('useridto'=>$user->id))) {
            $messagebuttonname = get_string("messages", "message")."($countmessages)";
        } else {
            $messagebuttonname = get_string("messages", "message");
        }
        echo "<form onclick=\"this.target='message'\" action=\"../message/index.php\" method=\"get\">";
        echo "<div>";
        echo "<input type=\"submit\" value=\"$messagebuttonname\" onclick=\"return openpopup('/message/index.php', 'message', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0);\" />";
        echo "</div>";
        echo "</form>";
    } else {
        echo "<form onclick=\"this.target='message$user->id'\" action=\"../message/discussion.php\" method=\"get\">";
        echo "<div>";
        echo "<input type=\"hidden\" name=\"id\" value=\"$user->id\" />";
        echo "<input type=\"submit\" value=\"".get_string("sendmessage", "message")."\" onclick=\"return openpopup('/message/discussion.php?id=$user->id', 'message_$user->id', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0);\" />";
        echo "</div>";
        echo "</form>";
    }
}
// Authorize.net: User Payments
if ($course->enrol == 'authorize' || (empty($course->enrol) && $CFG->enrol == 'authorize')) {
    echo "<form action=\"../enrol/authorize/index.php\" method=\"get\">";
    echo "<div>";
    echo "<input type=\"hidden\" name=\"course\" value=\"$course->id\" />";
    echo "<input type=\"hidden\" name=\"user\" value=\"$user->id\" />";
    echo "<input type=\"submit\" value=\"".get_string('payments')."\" />";
    echo "</div>";
    echo "</form>";
}
echo "</div>\n";

if ($CFG->debugdisplay && debugging('', DEBUG_DEVELOPER) && $USER->id == $user->id) {  // Show user object
    echo '<hr />';
    echo $OUTPUT->heading('DEBUG MODE:  User session variables');
    print_object($USER);
}

echo $OUTPUT->footer();

/// Functions ///////

function print_row($left, $right) {
    echo "\n<tr><td class=\"label c0\">$left</td><td class=\"info c1\">$right</td></tr>\n";
}


