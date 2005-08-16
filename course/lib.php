<?php  // $Id$
   // Library of useful functions


if (defined('COURSE_MAX_LOG_DISPLAY')) {  // Being included again - should never happen!!
    return;
}

define('COURSE_MAX_LOG_DISPLAY', 150);       // days

define('COURSE_MAX_LOGS_PER_PAGE', 1000);    // records

define('COURSE_LIVELOG_REFRESH', 60);        // Seconds

define('COURSE_MAX_RECENT_PERIOD', 172800);  // Two days, in seconds

define('COURSE_MAX_SUMMARIES_PER_PAGE', 10); // courses

define('COURSE_MAX_COURSES_PER_DROPDOWN',5000); //  max courses in log dropdown before switching to optional

define('COURSE_MAX_USERS_PER_DROPDOWN',5000); //  max users in log dropdown before switching to optional

define("FRONTPAGENEWS",           0);
define("FRONTPAGECOURSELIST",     1);
define("FRONTPAGECATEGORYNAMES",  2);
define("FRONTPAGETOPICONLY",      3);

function print_recent_selector_form($course, $advancedfilter=0, $selecteduser=0, $selecteddate="lastlogin",
                                    $mod="", $modid="activity/All", $modaction="", $selectedgroup="", $selectedsort="default") {

    global $USER, $CFG;

    $isteacher = isteacher($course->id);
    if ($advancedfilter) {

        // Get all the possible users
        $users = array();

        if ($courseusers = get_course_users($course->id, '', '', 'u.id, u.firstname, u.lastname')) {
            foreach ($courseusers as $courseuser) {
                $users[$courseuser->id] = fullname($courseuser, $isteacher);
            }
        }
        if ($guest = get_guest()) {
            $users[$guest->id] = fullname($guest);
        }

        if (isadmin()) {
            if ($ccc = get_records("course", "", "", "fullname")) {
                foreach ($ccc as $cc) {
                    if ($cc->category) {
                        $courses["$cc->id"] = "$cc->fullname";
                    } else {
                        $courses["$cc->id"] = " $cc->fullname (Site)";
                    }
                }
            }
            asort($courses);
        }

        $activities = array();

        $selectedactivity = $modid;

        if ($modinfo = unserialize($course->modinfo)) {
            $section = 0;
            if ($course->format == 'weeks') {  // Body
                $strsection = get_string("week");
            } else {
                $strsection = get_string("topic");
            }

            $activities["activity/All"] = "All activities";
            $activities["activity/Assignments"] =  "All assignments";
            $activities["activity/Chats"] = "All chats";
            $activities["activity/Forums"] = "All forums";
            $activities["activity/Quizzes"] = "All quizzes";
            $activities["activity/Workshops"] = "All workshops";

            $activities["section/individual"] = "------------- Individual Activities --------------";

            foreach ($modinfo as $mod) {
                if ($mod->mod == "label") {
                    continue;
                }
                if (!$mod->visible and !$isteacher) {
                    continue;
                }

                if ($mod->section > 0 and $section <> $mod->section) {
                    $activities["section/$mod->section"] = "-------------- $strsection $mod->section --------------";
                }
                $section = $mod->section;
                $mod->name = strip_tags(format_string(urldecode($mod->name),true));
                if (strlen($mod->name) > 55) {
                    $mod->name = substr($mod->name, 0, 50)."...";
                }
                if (!$mod->visible) {
                    $mod->name = "(".$mod->name.")";
                }
                $activities["$mod->cm"] = $mod->name;

                if ($mod->cm == $modid) {
                    $selectedactivity = "$mod->cm";
                }
            }
        }

        $strftimedate = get_string("strftimedate");
        $strftimedaydate = get_string("strftimedaydate");

        asort($users);

        // Get all the possible dates
        // Note that we are keeping track of real (GMT) time and user time
        // User time is only used in displays - all calcs and passing is GMT

        $timenow = time(); // GMT

        // What day is it now for the user, and when is midnight that day (in GMT).
        $timemidnight = $today = usergetmidnight($timenow);

        $dates = array();
        $dates["$USER->lastlogin"] = get_string("lastlogin").", ".userdate($USER->lastlogin, $strftimedate);
        $dates["$timemidnight"] = get_string("today").", ".userdate($timenow, $strftimedate);

        if (!$course->startdate or ($course->startdate > $timenow)) {
            $course->startdate = $course->timecreated;
        }

        $numdates = 1;
        while ($timemidnight > $course->startdate and $numdates < 365) {
            $timemidnight = $timemidnight - 86400;
            $timenow = $timenow - 86400;
            $dates["$timemidnight"] = userdate($timenow, $strftimedaydate);
            $numdates++;
        }

        if ($selecteddate === "lastlogin") {
            $selecteddate = $USER->lastlogin;
        }

        echo '<form action="recent.php" method="get">';
        echo '<input type="hidden" name="chooserecent" value="1" />';
        echo "<center>";
        echo "<table>";

        if (isadmin()) {
            echo "<tr><td><b>" . get_string("courses") . "</b></td><td>";
            choose_from_menu ($courses, "id", $course->id, "");
            echo "</td></tr>";
        } else {
            echo '<input type="hidden" name="id" value="'.$course->id.'" />';
        }

        $sortfields = array("default" => get_string("bycourseorder"),"dateasc" => get_string("datemostrecentlast"), "datedesc" => get_string("datemostrecentfirst"));

        echo "<tr><td><b>" . get_string("participants") . "</b></td><td>";
        choose_from_menu ($users, "user", $selecteduser, get_string("allparticipants") );
        echo "</td>";

        echo '<td align="right"><b>' . get_string("since") . '</b></td><td>';
        choose_from_menu ($dates, "date", $selecteddate, get_string("alldays"));
        echo "</td></tr>";

        echo "<tr><td><b>" . get_string("activities") . "</b></td><td>";
        choose_from_menu ($activities, "modid", $selectedactivity, "");
        echo "</td>";

        echo '<td align="right"><b>' . get_string("sortby") . "</b></td><td>";
        choose_from_menu ($sortfields, "sortby", $selectedsort, "");
        echo "</td></tr>";

        echo '<tr>';

        $groupmode =  groupmode($course);

        if ($groupmode == VISIBLEGROUPS or ($groupmode and isteacheredit($course->id))) {
            if ($groups = get_records_menu("groups", "courseid", $course->id, "name ASC", "id,name")) {
            echo '<td><b>';
                if ($groupmode == VISIBLEGROUPS) {
                    print_string('groupsvisible');
                } else {
                    print_string('groupsseparate');
                }
                echo ':</b></td><td>';
                choose_from_menu($groups, "selectedgroup", $selectedgroup, get_string("allgroups"), "", "");
                echo '</td>';
            }
        }


        echo '<td colspan="2" align="right">';
        echo '<input type="submit" value="'.get_string('showrecent').'" />';
        echo "</td></tr>";

        echo "</table>";

        $advancedlink = "<a href=\"$CFG->wwwroot/course/recent.php?id=$course->id&amp;advancedfilter=0\">" . get_string("normalfilter") . "</a>";
        print_heading($advancedlink);
        echo "</center>";
        echo "</form>";

    } else {

        $day_list = array("1","7","14","21","30");
        $strsince = get_string("since");
        $strlastlogin = get_string("lastlogin");
        $strday = get_string("day");
        $strdays = get_string("days");

        $heading = "";
        foreach ($day_list as $count)  {
            if ($count == "1") {
              $day = $strday;
            } else {
              $day = $strdays;
            }
            $tmpdate = time() - ($count * 3600 * 24);
            $heading = $heading .
                "<a href=\"$CFG->wwwroot/course/recent.php?id=$course->id&amp;date=$tmpdate\"> $count $day</a> | ";
        }

        $heading = $strsince . ": <a href=\"$CFG->wwwroot/course/recent.php?id=$course->id\">$strlastlogin</a>" . " | " . $heading;
        print_heading($heading);

        $advancedlink = "<a href=\"$CFG->wwwroot/course/recent.php?id=$course->id&amp;advancedfilter=1\">" . get_string("advancedfilter") . "</a>";
        print_heading($advancedlink);

    }

}

function print_log_selector_form($course, $selecteduser=0, $selecteddate="today",
                                 $modname="", $modid=0, $modaction="", $selectedgroup=-1,$showcourses=0,$showusers=0) {

    global $USER, $CFG;

    // first check to see if we can override showcourses and showusers
    $numcourses =  count_records_select("course", "", "COUNT(id)");
    if ($numcourses < COURSE_MAX_COURSES_PER_DROPDOWN && !$showcourses) {
        $showcourses = 1;
    }
   

    if ($course->category) {
        if ($selectedgroup) {   // If using a group, only get users in that group.
            $courseusers = get_group_users($selectedgroup, 'u.lastname ASC', '', 'u.id');
        } else {
            $courseusers = get_course_users($course->id, '', '', 'u.id');
        }
    } else {
        $courseusers = get_site_users('u.lastaccess DESC', 'u.id');
    }

    $numusers = count((array)$courseusers);


    if ($numusers < COURSE_MAX_USERS_PER_DROPDOWN && !$showusers) {
        $showusers = 1;
    }
    

    /// Setup for group handling.
    $isteacher = isteacher($course->id);
    $isteacheredit = isteacheredit($course->id);
    if ($course->groupmode == SEPARATEGROUPS and !$isteacheredit) {
        $selectedgroup = get_current_group($course->id);
        $showgroups = false;
    }
    else if ($course->groupmode) {
        $selectedgroup = ($selectedgroup == -1) ? get_current_group($course->id) : $selectedgroup;
        $showgroups = true;
    }
    else {
        $selectedgroup = 0;
        $showgroups = false;
    }

    // Get all the possible users
    $users = array();

    if ($showusers) {
        if ($course->category) {
            if ($selectedgroup) {   // If using a group, only get users in that group.
                $courseusers = get_group_users($selectedgroup, 'u.lastname ASC', '', 'u.id, u.firstname, u.lastname');
            } else {
                $courseusers = get_course_users($course->id, '', '', 'u.id, u.firstname, u.lastname');
            }
        } else {
            $courseusers = get_site_users("u.lastaccess DESC", "u.id, u.firstname, u.lastname");
        }

        if ($courseusers) {
            foreach ($courseusers as $courseuser) {
                $users[$courseuser->id] = fullname($courseuser, $isteacher);
            }
        }
        if ($guest = get_guest()) {
            $users[$guest->id] = fullname($guest);
        }
    }

    if (isadmin() && $showcourses) {
        if ($ccc = get_records("course", "", "", "fullname","id,fullname,category")) {
            foreach ($ccc as $cc) {
                if ($cc->category) {
                    $courses["$cc->id"] = "$cc->fullname";
                } else {
                    $courses["$cc->id"] = " $cc->fullname (Site)";
                }
            }
        }
        asort($courses);
    }

    $activities = array();
    $selectedactivity = "";

    if ($modinfo = unserialize($course->modinfo)) {
        $section = 0;
        if ($course->format == 'weeks') {  // Bodgy
            $strsection = get_string("week");
        } else {
            $strsection = get_string("topic");
        }
        foreach ($modinfo as $mod) {
            if ($mod->mod == "label") {
                continue;
            }
            if ($mod->section > 0 and $section <> $mod->section) {
                $activities["section/$mod->section"] = "-------------- $strsection $mod->section --------------";
            }
            $section = $mod->section;
            $mod->name = strip_tags(format_string(urldecode($mod->name),true));
            if (strlen($mod->name) > 55) {
                $mod->name = substr($mod->name, 0, 50)."...";
            }
            if (!$mod->visible) {
                $mod->name = "(".$mod->name.")";
            }
            $activities["$mod->cm"] = $mod->name;

            if ($mod->cm == $modid) {
                $selectedactivity = "$mod->cm";
            }
        }
    }

    if (isadmin() && !$course->category) {
        $activities["site_errors"] = get_string("siteerrors");
        if ($modid === "site_errors") {
            $selectedactivity = "site_errors";
        }
    }

    $strftimedate = get_string("strftimedate");
    $strftimedaydate = get_string("strftimedaydate");

    asort($users);

    // Get all the possible dates
    // Note that we are keeping track of real (GMT) time and user time
    // User time is only used in displays - all calcs and passing is GMT

    $timenow = time(); // GMT

    // What day is it now for the user, and when is midnight that day (in GMT).
    $timemidnight = $today = usergetmidnight($timenow);

    // Put today up the top of the list
    $dates = array("$timemidnight" => get_string("today").", ".userdate($timenow, $strftimedate) );

    if (!$course->startdate or ($course->startdate > $timenow)) {
        $course->startdate = $course->timecreated;
    }

    $numdates = 1;
    while ($timemidnight > $course->startdate and $numdates < 365) {
        $timemidnight = $timemidnight - 86400;
        $timenow = $timenow - 86400;
        $dates["$timemidnight"] = userdate($timenow, $strftimedaydate);
        $numdates++;
    }

    if ($selecteddate == "today") {
        $selecteddate = $today;
    }

    echo "<center>\n";
    echo "<form action=\"log.php\" method=\"get\">\n";
    echo "<input type=\"hidden\" name=\"chooselog\" value=\"1\" />\n";
    echo "<input type=\"hidden\" name=\"showusers\" value=\"$showusers\" />\n";
    echo "<input type=\"hidden\" name=\"showcourses\" value=\"$showcourses\" />\n";
    if (isadmin() && $showcourses) { 
        choose_from_menu ($courses, "id", $course->id, "");
    } else {
        //        echo '<input type="hidden" name="id" value="'.$course->id.'" />';
        $courses = array();
        $courses[$course->id] = $course->fullname . ((empty($course->category)) ? ' (Site) ' : '');
        choose_from_menu($courses,"id",$course->id,false);
        if (isadmin()) {
            $a->url = "log.php?chooselog=0&group=$selectedgroup&user=$selecteduser"
                ."&id=$course->id&date=$selecteddate&modid=$selectedactivity&showcourses=1&showusers=$showusers";
            print_string('logtoomanycourses','moodle',$a);
        }
    }

    if ($showgroups) {
        if ($cgroups = get_groups($course->id)) {
            foreach ($cgroups as $cgroup) {
                $groups[$cgroup->id] = $cgroup->name;
            }
        }
        else {
            $groups = array();
        }
        choose_from_menu ($groups, "group", $selectedgroup, get_string("allgroups") );
    }

    if ($showusers) {
        choose_from_menu ($users, "user", $selecteduser, get_string("allparticipants") );
    }
    else {
        $users = array();
        if (!empty($selecteduser)) {
            $user = get_record('user','id',$selecteduser);
            $users[$selecteduser] = fullname($user);
        }
        else {
            $users[0] = get_string('allparticipants');
        }
        choose_from_menu($users,'user',$selecteduser,false);
        $a->url = "log.php?chooselog=0&group=$selectedgroup&user=$selecteduser"
            ."&id=$course->id&date=$selecteddate&modid=$selectedactivity&showusers=1&showcourses=$showcourses";
        print_string('logtoomanyusers','moodle',$a);
    }
    choose_from_menu ($dates, "date", $selecteddate, get_string("alldays"));
    choose_from_menu ($activities, "modid", $selectedactivity, get_string("allactivities"), "", "");
    echo '<input type="submit" value="'.get_string('showtheselogs').'" />';
    echo "</form>";
    echo "</center>";
}

function make_log_url($module, $url) {
    switch ($module) {
        case "user":
        case "course":
        case "file":
        case "login":
        case "lib":
        case "admin":
        case "message":
        case "calendar":
            return "/$module/$url";
            break;
        case "upload":
            return "$url";
            break;
        case "library":
        case "":
            return "/";
            break;
        default:
            return "/mod/$module/$url";
            break;
    }
}

function print_log($course, $user=0, $date=0, $order="l.time ASC", $page=0, $perpage=100,
                   $url="", $modname="", $modid=0, $modaction="", $groupid=0) {

    // It is assumed that $date is the GMT time of midnight for that day,
    // and so the next 86400 seconds worth of logs are printed.

    global $CFG, $db;

    /// Setup for group handling.
    $isteacher = isteacher($course->id);
    $isteacheredit = isteacheredit($course->id);

    /// If the group mode is separate, and this user does not have editing privileges,
    /// then only the user's group can be viewed.
    if ($course->groupmode == SEPARATEGROUPS and !$isteacheredit) {
        $groupid = get_current_group($course->id);
    }
    /// If this course doesn't have groups, no groupid can be specified.
    else if (!$course->groupmode) {
        $groupid = 0;
    }

    $joins = array();

    if ($course->category) {
        $joins[] = "l.course='$course->id'";
    } else {
        $courses[0] = '';
        if ($ccc = get_courses("all", "c.id ASC", "c.id,c.shortname")) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = "$cc->shortname";
            }
        }
    }

    if ($modname) {
        $joins[] = "l.module = '$modname'";
    }

    if ('site_errors' === $modid) {
        $joins[] = "( l.action='error' OR l.action='infected' )";
    } else if ($modid) {
        $joins[] = "l.cmid = '$modid'";
    }

    if ($modaction) {
        $joins[] = "l.action = '$modaction'";
    }

    /// Getting all members of a group.
    if ($groupid and !$user) {
        if ($gusers = get_records('groups_members', 'groupid', $groupid)) {
            $first = true;
            foreach($gusers as $guser) {
                if ($first) {
                    $gselect = '(l.userid='.$guser->userid;
                    $first = false;
                }
                else {
                    $gselect .= ' OR l.userid='.$guser->userid;
                }
            }
            if (!$first) $gselect .= ')';
            $joins[] = $gselect;
        }
    }
    else if ($user) {
        $joins[] = "l.userid = '$user'";
    }

    if ($date) {
        $enddate = $date + 86400;
        $joins[] = "l.time > '$date' AND l.time < '$enddate'";
    }

    $selector = '';
    for ($i = 0; $i < count($joins); $i++) {
        $selector .= $joins[$i] . (($i == count($joins)-1) ? " " : " AND ");
    }


    $totalcount = 0;  // Initialise

    if (!$logs = get_logs($selector, $order, $page*$perpage, $perpage, $totalcount)) {
        notify("No logs found!");
        print_footer($course);
        exit;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");
    $isteacher = isteacher($course->id);

    echo "<p align=\"center\">\n";
    print_string("displayingrecords", "", $totalcount);
    echo "</p>\n";


    print_paging_bar($totalcount, $page, $perpage, "$url&amp;perpage=$perpage&amp;");

    echo "<table class=\"logtable\" border=\"0\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">\n";
    echo "<tr>";
    if ($course->id == SITEID) {
        echo "<th class=\"c0 header\">".get_string('course')."</th>\n";
    }
    echo "<th class=\"c1 header\">".get_string('time')."</th>\n";
    echo "<th class=\"c2 header\">".get_string('ip_address')."</th>\n";
    echo "<th class=\"c3 header\">".get_string('fullname')."</th>\n";
    echo "<th class=\"c4 header\">".get_string('action')."</th>\n";
    echo "<th class=\"c5 header\">".get_string('info')."</th>\n";
    echo "</tr>\n";

    $row = 1;
    foreach ($logs as $log) {

        $row = ($row + 1) % 2;

        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = get_record('log_display', 'module', $log->module, 'action', $log->action);
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == 'CONCAT(firstname," ",lastname)')) {
                $log->info = fullname(get_record($ld->mtable, 'id', $log->info), true);
            } else {
                $log->info = get_field($ld->mtable, $ld->field, 'id', $log->info);
            }
        }

        //Filter log->info 
        $log->info = format_string($log->info);

        $log->url  = strip_tags(urldecode($log->url));   // Some XSS protection
        $log->info = strip_tags(urldecode($log->info));  // Some XSS protection
        $log->url  = str_replace('&', '&amp;', $log->url); /// XHTML compatibility

        echo '<tr class="r'.$row.'">';
        if ($course->id == SITEID) {
            echo "<td class=\"r$row c0\" nowrap=\"nowrap\">\n";
            echo "    <a href=\"view.php?id={$log->course}\">".$courses[$log->course]."</a>\n";
            echo "</td>\n";
        }
        echo "<td class=\"r$row c1\" nowrap=\"nowrap\" align=\"right\">".userdate($log->time, '%a').
             ' '.userdate($log->time, $strftimedatetime)."</td>\n";
        echo "<td class=\"r$row c2\" nowrap=\"nowrap\">\n";
        link_to_popup_window("/iplookup/index.php?ip=$log->ip&amp;user=$log->userid", 'iplookup',$log->ip, 400, 700);
        echo "</td>\n";
        $fullname = fullname($log, $isteacher);
        echo "<td class=\"r$row c3\" nowrap=\"nowrap\">\n";
        echo "    <a href=\"../user/view.php?id={$log->userid}&amp;course={$log->course}\">$fullname</a>\n";
        echo "</td>\n";
        echo "<td class=\"r$row c4\" nowrap=\"nowrap\">\n";
        link_to_popup_window( make_log_url($log->module,$log->url), 'fromloglive',"$log->module $log->action", 400, 600);
        echo "</td>\n";;
        echo "<td class=\"r$row c5\" nowrap=\"nowrap\">{$log->info}</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";

    print_paging_bar($totalcount, $page, $perpage, "$url&amp;perpage=$perpage&amp;");
}


function print_log_graph($course, $userid=0, $type="course.png", $date=0) {
    global $CFG;
    if (empty($CFG->gdversion)) {
        echo "(".get_string("gdneed").")";
    } else {
        echo '<img src="'.$CFG->wwwroot.'/course/loggraph.php?id='.$course->id.
             '&amp;user='.$userid.'&amp;type='.$type.'&amp;date='.$date.'" alt="" />';
    }
}


function print_overview($course) {

    global $CFG, $USER;

    if (!$lastaccess = get_record("user_teachers","userid",$USER->id,"course",$course->id)) {
        if (!$lastaccess = get_record("user_students","userid",$USER->id,"course",$course->id)) {
            return false;
        }
    }
    $lastaccess = $lastaccess->timeaccess;

    print_simple_box_start("center", '400', '', 5, "coursebox");
    print_heading('<a title="'.$course->fullname.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a>');
    if ($mods = get_course_mods($course->id)) {
        foreach ($mods as $mod) {
            if (file_exists(dirname(dirname(__FILE__)).'/mod/'.$mod->modname.'/lib.php')) {
                require_once(dirname(dirname(__FILE__)).'/mod/'.$mod->modname.'/lib.php');
                $fname = $mod->modname.'_print_overview';
                if (function_exists($fname)) {
                    $fname($course,$mod,$lastaccess);
                }
            }
        }
    }
    print_simple_box_end();
}



function print_recent_activity($course) {
    // $course is an object
    // This function trawls through the logs looking for
    // anything new since the user's last login

    global $CFG, $USER, $SESSION;

    $isteacher = isteacher($course->id);

    $timestart = time() - COURSE_MAX_RECENT_PERIOD;

    if (!empty($USER->timeaccess[$course->id])) {
        if ($USER->timeaccess[$course->id] > $timestart) {
            $timestart = $USER->timeaccess[$course->id];
        }
    }

    echo '<div class="activitydate">';
    echo get_string('activitysince', '', userdate($timestart));
    echo '</div>';
    echo '<div class="activityhead">';

    echo '<a href="'.$CFG->wwwroot.'/course/recent.php?id='.$course->id.'">'.get_string('recentactivityreport').'</a>';

    echo '</div>';


    // Firstly, have there been any new enrolments?

    $heading = false;
    $content = false;

    $users = get_recent_enrolments($course->id, $timestart);

    if ($users) {
        echo '<div class="newusers">';
        foreach ($users as $user) {
            if (! $heading) {
                print_headline(get_string("newusers").':', 3);
                $heading = true;
                $content = true;
            }
            $fullname = fullname($user, $isteacher);
            echo '<span class="name"><a href="'.$CFG->wwwroot."/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a></span><br />";
        }
        echo '</div>';
    }

    // Next, have there been any modifications to the course structure?

    $logs = get_records_select('log', "time > '$timestart' AND course = '$course->id' AND
                                       module = 'course' AND action LIKE '% mod'", "time ASC");

    if ($logs) {
        foreach ($logs as $key => $log) {
            $info = split(' ', $log->info);

            if ($info[0] == 'label') {     // Labels are special activities
                continue;
            }

            $modname = get_field($info[0], 'name', 'id', $info[1]);
            //Create a temp valid module structure (course,id)
            $tempmod->course = $log->course;
            $tempmod->id = $info[1];
            //Obtain the visible property from the instance
            $modvisible = instance_is_visible($info[0],$tempmod);

            //Only if the mod is visible
            if ($modvisible) {
                switch ($log->action) {
                    case 'add mod':
                        $stradded = get_string('added', 'moodle', get_string('modulename', $info[0]));
                        $changelist[$log->info] = array ('operation' => 'add', 'text' => "$stradded:<br /><a href=\"$CFG->wwwroot/course/$log->url\">".format_string($modname,true)."</a>");
                    break;
                    case 'update mod':
                       $strupdated = get_string('updated', 'moodle', get_string('modulename', $info[0]));
                       if (empty($changelist[$log->info])) {
                           $changelist[$log->info] = array ('operation' => 'update', 'text' => "$strupdated:<br /><a href=\"$CFG->wwwroot/course/$log->url\">".format_string($modname,true)."</a>");
                       }
                    break;
                    case 'delete mod':
                       if (!empty($changelist[$log->info]['operation']) and
                                  $changelist[$log->info]['operation'] == 'add') {
                           $changelist[$log->info] = NULL;
                       } else {
                           $strdeleted = get_string('deletedactivity', 'moodle', get_string('modulename', $info[0]));
                           $changelist[$log->info] = array ('operation' => 'delete', 'text' => $strdeleted);
                       }
                    break;
                }
            }
        }
    }

    if (!empty($changelist)) {
        foreach ($changelist as $changeinfo => $change) {
            if ($change) {
                $changes[$changeinfo] = $change;
            }
        }
        if (isset($changes)){
            if (count($changes) > 0) {
                print_headline(get_string('courseupdates').':', 3);
                $content = true;
                foreach ($changes as $changeinfo => $change) {
                    echo '<p class="activity">'.$change['text'].'</p>';
                }
            }
        }
    }

    // Now display new things from each module

    $mods = get_records('modules', 'visible', '1', 'name', 'id, name');

    foreach ($mods as $mod) {      // Each module gets it's own logs and prints them
        include_once($CFG->dirroot.'/mod/'.$mod->name.'/lib.php');
        $print_recent_activity = $mod->name.'_print_recent_activity';
        if (function_exists($print_recent_activity)) {
            $modcontent = $print_recent_activity($course, $isteacher, $timestart);
            if ($modcontent) {
                $content = true;
            }
        }
    }

    if (! $content) {
        echo '<p class="message">'.get_string('nothingnew').'</p>';
    }
}


function get_array_of_activities($courseid) {
// For a given course, returns an array of course activity objects
// Each item in the array contains he following properties:
//  cm - course module id
//  mod - name of the module (eg forum)
//  section - the number of the section (eg week or topic)
//  name - the name of the instance
//  visible - is the instance visible or not
//  extra - contains extra string to include in any link

    global $CFG;

    $mod = array();

    if (!$rawmods = get_course_mods($courseid)) {
        return NULL;
    }

    if ($sections = get_records("course_sections", "course", $courseid, "section ASC")) {
       foreach ($sections as $section) {
           if (!empty($section->sequence)) {
               $sequence = explode(",", $section->sequence);
               foreach ($sequence as $seq) {
                   if (empty($rawmods[$seq])) {
                       continue;
                   }
                   $mod[$seq]->cm = $rawmods[$seq]->id;
                   $mod[$seq]->mod = $rawmods[$seq]->modname;
                   $mod[$seq]->section = $section->section;
                   $mod[$seq]->name = urlencode(get_field($rawmods[$seq]->modname, "name", "id", $rawmods[$seq]->instance));
                   $mod[$seq]->visible = $rawmods[$seq]->visible;
                   $mod[$seq]->extra = "";

                   $modname = $mod[$seq]->mod;
                   $functionname = $modname."_get_coursemodule_info";

                   include_once("$CFG->dirroot/mod/$modname/lib.php");

                   if (function_exists($functionname)) {
                       if ($info = $functionname($rawmods[$seq])) {
                           if (!empty($info->extra)) {
                               $mod[$seq]->extra = $info->extra;
                           }
                           if (!empty($info->icon)) {
                               $mod[$seq]->icon = $info->icon;
                           }
                       }
                   }
               }
            }
        }
    }
    return $mod;
}




function get_all_mods($courseid, &$mods, &$modnames, &$modnamesplural, &$modnamesused) {
// Returns a number of useful structures for course displays

    $mods          = NULL;    // course modules indexed by id
    $modnames      = NULL;    // all course module names (except resource!)
    $modnamesplural= NULL;    // all course module names (plural form)
    $modnamesused  = NULL;    // course module names used

    if ($allmods = get_records("modules")) {
        foreach ($allmods as $mod) {
            if ($mod->visible) {
                $modnames[$mod->name] = get_string("modulename", "$mod->name");
                $modnamesplural[$mod->name] = get_string("modulenameplural", "$mod->name");
            }
        }
        asort($modnames);
    } else {
        error("No modules are installed!");
    }

    if ($rawmods = get_course_mods($courseid)) {
        foreach($rawmods as $mod) {    // Index the mods
            if (empty($modnames[$mod->modname])) {
                continue;
            }
            $mods[$mod->id] = $mod;
            $mods[$mod->id]->modfullname = $modnames[$mod->modname];
            if ($mod->visible or isteacher($courseid)) {
                $modnamesused[$mod->modname] = $modnames[$mod->modname];
            }
        }
        if ($modnamesused) {
            asort($modnamesused);
        }
    }

    unset($modnames['resource']);
    unset($modnames['label']);
}


function get_all_sections($courseid) {

    return get_records("course_sections", "course", "$courseid", "section",
                       "section, id, course, summary, sequence, visible");
}

function course_set_display($courseid, $display=0) {
    global $USER;

    if (empty($USER->id)) {
        return false;
    }

    if ($display == "all" or empty($display)) {
        $display = 0;
    }

    if (record_exists("course_display", "userid", $USER->id, "course", $courseid)) {
        set_field("course_display", "display", $display, "userid", $USER->id, "course", $courseid);
    } else {
        $record->userid = $USER->id;
        $record->course = $courseid;
        $record->display = $display;
        if (!insert_record("course_display", $record)) {
            notify("Could not save your course display!");
        }
    }

    return $USER->display[$courseid] = $display;  // Note: = not ==
}

function set_section_visible($courseid, $sectionnumber, $visibility) {
/// For a given course section, markes it visible or hidden,
/// and does the same for every activity in that section

    if ($section = get_record("course_sections", "course", $courseid, "section", $sectionnumber)) {
        set_field("course_sections", "visible", "$visibility", "id", $section->id);
        if (!empty($section->sequence)) {
            $modules = explode(",", $section->sequence);
            foreach ($modules as $moduleid) {
                set_coursemodule_visible($moduleid, $visibility);
            }
        }
        rebuild_course_cache($courseid);
    }
}


function print_section($course, $section, $mods, $modnamesused, $absolute=false, $width="100%") {
/// Prints a section full of activity modules
    global $CFG, $USER;

    static $groupbuttons;
    static $groupbuttonslink;
    static $isteacher;
    static $isediting;
    static $ismoving;
    static $strmovehere;
    static $strmovefull;
    static $strunreadpostsone;

    static $untracked;
    static $usetracking;

    $labelformatoptions = New stdClass;

    if (!isset($isteacher)) {
        $groupbuttons     = ($course->groupmode or (!$course->groupmodeforce));
        $groupbuttonslink = (!$course->groupmodeforce);
        $isteacher = isteacher($course->id);
        $isediting = isediting($course->id);
        $ismoving = $isediting && ismoving($course->id);
        if ($ismoving) {
            $strmovehere = get_string("movehere");
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }
        include_once($CFG->dirroot.'/mod/forum/lib.php');
        if ($usetracking = forum_tp_can_track_forums()) {
            $strunreadpostsone    = get_string('unreadpostsone', 'forum');
            $untracked = forum_tp_get_untracked_forums($USER->id, $course->id);
        }
    }
    $labelformatoptions->noclean = true;

    $modinfo = unserialize($course->modinfo);

    echo '<table width="'.$width.'" class="section">';
    if (!empty($section->sequence)) {

        $sectionmods = explode(",", $section->sequence);

        foreach ($sectionmods as $modnumber) {
            if (empty($mods[$modnumber])) {
                continue;
            }
            $mod = $mods[$modnumber];

            if ($mod->visible or $isteacher) {
                echo '<tr><td class="activity '.$mod->modname.'">';
                if ($ismoving) {
                    if ($mod->id == $USER->activitycopy) {
                        continue;
                    }
                    echo '<a title="'.$strmovefull.'"'.
                         ' href="'.$CFG->wwwroot.'/course/mod.php?moveto='.$mod->id.'&amp;sesskey='.$USER->sesskey.'">'.
                         '<img class="movetarget" src="'.$CFG->pixpath.'/movehere.gif" '.
                         ' alt="'.$strmovehere.'" /></a><br />
                         ';
                }
                $instancename = urldecode($modinfo[$modnumber]->name);
                    $instancename = format_string($instancename, true,  $course->id);

                if (!empty($modinfo[$modnumber]->extra)) {
                    $extra = urldecode($modinfo[$modnumber]->extra);
                } else {
                    $extra = "";
                }

                if (!empty($modinfo[$modnumber]->icon)) {
                    $icon = "$CFG->pixpath/".urldecode($modinfo[$modnumber]->icon);
                } else {
                    $icon = "$CFG->modpixpath/$mod->modname/icon.gif";
                }

                if ($mod->indent) {
                    print_spacer(12, 20 * $mod->indent, false);
                }

                if ($mod->modname == "label") {
                    if (!$mod->visible) {
                        echo "<span class=\"dimmed_text\">";
                    }
                    echo format_text($extra, FORMAT_HTML, $labelformatoptions);
                    if (!$mod->visible) {
                        echo "</span>";
                    }

                } else { // Normal activity
                    $linkcss = $mod->visible ? "" : " class=\"dimmed\" ";
                    echo '<img src="'.$icon.'"'.
                         ' class="activityicon" alt="'.$mod->modfullname.'" />'.
                         ' <a title="'.$mod->modfullname.'" '.$linkcss.' '.$extra.
                         ' href="'.$CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id.'">'.
                         $instancename.'</a>';
                }
                if ($usetracking && $mod->modname == 'forum') {
                    $groupmode = groupmode($course, $mod);
                    $groupid = ($groupmode == SEPARATEGROUPS && !isteacheredit($course->id)) ?
                               get_current_group($course->id) : false;

                    if (forum_tp_can_track_forums() && !isset($untracked[$mod->instance])) {
                        $unread = forum_tp_count_forum_unread_posts($USER->id, $mod->instance, $groupid);
                        if ($unread) {
                            echo '<span class="unread"> <a href="'.$CFG->wwwroot.'/mod/forum/view.php?id='.$mod->id.'">';
                            if ($unread == 1) {
                                echo $strunreadpostsone;
                            } else {
                                print_string('unreadpostsnumber', 'forum', $unread);
                            }
                            echo '</a> </span>';
                        }
                    }
                }

                if ($isediting) {
                    if ($groupbuttons and $mod->modname != 'label' and $mod->modname != 'resource') {
                        if (! $mod->groupmodelink = $groupbuttonslink) {
                            $mod->groupmode = $course->groupmode;
                        }

                    } else {
                        $mod->groupmode = false;
                    }
                    echo '&nbsp;&nbsp;';
                    echo make_editing_buttons($mod, $absolute, true, $mod->indent, $section->section);
                }
                echo "</td>";
                echo "</tr>";
            }
        }
    } else {
        echo "<tr><td></td></tr>"; // needed for XHTML compatibility
    }
    if ($ismoving) {
        echo '<tr><td><a title="'.$strmovefull.'"'.
             ' href="'.$CFG->wwwroot.'/course/mod.php?movetosection='.$section->id.'&amp;sesskey='.$USER->sesskey.'">'.
             '<img class="movetarget" src="'.$CFG->pixpath.'/movehere.gif" '.
             ' alt="'.$strmovehere.'" /></a></td></tr>
             ';
    }
    echo "</table>\n\n";
}


function print_section_add_menus($course, $section, $modnames, $vertical=false, $return=false) {
// Prints the menus to add activities and resources

    global $CFG, $USER;
    static $straddactivity, $stractivities, $straddresource, $resources;

    if (!isset($straddactivity)) {
        $straddactivity = get_string('addactivity');
        $straddresource = get_string('addresource');

        /// Standard resource types
        require_once("$CFG->dirroot/mod/resource/lib.php");
        $resourceraw = resource_get_resource_types();

        foreach ($resourceraw as $type => $name) {
            $resources["resource&amp;type=$type"] = $name;
        }
        if (course_allowed_module($course,'label')) {
            $resources['label'] = get_string('resourcetypelabel', 'resource');
        }
    }

    $output  = '<div style="text-align: right">';
    if (course_allowed_module($course,'resource')) {
        $output .= popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=",
                              $resources, "ressection$section", "", $straddresource, 'resource/types', $straddresource, true);
    }

    if ($vertical) {
        $output .= '<div>';
    }

    // we need to loop through the forms and check to see if we can add them.
    foreach ($modnames as $key) {
        if (!course_allowed_module($course,$key))
            unset($modnames[strtolower($key)]);
    }
    
    // this is stupid but labels get put into resource, so if resource is hidden and label is not, we're in trouble.
    if (course_allowed_module($course,'label') && empty($resourceallowed)) {
        $modnames['label'] = get_string('modulename', 'label');
    }

    $output .= ' ';
    $output .= popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=",
                $modnames, "section$section", "", $straddactivity, 'mods', $straddactivity, true);

    if ($vertical) {
        $output .= '</div>';
    }

    $output .= '</div>';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

function rebuild_course_cache($courseid=0) {
// Rebuilds the cached list of course activities stored in the database
// If a courseid is not specified, then all are rebuilt

    if ($courseid) {
        $select = "id = '$courseid'";
    } else {
        $select = "";
    }

    if ($courses = get_records_select("course", $select,'','id,fullname')) {
        foreach ($courses as $course) {
            $modinfo = serialize(get_array_of_activities($course->id));
            if (!set_field("course", "modinfo", $modinfo, "id", $course->id)) {
                notify("Could not cache module information for course '$course->fullname'!");
            }
        }
    }
}



function make_categories_list(&$list, &$parents, $category=NULL, $path="") {
/// Given an empty array, this function recursively travels the
/// categories, building up a nice list for display.  It also makes
/// an array that list all the parents for each category.

    if ($category) {
        if ($path) {
            $path = $path.' / '.$category->name;
        } else {
            $path = $category->name;
        }
        $list[$category->id] = $path;
    } else {
        $category->id = 0;
    }

    if ($categories = get_categories($category->id)) {   // Print all the children recursively
        foreach ($categories as $cat) {
            if (!empty($category->id)) {
                if (isset($parents[$category->id])) {
                    $parents[$cat->id]   = $parents[$category->id];
                }
                $parents[$cat->id][] = $category->id;
            }
            make_categories_list($list, $parents, $cat, $path);
        }
    }
}


function print_whole_category_list($category=NULL, $displaylist=NULL, $parentslist=NULL, $depth=-1) {
/// Recursive function to print out all the categories in a nice format
/// with or without courses included
    global $CFG;

    if (isset($CFG->max_category_depth) && ($depth >= $CFG->max_category_depth)) {
        return;
    }

    if (!$displaylist) {
        make_categories_list($displaylist, $parentslist);
    }

    if ($category) {
        if ($category->visible or iscreator()) {
            print_category_info($category, $depth);
        } else {
            return;  // Don't bother printing children of invisible categories
        }

    } else {
        $category->id = "0";
    }

    if ($categories = get_categories($category->id)) {   // Print all the children recursively
        $countcats = count($categories);
        $count = 0;
        $first = true;
        $last = false;
        foreach ($categories as $cat) {
            $count++;
            if ($count == $countcats) {
                $last = true;
            }
            $up = $first ? false : true;
            $down = $last ? false : true;
            $first = false;

            print_whole_category_list($cat, $displaylist, $parentslist, $depth + 1, $printfunction);
        }
    }
}

// this function will return $options array for choose_from_menu, with whitespace to denote nesting.

function make_categories_options() {
    make_categories_list($cats,$parents);
    foreach ($cats as $key => $value) {
        if (array_key_exists($key,$parents)) {
            if ($indent = count($parents[$key])) {
                for ($i = 0; $i < $indent; $i++) {
                    $cats[$key] = '&nbsp;'.$cats[$key];
                }
            }
        }
    }
    return $cats;
}

function print_category_info($category, $depth) {
/// Prints the category info in indented fashion
/// This function is only used by print_whole_category_list() above

    global $CFG;
    static $strallowguests, $strrequireskey, $strsummary;

    if (empty($strsummary)) {
        $strallowguests = get_string('allowguests');
        $strrequireskey = get_string('requireskey');
        $strsummary = get_string('summary');
    }

    $catlinkcss = $category->visible ? '' : ' class="dimmed" ';

    if ($CFG->frontpage == FRONTPAGECOURSELIST) {
        $catimage = '<img src="'.$CFG->pixpath.'/i/course.gif" width="16" height="16" border="0" alt="" />';
    } else {
        $catimage = "&nbsp";
    }

    echo "\n\n".'<table border="0" cellpadding="3" cellspacing="0" width="100%">';

    if ($CFG->frontpage == FRONTPAGECOURSELIST) {
        $courses = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.guest,c.cost,c.currency');

        echo "<tr>";

        if ($depth) {
            $indent = $depth*30;
            $rows = count($courses) + 1;
            echo '<td rowspan="'.$rows.'" valign="top" width="'.$indent.'">';
            print_spacer(10, $indent);
            echo '</td>';
        }

        echo '<td valign="top">'.$catimage.'</td>';
        echo '<td valign="top" width="100%" class="category name">';
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/course/category.php?id='.$category->id.'">'.$category->name.'</a>';
        echo '</td>';
        echo '<td class="category info">&nbsp;</td>';
        echo '</tr>';

        if ($courses && !(isset($CFG->max_category_depth)&&($depth>=$CFG->max_category_depth-1))) {
            foreach ($courses as $course) {
                $linkcss = $course->visible ? '' : ' class="dimmed" ';
                echo '<tr><td valign="top" width="30">&nbsp;';
                echo '</td><td valign="top" width="100%" class="course name">';
                echo '<a '.$linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a>';
                echo '</td><td align="right" valign="top" nowrap="nowrap" class="course info">';
                if ($course->guest ) {
                    echo '<a title="'.$strallowguests.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
                    echo '<img hspace="1" alt="'.$strallowguests.'" height="16" width="16" border="0" src="'.$CFG->pixpath.'/i/guest.gif" /></a>';
                } else {
                    echo '<img alt="" height="16" width="18" border="0" src="'.$CFG->pixpath.'/spacer.gif" />';
                }
                if ($course->password) {
                    echo '<a title="'.$strrequireskey.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
                    echo '<img hspace="1" alt="'.$strrequireskey.'" height="16" width="16" border="0" src="'.$CFG->pixpath.'/i/key.gif" /></a>';
                } else {
                    echo '<img alt="" height="16" width="18" border="0" src="'.$CFG->pixpath.'/spacer.gif" />';
                }
                if ($course->summary) {
                    link_to_popup_window ('/course/info.php?id='.$course->id, 'courseinfo',
                                          '<img hspace="1" alt="'.$strsummary.'" height="16" width="16" border="0" src="'.$CFG->pixpath.'/i/info.gif" />',
                                           400, 500, $strsummary);
                } else {
                    echo '<img alt="" height="16" width="18" border="0" src="'.$CFG->pixpath.'/spacer.gif" />';
                }
                echo '</td></tr>';
            }
        }
    } else {

        if ($depth) {
            $indent = $depth*20;
            echo '<td valign="top" width="'.$indent.'">';
            print_spacer(10, $indent);
            echo '</td>';
        }

        echo '<td valign="top" width="100%" class="category name">';
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/course/category.php?id='.$category->id.'">'.$category->name.'</a>';
        echo '</td>';
        echo '<td valign="top" class="category number">';
        if ($category->coursecount) {
            echo $category->coursecount;
        }
        echo '</td></tr>';
    }
    echo '</table>';
}


function print_courses($category, $width="100%") {
/// Category is 0 (for all courses) or an object

    global $CFG;

    if (empty($category)) {
        $categories = get_categories(0);  // Parent = 0   ie top-level categories only
        if (count($categories) == 1) {
            $category   = array_shift($categories);
            $courses    = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.teacher,c.cost,c.currency');
        } else {
            $courses    = get_courses('all', 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.teacher,c.cost,c.currency');
        }
        unset($categories);
    } else {
        $categories = get_categories($category->id);  // sub categories
        $courses    = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.teacher,c.cost,c.currency');
    }

    if ($courses) {
        foreach ($courses as $course) {
            print_course($course, $width);
        }
    } else {
        print_heading(get_string("nocoursesyet"));
    }

}


function print_course($course, $width="100%") {

    global $CFG;

    static $enrol;

    if (empty($enrol)) {
        require_once("$CFG->dirroot/enrol/$CFG->enrol/enrol.php");
        $enrol = new enrolment_plugin;
    }

    print_simple_box_start("center", "$width", '', 5, "coursebox");

    $linkcss = $course->visible ? "" : " class=\"dimmed\" ";

    echo "<table width=\"100%\">";
    echo '<tr valign="top">';
    echo '<td valign="top" width="50%" class="info">';
    echo '<b><a title="'.get_string('entercourse').'"'.
         $linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.
         $course->fullname.'</a></b><br />';
    if ($teachers = get_course_teachers($course->id)) {
        echo "<span class=\"teachers\">\n";
        foreach ($teachers as $teacher) {
            if ($teacher->authority > 0) {
                if (!$teacher->role) {
                    $teacher->role = $course->teacher;
                }
                $fullname = fullname($teacher, isteacher($course->id)); // is the USER a teacher of that course
                echo $teacher->role.': <a href="'.$CFG->wwwroot.'/user/view.php?id='.$teacher->id.
                     '&amp;course='.SITEID.'">'.$fullname.'</a><br />';
            }
        }
        echo "</span>\n";
    }

    echo $enrol->get_access_icons($course);

    echo '</td><td valign="top" width="50%" class="summary">';
    $options = NULL;
    $options->noclean = true;
    $options->para = false;
    echo format_text($course->summary, FORMAT_MOODLE, $options,  $course->id);
    echo "</td></tr>";
    echo "</table>";

    print_simple_box_end();
}


function print_my_moodle() {
/// Prints custom user information on the home page.
/// Over time this can include all sorts of information

    global $USER, $CFG;

    if (!isset($USER->id)) {
        error("It shouldn't be possible to see My Moodle without being logged in.");
    }

    if ($courses = get_my_courses($USER->id)) {
        foreach ($courses as $course) {
            if (!$course->category) {
                continue;
            }
            print_course($course, "100%");
        }

        if (count_records("course") > (count($courses) + 1) ) {  // Some courses not being displayed
            echo "<table width=\"100%\"><tr><td align=\"center\">";
            print_course_search("", false, "short");
            echo "</td><td align=\"center\">";
            print_single_button("$CFG->wwwroot/course/index.php", NULL, get_string("fulllistofcourses"), "get");
            echo "</td></tr></table>\n";
        }
    } else {
        if (count_records("course_categories") > 1) {
            print_simple_box_start("center", "100%", "#FFFFFF", 5, "categorybox");
            print_whole_category_list();
            print_simple_box_end();
        } else {
            print_courses(0, "100%");
        }
    }
}


function print_course_search($value="", $return=false, $format="plain") {

    global $CFG;

    $strsearchcourses= get_string("searchcourses");

    if ($format == 'plain') {
        $output  = '<form name="coursesearch" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<center><p align="center" class="coursesearchbox">';
        $output .= '<input type="text" size="30" name="search" alt="'.$strsearchcourses.'" value="'.$value.'" />';
        $output .= '<input type="submit" value="'.$strsearchcourses.'" />';
        $output .= '</p></center></form>';
    } else if ($format == 'short') {
        $output  = '<form name="coursesearch" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<center><p align="center" class="coursesearchbox">';
        $output .= '<input type="text" size="12" name="search" alt="'.$strsearchcourses.'" value="'.$value.'" />';
        $output .= '<input type="submit" value="'.$strsearchcourses.'" />';
        $output .= '</p></center></form>';
    } else if ($format == 'navbar') {
        $output  = '<form name="coursesearch" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<table border="0" cellpadding="0" cellspacing="0"><tr><td nowrap="nowrap">';
        $output .= '<input type="text" size="20" name="search" alt="'.$strsearchcourses.'" value="'.$value.'" />';
        $output .= '<input type="submit" value="'.$strsearchcourses.'" />';
        $output .= '</td></tr></table>';
        $output .= '</form>';
    }

    if ($return) {
        return $output;
    }
    echo $output;
}

/// MODULE FUNCTIONS /////////////////////////////////////////////////////////////////

function add_course_module($mod) {

    $mod->added = time();
    unset($mod->id);

    return insert_record("course_modules", $mod);
}

function add_mod_to_section($mod, $beforemod=NULL) {
/// Given a full mod object with section and course already defined
/// If $before is specified, then this is an existing ID which we
/// will insert the new module before
///
/// Returns the course_sections ID where the mod is inserted

    if ($section = get_record("course_sections", "course", "$mod->course", "section", "$mod->section")) {

        $section->sequence = trim($section->sequence);

        if (empty($section->sequence)) {
            $newsequence = "$mod->coursemodule";

        } else if ($beforemod) {
            $modarray = explode(",", $section->sequence);

            if ($key = array_keys ($modarray, $beforemod->id)) {
                $insertarray = array($mod->id, $beforemod->id);
                array_splice($modarray, $key[0], 1, $insertarray);
                $newsequence = implode(",", $modarray);

            } else {  // Just tack it on the end anyway
                $newsequence = "$section->sequence,$mod->coursemodule";
            }

        } else {
            $newsequence = "$section->sequence,$mod->coursemodule";
        }

        if (set_field("course_sections", "sequence", $newsequence, "id", $section->id)) {
            return $section->id;     // Return course_sections ID that was used.
        } else {
            return 0;
        }

    } else {  // Insert a new record
        $section->course = $mod->course;
        $section->section = $mod->section;
        $section->summary = "";
        $section->sequence = $mod->coursemodule;
        return insert_record("course_sections", $section);
    }
}

function set_coursemodule_groupmode($id, $groupmode) {
    return set_field("course_modules", "groupmode", $groupmode, "id", $id);
}

function set_coursemodule_visible($id, $visible) {
    $cm = get_record('course_modules', 'id', $id);
    $modulename = get_field('modules', 'name', 'id', $cm->module);
    if ($events = get_records_select('event', "instance = '$cm->instance' AND modulename = '$modulename'")) {
        foreach($events as $event) {
            if ($visible) {
                show_event($event);
            } else {
                hide_event($event);
            }
        }
    }
    return set_field("course_modules", "visible", $visible, "id", $id);
}

/*
 * Delete a course module and any associated data at the course level (events)
 * Until 1.5 this function simply marked a deleted flag ... now it 
 * deletes it completely.
 *
 */
function delete_course_module($id) {
    if (!$cm = get_record('course_modules', 'id', $id)) {
        return true;
    }
    $modulename = get_field('modules', 'name', 'id', $cm->module);
    if ($events = get_records_select('event', "instance = '$cm->instance' AND modulename = '$modulename'")) {
        foreach($events as $event) {
            delete_event($event);
        }
    }
    return delete_records('course_modules', 'id', $cm->id);
}

function delete_mod_from_section($mod, $section) {

    if ($section = get_record("course_sections", "id", "$section") ) {

        $modarray = explode(",", $section->sequence);

        if ($key = array_keys ($modarray, $mod)) {
            array_splice($modarray, $key[0], 1);
            $newsequence = implode(",", $modarray);
            return set_field("course_sections", "sequence", $newsequence, "id", $section->id);
        } else {
            return false;
        }

    }
    return false;
}

function move_section($course, $section, $move) {
/// Moves a whole course section up and down within the course
    global $USER;

    if (!$move) {
        return true;
    }

    $sectiondest = $section + $move;

    if ($sectiondest > $course->numsections or $sectiondest < 1) {
        return false;
    }

    if (!$sectionrecord = get_record("course_sections", "course", $course->id, "section", $section)) {
        return false;
    }

    if (!$sectiondestrecord = get_record("course_sections", "course", $course->id, "section", $sectiondest)) {
        return false;
    }

    if (!set_field("course_sections", "section", $sectiondest, "id", $sectionrecord->id)) {
        return false;
    }
    if (!set_field("course_sections", "section", $section, "id", $sectiondestrecord->id)) {
        return false;
    }
    // if the focus is on the section that is being moved, then move the focus along
    if (isset($USER->display[$course->id]) and ($USER->display[$course->id] == $section)) {
        course_set_display($course->id, $sectiondest);
    }
    return true;
}


function moveto_module($mod, $section, $beforemod=NULL) {
/// All parameters are objects
/// Move the module object $mod to the specified $section
/// If $beforemod exists then that is the module
/// before which $modid should be inserted

/// Remove original module from original section

    if (! delete_mod_from_section($mod->id, $mod->section)) {
        notify("Could not delete module from existing section");
    }

/// Update module itself if necessary

    if ($mod->section != $section->id) {
        $mod->section = $section->id;
        if (!update_record("course_modules", $mod)) {
            return false;
        }
        // if moving to a hidden section then hide module
        if (!$section->visible) {
            set_coursemodule_visible($mod->id, 0);
        }
    }

/// Add the module into the new section

    $mod->course       = $section->course;
    $mod->section      = $section->section;  // need relative reference
    $mod->coursemodule = $mod->id;

    if (! add_mod_to_section($mod, $beforemod)) {
        return false;
    }

    return true;

}

function make_editing_buttons($mod, $absolute=false, $moveselect=true, $indent=-1, $section=-1) {
    global $CFG, $USER;

    static $str;
    static $sesskey;

    if (!isset($str)) {
        $str->delete    = get_string("delete");
        $str->move      = get_string("move");
        $str->moveup    = get_string("moveup");
        $str->movedown  = get_string("movedown");
        $str->moveright = get_string("moveright");
        $str->moveleft  = get_string("moveleft");
        $str->update    = get_string("update");
        $str->duplicate    = get_string("duplicate");
        $str->hide      = get_string("hide");
        $str->show      = get_string("show");
        $str->clicktochange  = get_string("clicktochange");
        $str->forcedmode     = get_string("forcedmode");
        $str->groupsnone     = get_string("groupsnone");
        $str->groupsseparate = get_string("groupsseparate");
        $str->groupsvisible  = get_string("groupsvisible");
        $sesskey = sesskey();
    }

    if ($section >= 0) {
        $section = '&amp;sr='.$section;   // Section return
    } else {
        $section = '';
    }

    if ($absolute) {
        $path = $CFG->wwwroot.'/course';
    } else {
        $path = '.';
    }

    if ($mod->visible) {
        $hideshow = '<a title="'.$str->hide.'" href="'.$path.'/mod.php?hide='.$mod->id.
                    '&amp;sesskey='.$sesskey.$section.'"><img'.
                    ' src="'.$CFG->pixpath.'/t/hide.gif" hspace="2" height="11" width="11" '.
                    ' border="0" alt="'.$str->hide.'" /></a> ';
    } else {
        $hideshow = '<a title="'.$str->show.'" href="'.$path.'/mod.php?show='.$mod->id.
                    '&amp;sesskey='.$sesskey.$section.'"><img'.
                    ' src="'.$CFG->pixpath.'/t/show.gif" hspace="2" height="11" width="11" '.
                    ' border="0" alt="'.$str->show.'" /></a> ';
    }
    if ($mod->groupmode !== false) {
        if ($mod->groupmode == SEPARATEGROUPS) {
            $grouptitle = $str->groupsseparate;
            $groupimage = $CFG->pixpath.'/t/groups.gif';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=0&amp;sesskey='.$sesskey;
        } else if ($mod->groupmode == VISIBLEGROUPS) {
            $grouptitle = $str->groupsvisible;
            $groupimage = $CFG->pixpath.'/t/groupv.gif';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=1&amp;sesskey='.$sesskey;
        } else {
            $grouptitle = $str->groupsnone;
            $groupimage = $CFG->pixpath.'/t/groupn.gif';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=2&amp;sesskey='.$sesskey;
        }
        if ($mod->groupmodelink) {
            $groupmode = '<a title="'.$grouptitle.' ('.$str->clicktochange.')" href="'.$grouplink.'">'.
                         '<img src="'.$groupimage.'" hspace="2" height="11" width="11" '.
                         'border="0" alt="'.$grouptitle.'" /></a>';
        } else {
            $groupmode = '<img title="'.$grouptitle.' ('.$str->forcedmode.')" '.
                         ' src="'.$groupimage.'" hspace="2" height="11" width="11" '.
                         'border="0" alt="'.$grouptitle.'" />';
        }
    } else {
        $groupmode = "";
    }

    if ($moveselect) {
        $move =     '<a title="'.$str->move.'" href="'.$path.'/mod.php?copy='.$mod->id.
                    '&amp;sesskey='.$sesskey.$section.'"><img'.
                    ' src="'.$CFG->pixpath.'/t/move.gif" hspace="2" height="11" width="11" '.
                    ' border="0" alt="'.$str->move.'" /></a>';
    } else {
        $move =     '<a title="'.$str->moveup.'" href="'.$path.'/mod.php?id='.$mod->id.
                    '&amp;move=-1&amp;sesskey='.$sesskey.$section.'"><img'.
                    ' src="'.$CFG->pixpath.'/t/up.gif" hspace="2" height="11" width="11" '.
                    ' border="0" alt="'.$str->moveup.'" /></a>'.
                    '<a title="'.$str->movedown.'" href="'.$path.'/mod.php?id='.$mod->id.
                    '&amp;move=1&amp;sesskey='.$sesskey.$section.'"><img'.
                    ' src="'.$CFG->pixpath.'/t/down.gif" hspace="2" height="11" width="11" '.
                    ' border="0" alt="'.$str->movedown.'" /></a>';
    }

    $leftright = "";
    if ($indent > 0) {
        $leftright .= '<a title="'.$str->moveleft.'" href="'.$path.'/mod.php?id='.$mod->id.
                      '&amp;indent=-1&amp;sesskey='.$sesskey.$section.'"><img'.
                      ' src="'.$CFG->pixpath.'/t/left.gif" hspace="2" height="11" width="11" '.
                      ' border="0" alt="'.$str->moveleft.'" /></a>';
    }
    if ($indent >= 0) {
        $leftright .= '<a title="'.$str->moveright.'" href="'.$path.'/mod.php?id='.$mod->id.
                      '&amp;indent=1&amp;sesskey='.$sesskey.$section.'"><img'.
                      ' src="'.$CFG->pixpath.'/t/right.gif" hspace="2" height="11" width="11" '.
                      ' border="0" alt="'.$str->moveright.'" /></a>';
    }

    return '<span class="commands">'.$leftright.$move.
           '<a title="'.$str->update.'" href="'.$path.'/mod.php?update='.$mod->id.
           '&amp;sesskey='.$sesskey.$section.'"><img'.
           ' src="'.$CFG->pixpath.'/t/edit.gif" hspace="2" height="11" width="11" border="0" '.
           ' alt="'.$str->update.'" /></a>'.
           '<a title="'.$str->delete.'" href="'.$path.'/mod.php?delete='.$mod->id.
           '&amp;sesskey='.$sesskey.$section.'"><img'.
           ' src="'.$CFG->pixpath.'/t/delete.gif" hspace="2" height="11" width="11" border="0" '.
           ' alt="'.$str->delete.'" /></a>'.$hideshow.$groupmode.'</span>';
}

/**
 * given a course object with shortname & fullname, this function will 
 * truncate the the number of chars allowed and add ... if it was too long
 */
function course_format_name ($course,$max=100) {
    
    $str = $course->shortname.': '.$course->fullname;
    if (strlen($str) <= $max) {
        return $str;
    }
    else {
        return substr($str,0,$max-3).'...';
    }
}

/**
 * This function will return true if the given course is a child course at all
 */
function course_in_meta ($course) {
    return record_exists("course_meta","child_course",$course->id);
}


/**
 * Print standard form elements on module setup forms in mod/.../mod.html
 */
function print_standard_coursemodule_settings($form) {
    if (! $course = get_record('course', 'id', $form->course)) {
        error("This course doesn't exist");
    }
    print_groupmode_setting($form, $course);
    print_visible_setting($form, $course);
}

/**
 * Print groupmode form element on module setup forms in mod/.../mod.html
 */
function print_groupmode_setting($form, $course=NULL) {

    if (empty($course)) {
        if (! $course = get_record('course', 'id', $form->course)) {
            error("This course doesn't exist");
        }
    }
    if ($form->coursemodule) {
        if (! $cm = get_record('course_modules', 'id', $form->coursemodule)) {
            error("This course module doesn't exist");
        }
    } else {
        $cm = null;
    }
    $groupmode = groupmode($course, $cm);
    if ($course->groupmode or (!$course->groupmodeforce)) {
        echo '<tr valign="top">';
        echo '<td align="right"><b>'.get_string('groupmode').':</b></td>';
        echo '<td align="left">';
        unset($choices);
        $choices[NOGROUPS] = get_string('groupsnone');
        $choices[SEPARATEGROUPS] = get_string('groupsseparate');
        $choices[VISIBLEGROUPS] = get_string('groupsvisible');
        choose_from_menu($choices, 'groupmode', $groupmode, '', '', 0, false, $course->groupmodeforce);
        helpbutton('groupmode', get_string('groupmode'));
        echo '</td></tr>';
    }
}

/**
 * Print visibility setting form element on module setup forms in mod/.../mod.html
 */
function print_visible_setting($form, $course=NULL) {
    if (empty($course)) {
        if (! $course = get_record('course', 'id', $form->course)) {
            error("This course doesn't exist");
        }
    }
    if ($form->coursemodule) {
        $visible = get_field('course_modules', 'visible', 'id', $form->coursemodule);
    } else {
        $visible = true;
    }

    if ($form->mode == 'add') { // in this case $form->section is the section number, not the id
        $hiddensection = !get_field('course_sections', 'visible', 'section', $form->section, 'course', $form->course);
    } else {
        $hiddensection = !get_field('course_sections', 'visible', 'id', $form->section);
    }
    if ($hiddensection) {
        $visible = false;
    }
    
    echo '<tr valign="top">';
    echo '<td align="right"><b>'.get_string('visibletostudents','',moodle_strtolower($course->students)).':</b></td>';
    echo '<td align="left">';
    unset($choices);
    $choices[1] = get_string('show');
    $choices[0] = get_string('hide');
    choose_from_menu($choices, 'visible', $visible, '', '', 0, false, $hiddensection);
    echo '</td></tr>';
} 

function update_restricted_mods($course,$mods) {
    delete_records("course_allowed_modules","course",$course->id);
    if (empty($course->restrictmodules)) {
        return;
    }
    else {
        foreach ($mods as $mod) {
            if ($mod == 0)
                continue; // this is the 'allow none' option
            $am->course = $course->id;
            $am->module = $mod;
            insert_record("course_allowed_modules",$am);
        }
    }
}

/**
 * This function will take an int (module id) or a string (module name)
 * and return true or false, whether it's allowed in the given course (object)
 * $mod is not allowed to be an object, as the field for the module id is inconsistent 
 * depending on where in the code it's called from (sometimes $mod->id, sometimes $mod->module)
 */

function course_allowed_module($course,$mod) {
    if (empty($course->restrictmodules)) {
        return true;
    }
    if (isadmin()) {
        return true;
    }
    if (is_numeric($mod)) {
        $modid = $mod;
    } else if (is_string($mod)) {
        if ($mod = get_field("modules","id","name",strtolower($mod)))
            $modid = $mod;
    }
    if (empty($modid)) {
        return false;
    }
    return (record_exists("course_allowed_modules","course",$course->id,"module",$modid));
}

?>
