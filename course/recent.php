<?PHP // $Id$

//  Display all recent activity in a flexible way

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);

    optional_variable($user);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    add_to_log($course->id, "course", "recent", "recent.php?id=$course->id", "$course->id");

    $strrecentactivity = get_string("recentactivity");

    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    if (!empty($_GET['chooserecent'])) {

        $userinfo = get_string("allparticipants");
        $dateinfo = get_string("alldays");

        if ($user) {
            if (!$u = get_record("user", "id", $user) ) {
                error("That's an invalid user!");
            }
            $userinfo = "$u->firstname $u->lastname";
        }
        if ($date) 
            $dateinfo = userdate($date, get_string("strftimedaydate"));

        if ($course->category) {
            print_header("$course->shortname: $strrecentactivity", "$course->fullname",
                         "<a href=\"view.php?id=$course->id\">$course->shortname</a> ->
                          <a href=\"recent.php?id=$course->id\">$strrecentactivity</a> -> $userinfo, $dateinfo", "");
        } else {
            print_header("$course->shortname: $strrecentactivity", "$course->fullname",
                         "<a href=\"../$CFG->admin/index.php\">$stradministration</a> ->
                          <a href=\"recent.php?id=$course->id\">$strrecentactivity</a> -> $userinfo, $dateinfo", "");
        }

        print_heading("$course->fullname: $userinfo, $dateinfo (".usertimezone().")");
        print_recent_selector_form($course, $user, $date, $modname, $modid, $modaction, $selectedgroup, $sortby);

    } else {

        if (empty($date)) { // no date picked, default to last login time
            $date = $USER->lastlogin;
        }

        if ($course->category) {
            print_header("$course->shortname: $strrecentactivity", "$course->fullname",
                     "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> $strrecentactivity", "");
        } else {
            print_header("$course->shortname: $strrecentactivity", "$course->fullname",
                     "<a href=\"../$CFG->admin/index.php\">$stradministration</a> -> $strrecentactivity", "");
        }

        print_heading(get_string("choosereportfilter").":");

        print_recent_selector_form($course);

    }

   $tmpmodid = $modid;

    switch ($tmpmodid) {
      case "activity/Assignments" : $filter = "assignment"; break;
      case "activity/Chats" : $filter = "chat"; break;
      case "activity/Forums" : $filter = "forum"; break;
      case "activity/Quizzes" : $filter = "quiz"; break;
      case "activity/Workshops" : $filter = "workshop"; break;
      default   : $filter = "";
    }

    if (!empty($filter)) {
        $activityfilter = "AND m.name = '$filter'";
    } else {
        $activityfilter = "";
    }

    $activities = array();
    $sections = array();

    $index = 0;

    if (is_numeric($modid)) { // you chose a single activity

        $sections[0]->sequence = "$modid";

    } else { // you chose a group of activities

        $sections = get_records_sql("SELECT cs.id, cs.section, cs.sequence
                                       FROM {$CFG->prefix}course_sections cs
                                       WHERE course = '$course->id'
                                        AND cs.visible = '1'
                                        AND sequence != '' 
                                      ORDER by section");
    }

    if (!empty($sections)) {

        echo "<hr>";

        foreach ($sections as $section) {
            $sectionmods = explode(",", $section->sequence);

            foreach ($sectionmods as $sectionmod) {
                $coursemod = get_record_sql("SELECT m.id, m.name, cm.groupmode
                                               FROM {$CFG->prefix}course_modules cm,
                                                    {$CFG->prefix}modules m
                                              WHERE course = '$course->id'
                                                AND m.id = cm.module $activityfilter
                                                AND cm.id = '$sectionmod'");

                $groupmode = groupmode($course, $coursemod);           
                switch ($groupmode) {
                    case SEPARATEGROUPS :  $groupid = mygroupid($course->id); break;
                    case VISIBLEGROUPS  :  
                                           if ($selectedgroup == "allgroups") {
                                               $groupid == "";
                                           } else {
                                               $groupid = $selectedgroup;
                                           } 
                                           break;
                    case NOGROUPS       :
                    default             :  $groupid = "";
                } 

                $libfile = "$CFG->dirroot/mod/$coursemod->name/lib.php";
            
                if (file_exists($libfile)) {
                    require_once($libfile);
                    $get_recent_mod_activity = $coursemod->name."_get_recent_mod_activity";

                    if (function_exists($get_recent_mod_activity)) {
                        $get_recent_mod_activity($activities, $index, $date, $course->id, $sectionmod, $user, $groupid);
                    }
                }
            }
        }
    }

    $detail = true;

    switch ($sortby) {
        case "datedesc" : usort($activities, "compare_activities_by_time_desc"); break;
        case "dateasc"  : usort($activities, "compare_activities_by_time_asc"); break;
        case "default"  : 
        default         : $detail = false; $sortby = "default";
         
    }

    if (!empty($activities)) {

        echo "<ul>";
        $newsection = true;
        $lastsection = "";
        $newinstance = true;
        $lastinstance = "";

        switch ($course->format) {
            case "weeks": $sectiontitle = get_string("week"); break;
            case "topics": $sectiontitle = get_string("topic"); break;
            default: $sectiontitle = get_string("section"); break;
        }

        echo "<hr>";
        foreach ($activities as $activity) {
             
            if ($sortby == "default") {
                if ($lastsection != $activity->section) {
                    $lastsection = $activity->section;
                    $newsection = true;
                }
                if ($newsection) {
//                    echo "<h2>$sectiontitle: $activity->section</h2>";
                    $newsection = false;
                }

                if ($lastinstance != $activity->instance) {
                    $lastinstance = $activity->instance;
                    $newinstance = true;
                }
                if ($newinstance) {
                    $image = "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
                             "height=16 width=16 alt=\"$activity->type\">";
                    echo "<h4>$image " . $activity->name . "</h4>";
  
                    $newinstance = false;
                } 

            }

            $print_recent_mod_activity = $activity->type."_print_recent_mod_activity";

            if (function_exists($print_recent_mod_activity)) {
                echo "<ul>";
                $print_recent_mod_activity($activity, $course->id, $detail);
                echo "</ul>";
            }
        }
        echo "</ul>";
    } else {
        echo "<h4><center>" . get_string("norecentactivity") . "</center></h2>";
    }

// fix modid for selection form
    $modid =$tmpmodid;

    print_footer($course);

function compare_activities_by_time_desc($a, $b) {
    if ($a->timestamp == $b->timestamp)
        return 0;
    return ($a->timestamp > $b->timestamp) ? -1 : 1;
}

function compare_activities_by_time_asc($a, $b) {
    if ($a->timestamp == $b->timestamp)
        return 0;
    return ($a->timestamp < $b->timestamp) ? -1 : 1;
}
?>
