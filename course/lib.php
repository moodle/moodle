<?php  // $Id$
   // Library of useful functions


if (defined('COURSE_MAX_LOG_DISPLAY')) {  // Being included again - should never happen!!
    return;
}

define('COURSE_MAX_LOG_DISPLAY', 150);          // days
define('COURSE_MAX_LOGS_PER_PAGE', 1000);       // records
define('COURSE_LIVELOG_REFRESH', 60);           // Seconds
define('COURSE_MAX_RECENT_PERIOD', 172800);     // Two days, in seconds
define('COURSE_MAX_SUMMARIES_PER_PAGE', 10);    // courses
define('COURSE_MAX_COURSES_PER_DROPDOWN',1000); //  max courses in log dropdown before switching to optional
define('COURSE_MAX_USERS_PER_DROPDOWN',1000);   //  max users in log dropdown before switching to optional
define('FRONTPAGENEWS', 0);
define('FRONTPAGECOURSELIST',     1);
define('FRONTPAGECATEGORYNAMES',  2);
define('FRONTPAGETOPICONLY',      3);
define('FRONTPAGECATEGORYCOMBO',  4);
define('FRONTPAGECOURSELIMIT',    200);         // maximum number of courses displayed on the frontpage
define('EXCELROWS', 65535);
define('FIRSTUSEDEXCELROW', 3);

define('MOD_CLASS_ACTIVITY', 0);
define('MOD_CLASS_RESOURCE', 1);


function print_recent_selector_form($course, $advancedfilter=0, $selecteduser=0, $selecteddate="lastlogin",
                                    $mod="", $modid="activity/All", $modaction="", $selectedgroup="", $selectedsort="default") {

    global $USER, $CFG;

    if ($advancedfilter) {

        // Get all the possible users
        $users = array();

        if ($courseusers = get_course_users($course->id, '', '', 'u.id, u.firstname, u.lastname')) {
            foreach ($courseusers as $courseuser) {
                $users[$courseuser->id] = fullname($courseuser, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
            }
        }
        if ($guest = get_guest()) {
            $users[$guest->id] = fullname($guest);
        }

        if (has_capability('moodle/course:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
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

    /// Casting $course->modinfo to string prevents one notice when the field is null
        if ($modinfo = unserialize((string)$course->modinfo)) {
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
                if (!$mod->visible and !has_capability('moodle/course:viewhiddenactivities',get_context_instance(CONTEXT_MODULE, $mod->cm))) {
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

        if (has_capability('moodle/course:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
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

        if ($groupmode == VISIBLEGROUPS or ($groupmode and has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_COURSE, $course->id)))) {
            if ($groups_names = groups_get_groups_names($course->id)) { //TODO:check.
            echo '<td><b>';
                if ($groupmode == VISIBLEGROUPS) {
                    print_string('groupsvisible');
                } else {
                    print_string('groupsseparate');
                }
                echo ':</b></td><td>';
                choose_from_menu($groups_names, "selectedgroup", $selectedgroup, get_string("allgroups"), "", "");
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


function make_log_url($module, $url) {
    switch ($module) {
        case 'user':
        case 'course':
        case 'file':
        case 'login':
        case 'lib':
        case 'admin':
        case 'message':
        case 'calendar':
        case 'blog':
            return "/$module/$url";
            break;
        case 'mnet course':
            return "/course/$url";
            break;
        case 'upload':
            return $url;
            break;
        case 'library':
        case '':
            return '/';
            break;
        default:
            return "/mod/$module/$url";
            break;
    }
}


function build_mnet_logs_array($hostid, $course, $user=0, $date=0, $order="l.time ASC", $limitfrom='', $limitnum='',
                   $modname="", $modid=0, $modaction="", $groupid=0) {

    global $CFG;

    // It is assumed that $date is the GMT time of midnight for that day,
    // and so the next 86400 seconds worth of logs are printed.

    /// Setup for group handling.
    
    // TODO: I don't understand group/context/etc. enough to be able to do 
    // something interesting with it here
    // What is the context of a remote course?
    
    /// If the group mode is separate, and this user does not have editing privileges,
    /// then only the user's group can be viewed.
    //if ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_COURSE, $course->id))) {
    //    $groupid = get_current_group($course->id);
    //}
    /// If this course doesn't have groups, no groupid can be specified.
    //else if (!$course->groupmode) {
    //    $groupid = 0;
    //}
    $groupid = 0;

    $joins = array();

    $qry = "
            SELECT
                l.*,
                u.firstname, 
                u.lastname, 
                u.picture
            FROM
                {$CFG->prefix}mnet_log l
            LEFT JOIN  
                {$CFG->prefix}user u
            ON 
                l.userid = u.id
            WHERE
                ";

    $where .= "l.hostid = '$hostid'";

    // TODO: Is 1 really a magic number referring to the sitename?
    if ($course != 1 || $modid != 0) {
        $where .= " AND\n                l.course='$course'";
    }

    if ($modname) {
        $where .= " AND\n                l.module = '$modname'";
    }

    if ('site_errors' === $modid) {
        $where .= " AND\n                ( l.action='error' OR l.action='infected' )";
    } else if ($modid) {
        //TODO: This assumes that modids are the same across sites... probably 
        //not true
        $where .= " AND\n                l.cmid = '$modid'";
    }

    if ($modaction) {
        $firstletter = substr($modaction, 0, 1);
        if (ctype_alpha($firstletter)) {
            $where .= " AND\n                lower(l.action) LIKE '%" . strtolower($modaction) . "%'";
        } else if ($firstletter == '-') {
            $where .= " AND\n                lower(l.action) NOT LIKE '%" . strtolower(substr($modaction, 1)) . "%'";
        }
    }

    if ($user) {
        $where .= " AND\n                l.userid = '$user'";
    }

    if ($date) {
        $enddate = $date + 86400;
        $where .= " AND\n                l.time > '$date' AND l.time < '$enddate'";
    }

    $result = array();
    $result['totalcount'] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}mnet_log l WHERE $where");
    if(!empty($result['totalcount'])) {
        $where .= "\n            ORDER BY\n                $order";
        $result['logs'] = get_records_sql($qry.$where, $limitfrom, $limitnum);
    } else {
        $result['logs'] = array();
    }
    return $result;
}

function build_logs_array($course, $user=0, $date=0, $order="l.time ASC", $limitfrom='', $limitnum='',
                   $modname="", $modid=0, $modaction="", $groupid=0) {

    // It is assumed that $date is the GMT time of midnight for that day,
    // and so the next 86400 seconds worth of logs are printed.

    /// Setup for group handling.

    /// If the group mode is separate, and this user does not have editing privileges,
    /// then only the user's group can be viewed.
    if ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_COURSE, $course->id))) {
        $groupid = get_current_group($course->id);
    }
    /// If this course doesn't have groups, no groupid can be specified.
    else if (!$course->groupmode) {
        $groupid = 0;
    }

    $joins = array();

    if ($course->id != SITEID || $modid != 0) {
        $joins[] = "l.course='$course->id'";
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
        $firstletter = substr($modaction, 0, 1);
        if (ctype_alpha($firstletter)) {
            $joins[] = "lower(l.action) LIKE '%" . strtolower($modaction) . "%'";
        } else if ($firstletter == '-') {
            $joins[] = "lower(l.action) NOT LIKE '%" . strtolower(substr($modaction, 1)) . "%'";
        }
    }

    /// Getting all members of a group.
    if ($groupid and !$user) {
        $gusers = groups_get_members($groupid);
        if (!empty($gusers)) {
            $joins[] = 'l.userid IN (' . implode(',', $gusers) . ')';
        } else {
            $joins[] = 'l.userid = 0'; // No users in groups, so we want something that will always by false.
        }
    }
    else if ($user) {
        $joins[] = "l.userid = '$user'";
    }

    if ($date) {
        $enddate = $date + 86400;
        $joins[] = "l.time > '$date' AND l.time < '$enddate'";
    }

    $selector = implode(' AND ', $joins);

    $totalcount = 0;  // Initialise
    $result = array();
    $result['logs'] = get_logs($selector, $order, $limitfrom, $limitnum, $totalcount);
    $result['totalcount'] = $totalcount;
    return $result;
}


function print_log($course, $user=0, $date=0, $order="l.time ASC", $page=0, $perpage=100,
                   $url="", $modname="", $modid=0, $modaction="", $groupid=0) {

    global $CFG;

    if (!$logs = build_logs_array($course, $user, $date, $order, $page*$perpage, $perpage,
                       $modname, $modid, $modaction, $groupid)) {
        notify("No logs found!");
        print_footer($course);
        exit;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $totalcount = $logs['totalcount'];
    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    echo "<div class=\"info\">\n";
    print_string("displayingrecords", "", $totalcount);
    echo "</div>\n";

    print_paging_bar($totalcount, $page, $perpage, "$url&amp;perpage=$perpage&amp;");

    echo '<table class="logtable genearlbox boxaligncenter" summary="">'."\n";
    // echo "<table class=\"logtable\" cellpadding=\"3\" cellspacing=\"0\" summary=\"\">\n";
    echo "<tr>";
    if ($course->id == SITEID) {
        echo "<th class=\"c0 header\" scope=\"col\">".get_string('course')."</th>\n";
    }
    echo "<th class=\"c1 header\" scope=\"col\">".get_string('time')."</th>\n";
    echo "<th class=\"c2 header\" scope=\"col\">".get_string('ip_address')."</th>\n";
    echo "<th class=\"c3 header\" scope=\"col\">".get_string('fullname')."</th>\n";
    echo "<th class=\"c4 header\" scope=\"col\">".get_string('action')."</th>\n";
    echo "<th class=\"c5 header\" scope=\"col\">".get_string('info')."</th>\n";
    echo "</tr>\n";

    // Make sure that the logs array is an array, even it is empty, to avoid warnings from the foreach.
    if (empty($logs['logs'])) {
        $logs['logs'] = array();
    }
    
    $row = 1;
    foreach ($logs['logs'] as $log) {

        $row = ($row + 1) % 2;

        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = get_record('log_display', 'module', $log->module, 'action', $log->action);
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && is_numeric($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == sql_concat('firstname', "' '" , 'lastname'))) {
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
            echo "<td class=\"cell c0\">\n";
            if (empty($log->course)) {
                echo format_string($log->info)."\n";
            } else {
                echo "    <a href=\"{$CFG->wwwroot}/course/view.php?id={$log->course}\">". format_string($courses[$log->course])."</a>\n";
            }
            echo "</td>\n";
        }
        echo "<td class=\"cell c1\" align=\"right\">".userdate($log->time, '%a').
             ' '.userdate($log->time, $strftimedatetime)."</td>\n";
        echo "<td class=\"cell c2\">\n";
        link_to_popup_window("/iplookup/index.php?ip=$log->ip&amp;user=$log->userid", 'iplookup',$log->ip, 400, 700);
        echo "</td>\n";
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        echo "<td class=\"cell c3\">\n";
        echo "    <a href=\"$CFG->wwwroot/user/view.php?id={$log->userid}&amp;course={$log->course}\">$fullname</a>\n";
        echo "</td>\n";
        echo "<td class=\"cell c4\">\n";
        link_to_popup_window( make_log_url($log->module,$log->url), 'fromloglive',"$log->module $log->action", 400, 600);
        echo "</td>\n";;
        echo "<td class=\"cell c5\">{$log->info}</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";

    print_paging_bar($totalcount, $page, $perpage, "$url&amp;perpage=$perpage&amp;");
}


function print_mnet_log($hostid, $course, $user=0, $date=0, $order="l.time ASC", $page=0, $perpage=100,
                   $url="", $modname="", $modid=0, $modaction="", $groupid=0) {
    
    global $CFG;
    
    if (!$logs = build_mnet_logs_array($hostid, $course, $user, $date, $order, $page*$perpage, $perpage,
                       $modname, $modid, $modaction, $groupid)) {
        notify("No logs found!");
        print_footer($course);
        exit;
    }
    
    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname,c.visible')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    }
    
    $totalcount = $logs['totalcount'];
    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    echo "<div class=\"info\">\n";
    print_string("displayingrecords", "", $totalcount);
    echo "</div>\n";

    print_paging_bar($totalcount, $page, $perpage, "$url&amp;perpage=$perpage&amp;");

    echo "<table class=\"logtable\" cellpadding=\"3\" cellspacing=\"0\">\n";
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

    if (empty($logs['logs'])) {
        echo "</table>\n";
        return;
    }

    $row = 1;
    foreach ($logs['logs'] as $log) {
        
        $log->info = $log->coursename;
        $row = ($row + 1) % 2;

        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = get_record('log_display', 'module', $log->module, 'action', $log->action);
            $ldcache[$log->module][$log->action] = $ld;
        }
        if (0 && $ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == sql_concat('firstname', "' '" , 'lastname'))) {
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
            echo "<td class=\"r$row c0\" >\n";
            echo "    <a href=\"{$CFG->wwwroot}/course/view.php?id={$log->course}\">".$courses[$log->course]."</a>\n";
            echo "</td>\n";
        }
        echo "<td class=\"r$row c1\" align=\"right\">".userdate($log->time, '%a').
             ' '.userdate($log->time, $strftimedatetime)."</td>\n";
        echo "<td class=\"r$row c2\" >\n";
        link_to_popup_window("/iplookup/index.php?ip=$log->ip&amp;user=$log->userid", 'iplookup',$log->ip, 400, 700);
        echo "</td>\n";
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        echo "<td class=\"r$row c3\" >\n";
        echo "    <a href=\"$CFG->wwwroot/user/view.php?id={$log->userid}\">$fullname</a>\n";
        echo "</td>\n";
        echo "<td class=\"r$row c4\">\n";
        echo $log->action .': '.$log->module;
        echo "</td>\n";;
        echo "<td class=\"r$row c5\">{$log->info}</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";

    print_paging_bar($totalcount, $page, $perpage, "$url&amp;perpage=$perpage&amp;");
}


function print_log_csv($course, $user, $date, $order='l.time DESC', $modname,
                        $modid, $modaction, $groupid) {

    $text = get_string('course')."\t".get_string('time')."\t".get_string('ip_address')."\t".
            get_string('fullname')."\t".get_string('action')."\t".get_string('info');

    if (!$logs = build_logs_array($course, $user, $date, $order, '', '',
                       $modname, $modid, $modaction, $groupid)) {
        return false;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    $filename = 'logs_'.userdate(time(),get_string('backupnameformat'),99,false);
    $filename .= '.txt';
    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    echo get_string('savedat').userdate(time(), $strftimedatetime)."\n";
    echo $text;

    if (empty($logs['logs'])) {
        return true;
    }

    foreach ($logs['logs'] as $log) {
        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = get_record('log_display', 'module', $log->module, 'action', $log->action);
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field ==  sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname(get_record($ld->mtable, 'id', $log->info), true);
            } else {
                $log->info = get_field($ld->mtable, $ld->field, 'id', $log->info);
            }
        }

        //Filter log->info
        $log->info = format_string($log->info);

        $log->url  = strip_tags(urldecode($log->url));     // Some XSS protection
        $log->info = strip_tags(urldecode($log->info));    // Some XSS protection
        $log->url  = str_replace('&', '&amp;', $log->url); // XHTML compatibility

        $firstField = $courses[$log->course];
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        $row = array($firstField, userdate($log->time, $strftimedatetime), $log->ip, $fullname, $log->module.' '.$log->action, $log->info);
        $text = implode("\t", $row);
        echo $text." \n";
    }
    return true;
}


function print_log_xls($course, $user, $date, $order='l.time DESC', $modname,
                        $modid, $modaction, $groupid) {

    global $CFG;

    require_once("$CFG->libdir/excellib.class.php");

    if (!$logs = build_logs_array($course, $user, $date, $order, '', '',
                       $modname, $modid, $modaction, $groupid)) {
        return false;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    $nroPages = ceil(count($logs)/(EXCELROWS-FIRSTUSEDEXCELROW+1));
    $filename = 'logs_'.userdate(time(),get_string('backupnameformat'),99,false);
    $filename .= '.xls';

    $workbook = new MoodleExcelWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();
    $headers = array(get_string('course'), get_string('time'), get_string('ip_address'),
                        get_string('fullname'),    get_string('action'), get_string('info'));

    // Creating worksheets
    for ($wsnumber = 1; $wsnumber <= $nroPages; $wsnumber++) {
        $sheettitle = get_string('excel_sheettitle', 'logs', $wsnumber).$nroPages;
        $worksheet[$wsnumber] =& $workbook->add_worksheet($sheettitle);
        $worksheet[$wsnumber]->set_column(1, 1, 30);
        $worksheet[$wsnumber]->write_string(0, 0, get_string('savedat').
                                    userdate(time(), $strftimedatetime));
        $col = 0;
        foreach ($headers as $item) {
            $worksheet[$wsnumber]->write(FIRSTUSEDEXCELROW-1,$col,$item,'');
            $col++;
        }
    }

    if (empty($logs['logs'])) {
        $workbook->close();
        return true;
    }

    $formatDate =& $workbook->add_format();
    $formatDate->set_num_format(get_string('log_excel_date_format'));

    $row = FIRSTUSEDEXCELROW;
    $wsnumber = 1;
    $myxls =& $worksheet[$wsnumber];
    foreach ($logs['logs'] as $log) {
        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = get_record('log_display', 'module', $log->module, 'action', $log->action);
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname(get_record($ld->mtable, 'id', $log->info), true);
            } else {
                $log->info = get_field($ld->mtable, $ld->field, 'id', $log->info);
            }
        }

        // Filter log->info
        $log->info = format_string($log->info);
        $log->info = strip_tags(urldecode($log->info));  // Some XSS protection

        if ($nroPages>1) {
            if ($row > EXCELROWS) {
                $wsnumber++;
                $myxls =& $worksheet[$wsnumber];
                $row = FIRSTUSEDEXCELROW;
            }
        }

        $myxls->write($row, 0, $courses[$log->course], '');
        // Excel counts from 1/1/1900
        $excelTime=25569+$log->time/(3600*24);
        $myxls->write($row, 1, $excelTime, $formatDate);
        $myxls->write($row, 2, $log->ip, '');
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        $myxls->write($row, 3, $fullname, '');
        $myxls->write($row, 4, $log->module.' '.$log->action, '');
        $myxls->write($row, 5, $log->info, '');

        $row++;
    }

    $workbook->close();
    return true;
}

function print_log_ods($course, $user, $date, $order='l.time DESC', $modname,
                        $modid, $modaction, $groupid) {

    global $CFG;

    require_once("$CFG->libdir/odslib.class.php");

    if (!$logs = build_logs_array($course, $user, $date, $order, '', '',
                       $modname, $modid, $modaction, $groupid)) {
        return false;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    $nroPages = ceil(count($logs)/(EXCELROWS-FIRSTUSEDEXCELROW+1));
    $filename = 'logs_'.userdate(time(),get_string('backupnameformat'),99,false);
    $filename .= '.ods';

    $workbook = new MoodleODSWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();
    $headers = array(get_string('course'), get_string('time'), get_string('ip_address'),
                        get_string('fullname'),    get_string('action'), get_string('info'));

    // Creating worksheets
    for ($wsnumber = 1; $wsnumber <= $nroPages; $wsnumber++) {
        $sheettitle = get_string('excel_sheettitle', 'logs', $wsnumber).$nroPages;
        $worksheet[$wsnumber] =& $workbook->add_worksheet($sheettitle);
        $worksheet[$wsnumber]->set_column(1, 1, 30);
        $worksheet[$wsnumber]->write_string(0, 0, get_string('savedat').
                                    userdate(time(), $strftimedatetime));
        $col = 0;
        foreach ($headers as $item) {
            $worksheet[$wsnumber]->write(FIRSTUSEDEXCELROW-1,$col,$item,'');
            $col++;
        }
    }

    if (empty($logs['logs'])) {
        $workbook->close();
        return true;
    }

    $formatDate =& $workbook->add_format();
    $formatDate->set_num_format(get_string('log_excel_date_format'));

    $row = FIRSTUSEDEXCELROW;
    $wsnumber = 1;
    $myxls =& $worksheet[$wsnumber];
    foreach ($logs['logs'] as $log) {
        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = get_record('log_display', 'module', $log->module, 'action', $log->action);
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname(get_record($ld->mtable, 'id', $log->info), true);
            } else {
                $log->info = get_field($ld->mtable, $ld->field, 'id', $log->info);
            }
        }

        // Filter log->info
        $log->info = format_string($log->info);
        $log->info = strip_tags(urldecode($log->info));  // Some XSS protection

        if ($nroPages>1) {
            if ($row > EXCELROWS) {
                $wsnumber++;
                $myxls =& $worksheet[$wsnumber];
                $row = FIRSTUSEDEXCELROW;
            }
        }

        $myxls->write_string($row, 0, $courses[$log->course]);
        $myxls->write_date($row, 1, $log->time);
        $myxls->write_string($row, 2, $log->ip);
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        $myxls->write_string($row, 3, $fullname);
        $myxls->write_string($row, 4, $log->module.' '.$log->action);
        $myxls->write_string($row, 5, $log->info);

        $row++;
    }

    $workbook->close();
    return true;
}


function print_log_graph($course, $userid=0, $type="course.png", $date=0) {
    global $CFG;
    if (empty($CFG->gdversion)) {
        echo "(".get_string("gdneed").")";
    } else {
        echo '<img src="'.$CFG->wwwroot.'/course/report/log/graph.php?id='.$course->id.
             '&amp;user='.$userid.'&amp;type='.$type.'&amp;date='.$date.'" alt="" />';
    }
}


function print_overview($courses) {

    global $CFG, $USER;

    $htmlarray = array();
    if ($modules = get_records('modules')) {
        foreach ($modules as $mod) {
            if (file_exists(dirname(dirname(__FILE__)).'/mod/'.$mod->name.'/lib.php')) {
                require_once(dirname(dirname(__FILE__)).'/mod/'.$mod->name.'/lib.php');
                $fname = $mod->name.'_print_overview';
                if (function_exists($fname)) {
                    $fname($courses,$htmlarray);
                }
            }
        }
    }
    foreach ($courses as $course) {
        print_simple_box_start('center', '100%', '', 5, "coursebox");
        $linkcss = '';
        if (empty($course->visible)) {
            $linkcss = 'class="dimmed"';
        }
        print_heading('<a title="'. format_string($course->fullname).'" '.$linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'. format_string($course->fullname).'</a>');
        if (array_key_exists($course->id,$htmlarray)) {
            foreach ($htmlarray[$course->id] as $modname => $html) {
                echo $html;
            }
        }
        print_simple_box_end();
    }
}


function print_recent_activity($course) {
    // $course is an object
    // This function trawls through the logs looking for
    // anything new since the user's last login

    global $CFG, $USER, $SESSION;

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $timestart = time() - COURSE_MAX_RECENT_PERIOD;

    if (!has_capability('moodle/legacy:guest', $context, NULL, false)) {
        if (!empty($USER->lastcourseaccess[$course->id])) {
            if ($USER->lastcourseaccess[$course->id] > $timestart) {
                $timestart = $USER->lastcourseaccess[$course->id];
            }
        }
    }

    echo '<div class="activitydate">';
    echo get_string('activitysince', '', userdate($timestart));
    echo '</div>';
    echo '<div class="activityhead">';

    echo '<a href="'.$CFG->wwwroot.'/course/recent.php?id='.$course->id.'">'.get_string('recentactivityreport').'</a>';

    echo "</div>\n";


    // Firstly, have there been any new enrolments?

    $heading = false;
    $content = false;

    $users = get_recent_enrolments($course->id, $timestart);

    //Accessibility: new users now appear in an <OL> list.
    if ($users) {
        echo '<div class="newusers">';
        if (! $heading) {
            print_headline(get_string("newusers").':', 3);
            $heading = true;
            $content = true;
        }
        echo "<ol class=\"list\">\n";
        foreach ($users as $user) {

            $fullname = fullname($user, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
            echo '<li class="name"><a href="'.$CFG->wwwroot."/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a></li>\n";
        }
        echo "</ol>\n</div>\n";
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

    $viewfullnames = has_capability('moodle/site:viewfullnames', $context);

    foreach ($mods as $mod) {      // Each module gets it's own logs and prints them
        include_once($CFG->dirroot.'/mod/'.$mod->name.'/lib.php');
        $print_recent_activity = $mod->name.'_print_recent_activity';
        if (function_exists($print_recent_activity)) {
            //
            // NOTE:
            //   $isteacher (second parameter below) is to be deprecated!
            //
            // TODO:
            //   1) Make sure that all _print_recent_activity functions are
            //      not using the $isteacher value.
            //   2) Eventually, remove the $isteacher parameter from the
            //      function calls.
            //
            $modcontent = $print_recent_activity($course, $viewfullnames, $timestart);
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
            if ($mod->visible or has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_COURSE, $courseid))) {
                $modnamesused[$mod->modname] = $modnames[$mod->modname];
            }
        }
        if ($modnamesused) {
            asort($modnamesused);
        }
    }
}


function get_all_sections($courseid) {

    return get_records("course_sections", "course", "$courseid", "section",
                       "section, id, course, summary, sequence, visible");
}

function course_set_display($courseid, $display=0) {
    global $USER;

    if ($display == "all" or empty($display)) {
        $display = 0;
    }

    if (empty($USER->id) or $USER->username == 'guest') {
        //do not store settings in db for guests
    } else if (record_exists("course_display", "userid", $USER->id, "course", $courseid)) {
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
                set_coursemodule_visible($moduleid, $visibility, true);
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

/// Casting $course->modinfo to string prevents one notice when the field is null
    $modinfo = unserialize((string)$course->modinfo);

    //Acccessibility: replace table with list <ul>, but don't output empty list.
    if (!empty($section->sequence)) {

        // Fix bug #5027, don't want style=\"width:$width\".
        echo "<ul class=\"section\">\n";
        $sectionmods = explode(",", $section->sequence);

        foreach ($sectionmods as $modnumber) {
            if (empty($mods[$modnumber])) {
                continue;
            }
            $mod = $mods[$modnumber];

            if ($mod->visible or has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_COURSE, $course->id))) {
                echo '<li class="activity '.$mod->modname.'" id="module-'.$modnumber.'">';  // Unique ID
                if ($ismoving) {
                    if ($mod->id == $USER->activitycopy) {
                        echo "</li>\n";
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
                
                    if (!empty($USER->screenreader)) {
                        $typestring = '('.get_string('modulename',$mod->modname).') ';
                    } else {
                        $typestring = '';
                    }
                
                    $linkcss = $mod->visible ? "" : " class=\"dimmed\" ";
                    echo '<img src="'.$icon.'"'.
                         ' class="activityicon" alt="'.$mod->modfullname.'" />'.
                         ' <a title="'.$mod->modfullname.'" '.$linkcss.' '.$extra.
                         ' href="'.$CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id.'">'.
                         $typestring.$instancename.'</a>';
                }
                if ($usetracking && $mod->modname == 'forum') {
                    $groupmode = groupmode($course, $mod);
                    $groupid = ($groupmode == SEPARATEGROUPS && !has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_COURSE, $course->id))) ?
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
                    // TODO: we must define this as mod property!
                    if ($groupbuttons and $mod->modname != 'label' and $mod->modname != 'resource' and $mod->modname != 'glossary') {
                        if (! $mod->groupmodelink = $groupbuttonslink) {
                            $mod->groupmode = $course->groupmode;
                        }

                    } else {
                        $mod->groupmode = false;
                    }
                    echo '&nbsp;&nbsp;';
                    echo make_editing_buttons($mod, $absolute, true, $mod->indent, $section->section);
                }
                echo "</li>\n";
            }
        }
    } elseif ($ismoving) {
        echo "<ul class=\"section\">\n";
    }
    if ($ismoving) {
        echo '<li><a title="'.$strmovefull.'"'.
             ' href="'.$CFG->wwwroot.'/course/mod.php?movetosection='.$section->id.'&amp;sesskey='.$USER->sesskey.'">'.
             '<img class="movetarget" src="'.$CFG->pixpath.'/movehere.gif" '.
             ' alt="'.$strmovehere.'" /></a></li>
             ';
    }
    if (!empty($section->sequence) || $ismoving) {
        echo "</ul><!--class='section'-->\n\n";
    }
}

/**
 * Prints the menus to add activities and resources.
 */
function print_section_add_menus($course, $section, $modnames, $vertical=false, $return=false) {
    global $CFG;

    // check to see if user can add menus
    if (!has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_COURSE, $course->id))) {
        return false;
    }

    static $resources = false;
    static $activities = false;

    if ($resources === false) { 
        $resources = array();
        $activities = array();

        foreach($modnames as $modname=>$modnamestr) {
            if (!course_allowed_module($course, $modname)) {
                continue;
            }

            require_once("$CFG->dirroot/mod/$modname/lib.php");
            $gettypesfunc =  $modname.'_get_types';
            if (function_exists($gettypesfunc)) {
                $types = $gettypesfunc();
                foreach($types as $type) {
                    if ($type->modclass == MOD_CLASS_RESOURCE) {
                        $resources[$type->type] = $type->typestr;
                    } else {
                        $activities[$type->type] = $type->typestr;
                    }
                }
            } else {
                // all mods without type are considered activity
                $activities[$modname] = $modnamestr;
            }
        }
    }

    $straddactivity = get_string('addactivity');
    $straddresource = get_string('addresource');

    $output  = '<div class="section_add_menus">';

    if (!$vertical) {
        $output .= '<div class="horizontal">';
    }

    if (!empty($resources)) {
        $output .= popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$section&amp;sesskey=".sesskey()."&amp;add=",
                              $resources, "ressection$section", "", $straddresource, 'resource/types', $straddresource, true);
    }

    if (!empty($activities)) {
        $output .= ' ';
        $output .= popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$section&amp;sesskey=".sesskey()."&amp;add=",
                    $activities, "section$section", "", $straddactivity, 'mods', $straddactivity, true);
    }

    if (!$vertical) {
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
        @set_time_limit(0);  // this could take a while!   MDL-10954
    }

    if ($courses = get_records_select("course", $select,'','id,fullname')) {
        foreach ($courses as $course) {
            $modinfo = serialize(get_array_of_activities($course->id));
            if (!set_field("course", "modinfo", $modinfo, "id", $course->id)) {
                notify("Could not cache module information for course '" . format_string($course->fullname) . "'!");
            }
        }
    }
}



function make_categories_list(&$list, &$parents, $category=NULL, $path="") {
/// Given an empty array, this function recursively travels the
/// categories, building up a nice list for display.  It also makes
/// an array that list all the parents for each category.

    // initialize the arrays if needed
    if (!is_array($list)) {
        $list = array();
    }
    if (!is_array($parents)) {
        $parents = array();
    }

    if ($category) {
        if ($path) {
            $path = $path.' / '.format_string($category->name);
        } else {
            $path = format_string($category->name);
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


function print_whole_category_list($category=NULL, $displaylist=NULL, $parentslist=NULL, $depth=-1, $files = true) {
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
        if ($category->visible or has_capability('moodle/course:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            print_category_info($category, $depth, $files);
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

            print_whole_category_list($cat, $displaylist, $parentslist, $depth + 1, $files);
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

function print_category_info($category, $depth, $files = false) {
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

    $coursecount = count_records('course') <= FRONTPAGECOURSELIMIT;
    if ($files and $coursecount) {
        $catimage = '<img src="'.$CFG->pixpath.'/i/course.gif" alt="" />';
    } else {
        $catimage = "&nbsp;";
    }

    echo "\n\n".'<table class="categorylist">';

    if ($files and $coursecount) {
        $courses = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.guest,c.cost,c.currency');

        echo '<tr>';

        if ($depth) {
            $indent = $depth*30;
            $rows = count($courses) + 1;
            echo '<td rowspan="'.$rows.'" valign="top" width="'.$indent.'">';
            print_spacer(10, $indent);
            echo '</td>';
        }

        echo '<td valign="top" class="category image">'.$catimage.'</td>';
        echo '<td valign="top" class="category name">';
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/course/category.php?id='.$category->id.'">'. format_string($category->name).'</a>';
        echo '</td>';
        echo '<td class="category info">&nbsp;</td>';
        echo '</tr>';

        if ($courses && !(isset($CFG->max_category_depth)&&($depth>=$CFG->max_category_depth-1))) {
            foreach ($courses as $course) {
                $linkcss = $course->visible ? '' : ' class="dimmed" ';
                echo '<tr><td valign="top">&nbsp;';
                echo '</td><td valign="top" class="course name">';
                echo '<a '.$linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'. format_string($course->fullname).'</a>';
                echo '</td><td align="right" valign="top" class="course info">';
                if ($course->guest ) {
                    echo '<a title="'.$strallowguests.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
                    echo '<img alt="'.$strallowguests.'" src="'.$CFG->pixpath.'/i/guest.gif" /></a>';
                } else {
                    echo '<img alt="" style="width:18px;height:16px;" src="'.$CFG->pixpath.'/spacer.gif" />';
                }
                if ($course->password) {
                    echo '<a title="'.$strrequireskey.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
                    echo '<img alt="'.$strrequireskey.'" src="'.$CFG->pixpath.'/i/key.gif" /></a>';
                } else {
                    echo '<img alt="" style="width:18px;height:16px;" src="'.$CFG->pixpath.'/spacer.gif" />';
                }
                if ($course->summary) {
                    link_to_popup_window ('/course/info.php?id='.$course->id, 'courseinfo',
                                          '<img alt="'.$strsummary.'" src="'.$CFG->pixpath.'/i/info.gif" />',
                                           400, 500, $strsummary);
                } else {
                    echo '<img alt="" style="width:18px;height:16px;" src="'.$CFG->pixpath.'/spacer.gif" />';
                }
                echo '</td></tr>';
            }
        }
    } else {

        echo '<tr>';

        if ($depth) {
            $indent = $depth*20;
            echo '<td valign="top" width="'.$indent.'">';
            print_spacer(10, $indent);
            echo '</td>';
        }

        echo '<td valign="top" class="category name">';
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/course/category.php?id='.$category->id.'">'. format_string($category->name).'</a>';
        echo '</td>';
        echo '<td valign="top" class="category number">';
        if ($category->coursecount) {
            echo $category->coursecount;
        }
        echo '</td></tr>';
    }
    echo '</table>';
}


function print_courses($category, $hidesitecourse = false) {
/// Category is 0 (for all courses) or an object

    global $CFG;

    if (empty($category)) {
        $categories = get_categories(0);  // Parent = 0   ie top-level categories only
        if ($categories != null and count($categories) == 1) {
            $category   = array_shift($categories);
            $courses    = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.category,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.teacher,c.cost,c.currency,c.enrol,c.guest');
        } else {
            $courses    = get_courses('all', 'c.sortorder ASC', 'c.id,c.category,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.teacher,c.cost,c.currency,c.enrol,c.guest');
        }
        unset($categories);
    } else {
        $categories = get_categories($category->id);  // sub categories
        $courses    = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.category,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.teacher,c.cost,c.currency,c.enrol,c.guest');
    }

    if ($courses) {
        foreach ($courses as $course) {
            if ($hidesitecourse and ($course->id == SITEID)) {
                continue;
            }
            print_course($course);
        }
    } else {
        print_heading(get_string("nocoursesyet"));
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
        if (has_capability('moodle/course:create', $context)) {
            $options = array();
            $options['category'] = $category->id;
            echo '<div class="addcoursebutton">';
            print_single_button($CFG->wwwroot.'/course/edit.php', $options, get_string("addnewcourse"));
            echo '</div>';
        }
    }



}


function print_course($course) {

    global $CFG, $USER;

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $linkcss = $course->visible ? '' : ' class="dimmed" ';

    echo '<div class="coursebox">';
    echo '<div class="info">';
    echo '<div class="name"><a title="'.get_string('entercourse').'"'.
         $linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.
         format_string($course->fullname).'</a></div>';   
    
    /// first find all roles that are supposed to be displayed
    if ($managerroles = get_config('', 'coursemanager')) {
        $coursemanagerroles = split(',', $managerroles);
        $roles = get_records_select( 'role', '', 'sortorder' );
        foreach ($roles as $role) {
            if (in_array( $role->id, $coursemanagerroles )) {
                if ($users = get_role_users($role->id, $context, true, '', 'u.lastname ASC', true)) {
                    foreach ($users as $teacher) {
                        $fullname = fullname($teacher, has_capability('moodle/site:viewfullnames', $context)); 
                        $namesarray[] = format_string($role->name).': <a href="'.$CFG->wwwroot.'/user/view.php?id='.
                                    $teacher->id.'&amp;course='.SITEID.'">'.$fullname.'</a>';
                    }
                }          
            }
        }
        
        if (!empty($namesarray)) {
            echo "<ul class=\"teachers\">\n<li>";
            echo implode('</li><li>', $namesarray);
            echo "</li></ul>";
        }
    }
    
    require_once("$CFG->dirroot/enrol/enrol.class.php");
    $enrol = enrolment_factory::factory($course->enrol);
    echo $enrol->get_access_icons($course);

    echo '</div><div class="summary">';
    $options = NULL;
    $options->noclean = true;
    $options->para = false;
    echo format_text($course->summary, FORMAT_MOODLE, $options,  $course->id);
    echo '</div>';
    echo '</div>';
    echo '<div class="clearer"></div>';
}


function print_my_moodle() {
/// Prints custom user information on the home page.
/// Over time this can include all sorts of information

    global $USER, $CFG;

    if (empty($USER->id)) {
        error("It shouldn't be possible to see My Moodle without being logged in.");
    }

    $courses  = get_my_courses($USER->id);
    $rhosts   = array();
    $rcourses = array();
    if (!empty($CFG->mnet_dispatcher_mode) && $CFG->mnet_dispatcher_mode==='strict') {
        $rcourses = get_my_remotecourses($USER->id);
        $rhosts   = get_my_remotehosts();
    }

    if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {

        if (!empty($courses)) {
            foreach ($courses as $course) {
                if ($course->id == SITEID) {
                    continue;
                }
                print_course($course, "100%");
            }
        }

        // MNET
        if (!empty($rcourses)) { 
            // at the IDP, we know of all the remote courses
            foreach ($rcourses as $course) {
                print_remote_course($course, "100%");
            }
        } elseif (!empty($rhosts)) {
            // non-IDP, we know of all the remote servers, but not courses
            foreach ($rhosts as $host) {
                print_remote_host($host, "100%");
            }
        }
        unset($course);
        unset($host);

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
            print_courses(0);
        }
    }
}


function print_course_search($value="", $return=false, $format="plain") {

    global $CFG;
    static $count = 0;

    $count++;

    $id = 'coursesearch';

    if ($count > 1) {
        $id .= $count;
    }

    $strsearchcourses= get_string("searchcourses");

    if ($format == 'plain') {
        $output  = '<form id="'.$id.'" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<fieldset class="coursesearchbox invisiblefieldset">';
        $output .= '<label for="coursesearchbox">'.$strsearchcourses.': </label>';
        $output .= '<input type="text" id="coursesearchbox" size="30" name="search" alt="'.s($strsearchcourses).'" value="'.s($value, true).'" />';
        $output .= '<input type="submit" value="'.get_string('go').'" />';
        $output .= '</fieldset></form>';
    } else if ($format == 'short') {
        $output  = '<form id="'.$id.'" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<fieldset class="coursesearchbox invisiblefieldset">';
        $output .= '<label for="coursesearchbox">'.$strsearchcourses.': </label>';
        $output .= '<input type="text" id="coursesearchbox" size="12" name="search" alt="'.s($strsearchcourses).'" value="'.s($value, true).'" />';
        $output .= '<input type="submit" value="'.get_string('go').'" />';
        $output .= '</fieldset></form>';
    } else if ($format == 'navbar') {
        $output  = '<form id="coursesearchnavbar" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<fieldset class="coursesearchbox invisiblefieldset">';
        $output .= '<label for="coursesearchbox">'.$strsearchcourses.': </label>';
        $output .= '<input type="text" id="coursesearchbox" size="20" name="search" alt="'.s($strsearchcourses).'" value="'.s($value, true).'" />';
        $output .= '<input type="submit" value="'.get_string('go').'" />';
        $output .= '</fieldset></form>';
    }

    if ($return) {
        return $output;
    }
    echo $output;
}

function print_remote_course($course, $width="100%") {

    global $CFG, $USER;

    $linkcss = '';

    $url = "{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}";

    echo '<div class="coursebox remotecoursebox">';
    echo '<div class="info">';
    echo '<div class="name"><a title="'.get_string('entercourse').'"'.
         $linkcss.' href="'.$url.'">'
        .  format_string($course->fullname) .'</a><br />'
        . format_string($course->hostname) . ' : '
        . format_string($course->cat_name) . ' : ' 
        . format_string($course->shortname). '</div>';   
    echo '</div><div class="summary">';
    $options = NULL;
    $options->noclean = true;
    $options->para = false;
    echo format_text($course->summary, FORMAT_MOODLE, $options);
    echo '</div>';
    echo '</div>';
    echo '<div class="clearer"></div>';
}

function print_remote_host($host, $width="100%") {

    global $CFG, $USER;

    $linkcss = '';

    echo '<div class="coursebox">';
    echo '<div class="info">';
    echo '<div class="name">';
    echo '<img src="'.$CFG->pixpath.'/i/mnethost.gif" class="icon" alt="'.get_string('course').'" />';
    echo '<a title="'.s($host['name']).'" href="'.s($host['url']).'">'
        . s($host['name']).'</a> - ';
    echo $host['count'] . ' ' . get_string('courses');
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<div class="clearer"></div>';
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

/**
* $prevstateoverrides = true will set the visibility of the course module
* to what is defined in visibleold. This enables us to remember the current
* visibility when making a whole section hidden, so that when we toggle
* that section back to visible, we are able to return the visibility of
* the course module back to what it was originally.
*/
function set_coursemodule_visible($id, $visible, $prevstateoverrides=false) {
    if (!$cm = get_record('course_modules', 'id', $id)) {
        return false;
    }
    if (!$modulename = get_field('modules', 'name', 'id', $cm->module)) {
        return false;
    }
    if ($events = get_records_select('event', "instance = '$cm->instance' AND modulename = '$modulename'")) {
        foreach($events as $event) {
            if ($visible) {
                show_event($event);
            } else {
                hide_event($event);
            }
        }
    }
    if ($prevstateoverrides) {
        if ($visible == '0') {
            // Remember the current visible state so we can toggle this back.
            set_field('course_modules', 'visibleold', $cm->visible, 'id', $id);
        } else {
            // Get the previous saved visible states.
            return set_field('course_modules', 'visible', $cm->visibleold, 'id', $id);
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
            delete_event($event->id);
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

    // Check for duplicates and fix order if needed.
    // There is a very rare case that some sections in the same course have the same section id.
    $sections = get_records_select('course_sections', "course = $course->id", 'section ASC');
    $n = 0;
    foreach ($sections as $section) {
        if ($section->section != $n) {
            if (!set_field('course_sections', 'section', $n, 'id', $section->id)) {
                return false;
            }
        }
        $n++;
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

    $modcontext = get_context_instance(CONTEXT_MODULE, $mod->id);
    // no permission to edit
    if (!has_capability('moodle/course:manageactivities', $modcontext)) {
        return false;
    }

    if (!isset($str)) {
        $str->delete         = get_string("delete");
        $str->move           = get_string("move");
        $str->moveup         = get_string("moveup");
        $str->movedown       = get_string("movedown");
        $str->moveright      = get_string("moveright");
        $str->moveleft       = get_string("moveleft");
        $str->update         = get_string("update");
        $str->duplicate      = get_string("duplicate");
        $str->hide           = get_string("hide");
        $str->show           = get_string("show");
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
        $hideshow = '<a class="editing_hide" title="'.$str->hide.'" href="'.$path.'/mod.php?hide='.$mod->id.
                    '&amp;sesskey='.$sesskey.$section.'"><img'.
                    ' src="'.$CFG->pixpath.'/t/hide.gif" class="iconsmall" '.
                    ' alt="'.$str->hide.'" /></a>'."\n";
    } else {
        $hideshow = '<a class="editing_show" title="'.$str->show.'" href="'.$path.'/mod.php?show='.$mod->id.
                    '&amp;sesskey='.$sesskey.$section.'"><img'.
                    ' src="'.$CFG->pixpath.'/t/show.gif" class="iconsmall" '.
                    ' alt="'.$str->show.'" /></a>'."\n";
    }
    if ($mod->groupmode !== false) {
        if ($mod->groupmode == SEPARATEGROUPS) {
            $grouptitle = $str->groupsseparate;
            $groupclass = 'editing_groupsseparate';
            $groupimage = $CFG->pixpath.'/t/groups.gif';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=0&amp;sesskey='.$sesskey;
        } else if ($mod->groupmode == VISIBLEGROUPS) {
            $grouptitle = $str->groupsvisible;
            $groupclass = 'editing_groupsvisible';
            $groupimage = $CFG->pixpath.'/t/groupv.gif';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=1&amp;sesskey='.$sesskey;
        } else {
            $grouptitle = $str->groupsnone;
            $groupclass = 'editing_groupsnone';
            $groupimage = $CFG->pixpath.'/t/groupn.gif';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=2&amp;sesskey='.$sesskey;
        }
        if ($mod->groupmodelink) {
            $groupmode = '<a class="'.$groupclass.'" title="'.$grouptitle.' ('.$str->clicktochange.')" href="'.$grouplink.'">'.
                         '<img src="'.$groupimage.'" class="iconsmall" '.
                         'alt="'.$grouptitle.'" /></a>';
        } else {
            $groupmode = '<img title="'.$grouptitle.' ('.$str->forcedmode.')" '.
                         ' src="'.$groupimage.'" class="iconsmall" '.
                         'alt="'.$grouptitle.'" />';
        }
    } else {
        $groupmode = "";
    }

    if (has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $mod->course))) {
        if ($moveselect) {
            $move =     '<a class="editing_move" title="'.$str->move.'" href="'.$path.'/mod.php?copy='.$mod->id.
                        '&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$CFG->pixpath.'/t/move.gif" class="iconsmall" '.
                        ' alt="'.$str->move.'" /></a>'."\n";
        } else {
            $move =     '<a class="editing_moveup" title="'.$str->moveup.'" href="'.$path.'/mod.php?id='.$mod->id.
                        '&amp;move=-1&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$CFG->pixpath.'/t/up.gif" class="iconsmall" '.
                        ' alt="'.$str->moveup.'" /></a>'."\n".
                        '<a class="editing_movedown" title="'.$str->movedown.'" href="'.$path.'/mod.php?id='.$mod->id.
                        '&amp;move=1&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$CFG->pixpath.'/t/down.gif" class="iconsmall" '.
                        ' alt="'.$str->movedown.'" /></a>'."\n";
        }
    }

    $leftright = "";
    if ($indent > 0) {
        $leftright .= '<a class="editing_moveleft" title="'.$str->moveleft.'" href="'.$path.'/mod.php?id='.$mod->id.
                      '&amp;indent=-1&amp;sesskey='.$sesskey.$section.'"><img'.
                      ' src="'.$CFG->pixpath.'/t/left.gif" class="iconsmall" '.
                      ' alt="'.$str->moveleft.'" /></a>'."\n";
    }
    if ($indent >= 0) {
        $leftright .= '<a class="editing_moveright" title="'.$str->moveright.'" href="'.$path.'/mod.php?id='.$mod->id.
                      '&amp;indent=1&amp;sesskey='.$sesskey.$section.'"><img'.
                      ' src="'.$CFG->pixpath.'/t/right.gif" class="iconsmall" '.
                      ' alt="'.$str->moveright.'" /></a>'."\n";
    }

    return '<span class="commands">'."\n".$leftright.$move.
           '<a class="editing_update" title="'.$str->update.'" href="'.$path.'/mod.php?update='.$mod->id.
           '&amp;sesskey='.$sesskey.$section.'"><img'.
           ' src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" '.
           ' alt="'.$str->update.'" /></a>'."\n".
           '<a class="editing_delete" title="'.$str->delete.'" href="'.$path.'/mod.php?delete='.$mod->id.
           '&amp;sesskey='.$sesskey.$section.'"><img'.
           ' src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" '.
           ' alt="'.$str->delete.'" /></a>'."\n".$hideshow.$groupmode."\n".'</span>';
}

/**
 * given a course object with shortname & fullname, this function will
 * truncate the the number of chars allowed and add ... if it was too long
 */
function course_format_name ($course,$max=100) {

    $str = $course->shortname.': '. $course->fullname;
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
    echo '<td align="right"><b>'.get_string('visible', '').':</b></td>';
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

    // i am not sure this capability is correct
    if (has_capability('moodle/course:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
        return true;
    }
    if (is_numeric($mod)) {
        $modid = $mod;
    } else if (is_string($mod)) {
        if ($mod = get_field("modules","id","name",$mod))
            $modid = $mod;
    }
    if (empty($modid)) {
        return false;
    }
    return (record_exists("course_allowed_modules","course",$course->id,"module",$modid));
}

/***
 *** Efficiently moves many courses around while maintaining
 *** sortorder in order.
 ***
 *** $courseids is an array of course ids
 ***
 **/

function move_courses ($courseids, $categoryid) {

    global $CFG;

    if (!empty($courseids)) {

            $courseids = array_reverse($courseids);

            foreach ($courseids as $courseid) {

                if (! $course  = get_record("course", "id", $courseid)) {
                    notify("Error finding course $courseid");
                } else {
                    // figure out a sortorder that we can use in the destination category
                    $sortorder = get_field_sql('SELECT MIN(sortorder)-1 AS min
                                                    FROM ' . $CFG->prefix . 'course WHERE category=' . $categoryid);
                    if ($sortorder === false) {
                        // the category is empty
                        // rather than let the db default to 0
                        // set it to > 100 and avoid extra work in fix_coursesortorder()
                        $sortorder = 200;
                    } else if ($sortorder < 10) {
                        fix_course_sortorder($categoryid);
                    }

                    $course->category  = $categoryid;
                    $course->sortorder = $sortorder;
                    $course->fullname = addslashes($course->fullname);
                    $course->shortname = addslashes($course->shortname);
                    $course->summary = addslashes($course->summary);
                    $course->password = addslashes($course->password);
                    $course->teacher = addslashes($course->teacher);
                    $course->teachers = addslashes($course->teachers);
                    $course->student = addslashes($course->student);
                    $course->students = addslashes($course->students);

                    if (!update_record('course', $course)) {
                        notify("An error occurred - course not moved!");
                    }
                    // parents changed (course category), do not delete child context relations
                    insert_context_rel(get_context_instance(CONTEXT_COURSE, $course->id), false);
                }
            }
            fix_course_sortorder();
        }
    return true;
}

/**
 * @param string $format Course format ID e.g. 'weeks'
 * @return Name that the course format prefers for sections
 */
function get_section_name($format) {
    $sectionname = get_string("name$format","format_$format");
    if($sectionname == "[[name$format]]") {
        $sectionname = get_string("name$format");
    }
	return $sectionname;
}

/**
 * Can the current user delete this course?
 * @param int $courseid
 * @return boolean
 *
 * Exception here to fix MDL-7796.
 *
 * FIXME
 *   Course creators who can manage activities in the course
 *   shoule be allowed to delete the course. We do it this
 *   way because we need a quick fix to bring the functionality
 *   in line with what we had pre-roles. We can't give the
 *   default course creator role moodle/course:delete at
 *   CONTEXT_SYSTEM level because this will allow them to
 *   delete any course in the site. So we hard code this here
 *   for now.
 *
 * @author vyshane AT gmail.com
 */
function can_delete_course($courseid) {

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    return ( has_capability('moodle/course:delete', $context)
             || (has_capability('moodle/legacy:coursecreator', $context)
             && has_capability('moodle/course:manageactivities', $context)) );
}


/* 
 * Create a course and either return a $course object or false
 *
 * @param object $data  - all the data needed for an entry in the 'course' table
 */
function create_course($data) {
    global $CFG, $USER;

    // preprocess allowed mods
    $allowedmods = empty($data->allowedmods) ? array() : $data->allowedmods;
    unset($data->allowedmods);
    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        if ($CFG->restrictmodulesfor == 'all') {
            $data->restrictmodules = 1;
        } else {
            $data->restrictmodules = 0;
        }
    }

    $data->timecreated = time();

    // place at beginning of category
    fix_course_sortorder();
    $data->sortorder = get_field_sql("SELECT min(sortorder)-1 FROM {$CFG->prefix}course WHERE category=$data->category");
    if (empty($data->sortorder)) {
        $data->sortorder = 100;
    }

    if ($newcourseid = insert_record('course', $data)) {  // Set up new course

        $course = get_record('course', 'id', $newcourseid);

        // Setup the blocks
        $page = page_create_object(PAGE_COURSE_VIEW, $course->id);
        blocks_repopulate_page($page); // Return value not checked because you can always edit later

        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            update_restricted_mods($course, $allowedmods);
        }

        $section = new object();
        $section->course = $course->id;   // Create a default section.
        $section->section = 0;
        $section->id = insert_record('course_sections', $section);

        fix_course_sortorder();

        add_to_log(SITEID, 'course', 'new', 'view.php?id='.$course->id, $data->fullname.' (ID '.$course->id.')');

        return $course;
    } 

    return false;   // error
}


/* 
 * Update a course and return true or false
 *
 * @param object $data  - all the data needed for an entry in the 'course' table
 */
function update_course($data) {
    global $USER, $CFG;

    // preprocess allowed mods
    $allowedmods = empty($data->allowedmods) ? array() : $data->allowedmods;
    unset($data->allowedmods);
    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        unset($data->restrictmodules);
    }

    $oldcourse = get_record('course', 'id', $data->id); // should not fail, already tested above
    if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $oldcourse->category))
      or !has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $data->category))) {
        // can not move to new category, keep the old one
        unset($data->category);
    }

    // Update with the new data
    if (update_record('course', $data)) {

        $course = get_record('course', 'id', $data->id);

        add_to_log($course->id, "course", "update", "edit.php?id=$course->id", $course->id);

        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            update_restricted_mods($course, $allowedmods);
        }

        fix_course_sortorder();

        // Test for and remove blocks which aren't appropriate anymore
        $page = page_create_object(PAGE_COURSE_VIEW, $course->id);
        blocks_remove_inappropriate($page);

        return true;

    }

    return false;
}

?>
