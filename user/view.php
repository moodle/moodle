<?PHP // $Id$

//  Display profile for a particular user
    
    require_once("../config.php");
    $id      = optional_param('id',     0,      PARAM_INT);   // user id
    $course  = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)
    $enable  = optional_param('enable', '');                  // enable email
    $disable = optional_param('disable', '');                 // disable email

    if (empty($id)) {         // See your own profile by default
        require_login();
        $id = $USER->id;
    }

    if (! $user = get_record("user", "id", $id) ) {
        error("No such user in this course");
    }

    if (! $course = get_record("course", "id", $course) ) {
        error("No such course id");
    }

/// Make sure the current user is allowed to see this user

    if (empty($USER->id)) {
       $currentuser = false;
    } else {
       $currentuser = ($user->id == $USER->id);
    }

    if (!empty($CFG->forcelogin) || $course->id != SITEID) {
        require_login($course->id);
    }

    if ($course->id == SITEID) {
        $coursecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);   // SYSTEM context
    } else {
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context
    }
    $usercontext   = get_context_instance(CONTEXT_USER, $user->id);       // User context
    
    // make sure user can view this student's profile
    if ($USER->id != $user->id && !has_capability('moodle/user:viewdetails', $coursecontext) && !has_capability('moodle/user:viewdetails', $usercontext)) {
        error('You can not view the profile of this user');
    }

    if (!empty($CFG->forceloginforprofiles)) {
        require_login();
        if (isguest()) {
            redirect("$CFG->wwwroot/login/index.php");
        }
    }

    $strpersonalprofile = get_string('personalprofile');
    $strparticipants = get_string("participants");
    $struser = get_string("user");

    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $coursecontext));

/// If the user being shown is not ourselves, then make sure we are allowed to see them!

    if (!$currentuser) {

        if ($course->id == SITEID) {  // Reduce possibility of "browsing" userbase at site level
            if ($CFG->forceloginforprofiles and !isteacherinanycourse() and !isteacherinanycourse($user->id)) {  // Teachers can browse and be browsed at site level. If not forceloginforprofiles, allow access (bug #4366)
                print_header("$strpersonalprofile: ", "$strpersonalprofile: ",
                              "<a href=\"index.php?id=$course->id\">$strparticipants</a> -> $struser",
                              "", "", true, "&nbsp;", navmenu($course));
                print_heading(get_string('usernotavailable', 'error'));
                print_footer($course);
                exit;
            }
        } else {   // Normal course
            if (!has_capability('moodle/course:view', $coursecontext, $user->id, false)) {
                if (has_capability('moodle/course:view', $coursecontext)) {
                    print_header("$strpersonalprofile: ", "$strpersonalprofile: ",
                                     "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> ->
                                  <a href=\"index.php?id=$course->id\">$strparticipants</a> -> $fullname",
                                  "", "", true, "&nbsp;", navmenu($course));
                    print_heading(get_string('notenrolled', '', $fullname));
                } else {
                    print_header("$strpersonalprofile: ", "$strpersonalprofile: ",
                                     "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> ->
                                  <a href=\"index.php?id=$course->id\">$strparticipants</a> -> $struser",
                                  "", "", true, "&nbsp;", navmenu($course));
                    print_heading(get_string('notenrolledprofile'));
                }
                print_continue($_SERVER['HTTP_REFERER']);
                print_footer($course);
                exit;
            }
        }


        // If groups are in use, make sure we can see that group
        if (groupmode($course) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $coursecontext)) {
            require_login();
    
            ///this is changed because of mygroupid
            $gtrue = false;
            if ($mygroups = mygroupid($course->id)){
                foreach ($mygroups as $group){
                    if (ismember($group, $user->id)){
                        $gtrue = true;
                    }
                }
            }
            if (!$gtrue) {
                print_header("$strpersonalprofile: ", "$strpersonalprofile: ",
                             "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> ->
                              <a href=\"index.php?id=$course->id\">$strparticipants</a>",
                              "", "", true, "&nbsp;", navmenu($course));
                error(get_string("groupnotamember"), "../course/view.php?id=$course->id");
            }
        }
    }
  

/// We've established they can see the user's name at least, so what about the rest?

    if ($course->category) {
        print_header("$strpersonalprofile: $fullname", "$strpersonalprofile: $fullname",
                     "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> ->
                      <a href=\"index.php?id=$course->id\">$strparticipants</a> -> $fullname",
                      "", "", true, "&nbsp;", navmenu($course));
    } else {
        print_header("$course->fullname: $strpersonalprofile: $fullname", "$course->fullname",
                     "$fullname", "", "", true, "&nbsp;", navmenu($course));
    }


    if ($course->category and ! isguest() ) {   // Need to have access to a course to see that info
        if (!has_capability('moodle/course:view', $coursecontext)) {
            print_heading(get_string('notenrolled', '', $fullname));
            print_footer($course);
            die;
        }
    }

    if ($user->deleted) {
        print_heading(get_string('userdeleted'));
    }

/// OK, security out the way, now we are showing the user

    add_to_log($course->id, "user", "view", "view.php?id=$user->id&course=$course->id", "$user->id");

    if ($course->id != SITEID) {
        if ($lastaccess = get_record('user_lastaccess', 'userid', $user->id, 'courseid', $course->id)) {
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
    include('tabs.php');

    echo "<table width=\"80%\" align=\"center\" border=\"0\" cellspacing=\"0\" class=\"userinfobox\">";
    echo "<tr>";
    echo "<td width=\"100\" valign=\"top\" class=\"side\">";
    print_user_picture($user->id, $course->id, $user->picture, true, false, false);
    echo "</td><td width=\"100%\" class=\"content\">";

    // Print the description

    if ($user->description && !isset($hiddenfields['description'])) {
        echo format_text($user->description, FORMAT_MOODLE)."<hr />";
    }

    // Print all the little details in a list

    echo '<table border="0" cellpadding="0" cellspacing="0" class="list">';

    if (($user->city or $user->country) and (!isset($hiddenfields['city']) or !isset($hiddenfields['country']))) {
        $location = '';
        if ($user->city && !isset($hiddenfields['city'])) {
            $location .= $user->city;
        }
        if (!empty($countries[$user->country]) && !isset($hiddenfields['country'])) {
            if ($user->city && !isset($hiddenfields['country'])) {
                $location .= ', ';
            }
            $countries = get_list_of_countries();
            $location .= $countries[$user->country];
        }
        print_row(get_string("location").":", $location);
    }

    if (has_capability('moodle/user:viewhiddendetails', $coursecontext)) {
        if ($user->address) {
            print_row(get_string("address").":", "$user->address");
        }
        if ($user->phone1) {
            print_row(get_string("phone").":", "$user->phone1");
        }
        if ($user->phone2) {
            print_row(get_string("phone").":", "$user->phone2");
        }
    }

    if ($user->maildisplay == 1 or
       ($user->maildisplay == 2 and $course->category and !isguest()) or
       isteacher($course->id)) {

        $emailswitch = '';

        if (has_capability('moodle/course:useremail', $coursecontext) or $currentuser) {   /// Can use the enable/disable email stuff
            if (!empty($enable)) {     /// Recieved a parameter to enable the email address
                set_field('user', 'emailstop', 0, 'id', $user->id);
                $user->emailstop = 0;
            }
            if (!empty($disable)) {     /// Recieved a parameter to disable the email address
                set_field('user', 'emailstop', 1, 'id', $user->id);
                $user->emailstop = 1;
            }
        }

        if (has_capability('moodle/course:useremail', $coursecontext)) {   /// Can use the enable/disable email stuff
            if ($user->emailstop) {
                $switchparam = 'enable';
                $switchtitle = get_string('emaildisable');
                $switchclick = get_string('emailenableclick');
                $switchpix   = 'emailno.gif';
            } else {
                $switchparam = 'disable';
                $switchtitle = get_string('emailenable');
                $switchclick = get_string('emaildisableclick');
                $switchpix   = 'email.gif';
            }
            $emailswitch = "&nbsp;<a title=\"$switchclick\" ".
                           "href=\"view.php?id=$user->id&amp;course=$course->id&amp;$switchparam=1\">".
                           "<img border=\"0\" width=\"11\" height=\"11\" src=\"$CFG->pixpath/t/$switchpix\" alt=\"\" /></a>";

        } else if ($currentuser) {         /// Can only re-enable an email this way
            if ($user->emailstop) {   // Include link that tells how to re-enable their email
                $switchparam = 'enable';
                $switchtitle = get_string('emaildisable');
                $switchclick = get_string('emailenableclick');

                $emailswitch = "&nbsp;(<a title=\"$switchclick\" ".
                               "href=\"view.php?id=$user->id&amp;course=$course->id&amp;enable=1\">$switchtitle</a>)";
            }
        }

        print_row(get_string("email").":", obfuscate_mailto($user->email, '', $user->emailstop)."$emailswitch");
    }

    if ($user->url && !isset($hiddenfields['webpage'])) {
        print_row(get_string("webpage").":", "<a href=\"$user->url\">$user->url</a>");
    }

    if ($user->icq && !isset($hiddenfields['icqnumber'])) {
        print_row(get_string('icqnumber').':',"<a href=\"http://web.icq.com/wwp?uin=$user->icq\">$user->icq <img src=\"http://web.icq.com/whitepages/online?icq=$user->icq&amp;img=5\" width=\"18\" height=\"18\" border=\"0\" alt=\"\" /></a>");
    }

    if ($user->skype && !isset($hiddenfields['skypeid'])) {
        print_row(get_string('skypeid').':','<a href="callto:'.urlencode($user->skype).'">'.s($user->skype). 
            ' <img src="http://mystatus.skype.com/smallicon/'.urlencode($user->skype).'" alt="status" '.
            ' height="16" width="16" /></a>');
    }
    if ($user->yahoo && !isset($hiddenfields['yahooid'])) {
        print_row(get_string('yahooid').':', '<a href="http://edit.yahoo.com/config/send_webmesg?.target='.urlencode($user->yahoo).'&amp;.src=pg">'.s($user->yahoo)." <img border=0 src=\"http://opi.yahoo.com/online?u=".urlencode($user->yahoo)."&m=g&t=0\" width=\"12\" height=\"12\" alt=\"\"></a>");
    }
    if ($user->aim && !isset($hiddenfields['aimid'])) {
        print_row(get_string('aimid').':', '<a href="aim:goim?screenname='.s($user->aim).'">'.s($user->aim).'</a>');
    }
    if ($user->msn && !isset($hiddenfields['msnid'])) {
        print_row(get_string('msnid').':', s($user->msn));
    }

    if ($mycourses = get_my_courses($user->id)) {
        $courselisting = '';
        foreach ($mycourses as $mycourse) {
            if ($mycourse->visible and $mycourse->category) {
                if ($mycourse->id != $course->id){
                    $courselisting .= "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$mycourse->id\">$mycourse->fullname</a>, ";
                }
                else {
                    $courselisting .= "$mycourse->fullname, ";
                }
            }
        }
        print_row(get_string('courses').':', rtrim($courselisting,', '));
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
    
    if ($rolestring = get_user_roles_in_context($id, $coursecontext->id)) {
        print_row(get_string('roles').':', $rolestring);
    }

/// Printing groups
    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and $course->groupmodeforce and
                             !has_capability('moodle/site:accessallgroups', $coursecontext));
    if (!$isseparategroups){
        if ($usergroups = user_group($course->id, $user->id)){
            $groupstr = '';
            foreach ($usergroups as $group){
                $groupstr .= ' <a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$group->id.'">'.$group->name.'</a>,';
            }
            print_row(get_string("group").":", rtrim($groupstr, ', '));
        }
    }
/// End of printing groups

    echo "</table>";

    echo "</td></tr></table>";

    $internalpassword = false;
    if (is_internal_auth($user->auth) or (!empty($CFG->{'auth_'.$user->auth.'_stdchangepassword'}))) {
        if (empty($CFG->loginhttps)) {
            $internalpassword = "$CFG->wwwroot/login/change_password.php";
        } else {
            $internalpassword = str_replace('http:','https:',$CFG->wwwroot.'/login/change_password.php');
        }
    }

//  Print other functions
    echo '<div class="buttons"><table align="center"><tr>';

    if ($currentuser and !isguest()) {
        if ($internalpassword ) {
            echo "<td nowrap=\"nowrap\"><form action=\"$internalpassword\" method=\"get\">";
            echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />";
            echo "<input type=\"submit\" value=\"".get_string("changepassword")."\" />";
            echo "</form></td>";
        } else if ( strlen($CFG->{'auth_'.$user->auth.'_changepasswordurl'}) > 1 ) {
            echo "<td nowrap=\"nowrap\"><form action=\"".$CFG->{'auth_'.$user->auth.'_changepasswordurl'}."\" method=\"get\">";
            echo "<input type=\"submit\" value=\"".get_string("changepassword")."\" />";
            echo "</form></td>";
        }
    }

    if ($course->id != SITEID && empty($course->metacourse)) {   // Mostly only useful at course level

        if (($user->id == $USER->id &&                                               // Myself
             has_capability('moodle/course:view', $coursecontext, NULL) &&           // Course participant
             has_capability('moodle/role:unassignself', $coursecontext, NULL, false)) // Can unassign myself
             ||
            (has_capability('moodle/role:assign', $coursecontext, NULL) &&           // I can assign roles
             get_user_roles($coursecontext, $user->id)) ) {                          // This user has roles

            echo '<td nowrap="nowrap"><form action="../course/unenrol.php" method="get" />';
            echo '<input type="hidden" name="id" value="'.$course->id.'" />';
            echo '<input type="hidden" name="user" value="'.$user->id.'" />';
            echo '<input type="submit" value="'.get_string('unenrolme', '', $course->shortname).'">';
            echo '</form></td>';
        }
    }

    if ($USER->id != $user->id  && has_capability('moodle/user:loginas', $coursecontext) &&
                                 ! has_capability('moodle/site:doanything', $coursecontext, $user->id, false)) {
        echo '<td nowrap="nowrap"><form action="'.$CFG->wwwroot.'/course/loginas.php" method="get">';
        echo '<input type="hidden" name="id" value="'.$course->id.'" />';
        echo '<input type="hidden" name="user" value="'.$user->id.'" />';
        echo '<input type="submit" value="'.get_string('loginas').'" />';
        echo '</form></td>';
    }

    if (!empty($CFG->messaging) and !isguest()) {
        if (!empty($USER->id) and ($USER->id == $user->id)) {
            if ($countmessages = count_records('message', 'useridto', $user->id)) {
                $messagebuttonname = get_string("messages", "message")."($countmessages)";
            } else {
                $messagebuttonname = get_string("messages", "message");
            }
            echo "<td nowrap=\"nowrap\"><form target=\"message\" action=\"../message/index.php\" method=\"get\">";
            echo "<input type=\"submit\" value=\"$messagebuttonname\" onclick=\"return openpopup('/message/index.php', 'message', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0);\" />";
            echo "</form></td>";
        } else {
            echo "<td nowrap=\"nowrap\"><form target=\"message_$user->id\" action=\"../message/discussion.php\" method=\"get\">";
            echo "<input type=\"hidden\" name=\"id\" value=\"$user->id\" />";
            echo "<input type=\"submit\" value=\"".get_string("sendmessage", "message")."\" onclick=\"return openpopup('/message/discussion.php?id=$user->id', 'message_$user->id', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0);\" />";
            echo "</form></td>";
        }
    }
    // Authorize.net: User Payments
    if ($course->enrol == 'authorize' || (empty($course->enrol) && $CFG->enrol == 'authorize')) {
        echo "<td nowrap=\"nowrap\"><form action=\"../enrol/authorize/index.php\" method=\"get\">";
        echo "<input type=\"hidden\" name=\"course\" value=\"$course->id\" />";
        echo "<input type=\"hidden\" name=\"user\" value=\"$user->id\" />";
        echo "<input type=\"submit\" value=\"".get_string('payments')."\" />";
        echo "</form></td>";
    }
    echo "<td></td>";
    echo "</tr></table></div>\n";

/*
    if (debugging() && $USER->id == $user->id) {   // TEMPORARY in DEV!
        echo '<hr />';
        print_heading('DEBUG MODE:  User session variables');
        print_object($USER);
    }
*/

    print_footer($course);

/// Functions ///////

function print_row($left, $right) {
    echo "\n<tr><td nowrap=\"nowrap\" align=\"right\" valign=\"top\" class=\"label c0\">$left</td><td align=\"left\" valign=\"top\" class=\"info c1\">$right</td></tr>\n";
}

?>
