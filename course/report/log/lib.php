<?php

function print_log_selector_form($course, $selecteduser=0, $selecteddate="today",
                                 $modname="", $modid=0, $modaction="", $selectedgroup=-1,$showcourses=0,$showusers=0) {

    global $USER, $CFG;

    // first check to see if we can override showcourses and showusers
    $numcourses =  count_records_select("course", "", "COUNT(id)");
    if ($numcourses < COURSE_MAX_COURSES_PER_DROPDOWN && !$showcourses) {
        $showcourses = 1;
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

    if ($course->category) {
        if ($selectedgroup) {   // If using a group, only get users in that group.
            $courseusers = get_group_users($selectedgroup, 'u.lastname ASC', '', 'u.id, u.firstname, u.lastname, u.idnumber');
        } else {
            $courseusers = get_course_users($course->id, '', '', 'u.id, u.firstname, u.lastname, u.idnumber');
        }
    } else {
        $courseusers = get_site_users("u.lastaccess DESC", "u.id, u.firstname, u.lastname, u.idnumber");
    }
    
    if (count($courseusers) < COURSE_MAX_USERS_PER_DROPDOWN && !$showusers) {
        $showusers = 1;
    }

    if ($showusers) {
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

?>
