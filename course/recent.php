<?php // $Id$

//  Display all recent activity in a flexible way

    require_once("../config.php");
    require_once("lib.php");

    $id             = required_param('id', PARAM_INT);
    $user           = optional_param('user', '0', PARAM_INT);
    $sortby         = optional_param('sortby', 'default', PARAM_ALPHA);
    $selectedgroup  = optional_param('selectedgroup', 0, PARAM_INT);
    $date           = optional_param('date', '', PARAM_INT);
    $advancedfilter = optional_param('advancedfilter', 0, PARAM_INT);
    $modname        = optional_param('modname', '', PARAM_ALPHA);          // not used??
    $modid          = optional_param('modid', 'activity/All', PARAM_FILE); // not a file, but looks like it anyway
    $modaction      = optional_param('modaction', '', PARAM_ALPHA);        // not used??
    $chooserecent   = optional_param('chooserecent', 0, PARAM_INT);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    add_to_log($course->id, "course", "recent", "recent.php?id=$course->id", "$course->id");

    $strrecentactivity = get_string("recentactivity");
    $meta = '<meta name="robots" content="none" />'; // prevent duplicate content in search engines MDL-7299

    $loggedinas = user_login_string($course, $USER);
    $navlinks = array();

    if (!empty($chooserecent)) {
        $userinfo = get_string("allparticipants");
        $dateinfo = get_string("alldays");

        if ($user) {
            if (!$u = get_record("user", "id", $user) ) {
                error("That's an invalid user!");
            }
            $userinfo = fullname($u);
        }
        if ($date)
            $dateinfo = userdate($date, get_string("strftimedaydate"));

        $navlinks[] = array('name' => $strrecentactivity, 'link' => "recent.php?id=$course->id", 'type' => 'misc');
        $navlinks[] = array('name' => "$userinfo, $dateinfo", 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);
        print_header("$course->shortname: $strrecentactivity", $course->fullname, $navigation, "", $meta);

        print_heading(format_string($course->fullname) . ": $userinfo, $dateinfo (".usertimezone().")", '', 3);
        $advancedfilter = 1;
        print_recent_selector_form($course, $advancedfilter, $user, $date, $modname, $modid, $modaction, $selectedgroup, $sortby);

    } else {

        if (empty($date)) { // no date picked, default to last login time
            $date = time() - COURSE_MAX_RECENT_PERIOD;

            if (!empty($USER->lastcourseaccess[$course->id])) {
                if ($USER->lastcourseaccess[$course->id] > $date) {
                    $date = $USER->lastcourseaccess[$course->id];
                }
            }
        }
        $navlinks[] = array('name' => $strrecentactivity, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);
        print_header("$course->shortname: $strrecentactivity", $course->fullname, $navigation, "", $meta);

        print_heading(get_string("activitysince", "", userdate($date)));

        print_recent_selector_form($course, $advancedfilter);

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

    $activities = array();
    $sections = array();

    switch ($course->format) {
        case "weeks": $sectiontitle = get_string("week"); break;
        case "topics": $sectiontitle = get_string("topic"); break;
        default: $sectiontitle = get_string("section"); break;
    }

    $index = 0;

    if (is_numeric($modid)) { // you chose a single activity

        $sections[0]->sequence = "$modid";

    } else { // you chose a group of activities

        if (has_capability('moodle/course:viewhiddensections', get_context_instance(CONTEXT_COURSE, $course->id))) {
            $hiddenfilter = "";
        } else {
            $hiddenfilter = " AND cs.visible = '1' ";
        }

        $sections = get_records_sql("SELECT cs.id, cs.section, cs.sequence, cs.summary, cs.visible
                                       FROM {$CFG->prefix}course_sections cs
                                       WHERE course = '$course->id' $hiddenfilter
                                      ORDER by section");
    }

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    if (!empty($sections)) {

        echo "<hr />";
        $i = 0;

        if (!empty($filter)) {
            $activityfilter = "AND m.name = '$filter'";
        } else {
            $activityfilter = "";
        }

        if (has_capability('moodle/course:viewhiddensections', get_context_instance(CONTEXT_COURSE, $course->id))) {
            $hiddenfilter = "";
        } else {
            $hiddenfilter = " AND cm.visible = '1' ";
        }

        foreach ($sections as $section) {

            if ($i <= $course->numsections) {
                $activity = new Object;
                $activity->type = "section";
                if ($i) {
                    $activity->name = $sectiontitle . " $i";
                } else {
                    $activity->name = '';
                }
                $activity->visible = $section->visible;
                $activities[$index] = $activity;
            }
            $index++;
            $i++;

            $sectionmods = explode(",", $section->sequence);

            foreach ($sectionmods as $sectionmod) {

                if (empty($mods[$sectionmod])) {
                    continue;
                }
                $mod = $mods[$sectionmod];
                $instance = get_record("$mod->modname", "id", "$mod->instance");

                $coursemod = get_record_sql("SELECT m.id, m.name, cm.groupmode, cm.visible
                                               FROM {$CFG->prefix}course_modules cm,
                                                    {$CFG->prefix}modules m
                                              WHERE course = '$course->id' $hiddenfilter
                                                AND m.id = cm.module $activityfilter
                                                AND cm.id = '$sectionmod'");

                $groupmode = groupmode($course, $coursemod);
                switch ($groupmode) {
                    case SEPARATEGROUPS :  $groupid = mygroupid($course->id); break;
                    case VISIBLEGROUPS  :  $groupid = $selectedgroup; break;
                    case NOGROUPS       :
                    default             :  $groupid = 0;
                }

                $libfile = "$CFG->dirroot/mod/$coursemod->name/lib.php";

                if (file_exists($libfile)) {
                    require_once($libfile);
                    $get_recent_mod_activity = $coursemod->name."_get_recent_mod_activity";

                    if (function_exists($get_recent_mod_activity)) {
                        $activity = new Object;
                        $activity->type = "activity";
                        $activity->name = $instance->name;
                        $activity->visible = $coursemod->visible;
                        $activity->content->modfullname = $mod->modfullname;
                        $activity->content->modname = $mod->modname;
                        $activity->content->modid =$mod->id;
                        $activities[$index] = $activity;
                        $index++;
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

        $newsection = true;
        $lastsection = "";
        $newinstance = true;
        $lastinstance = "";
        $inbox = false;

        $section = 0;

        if (has_capability('moodle/course:viewhiddensections', get_context_instance(CONTEXT_COURSE, $course->id))) {
            $teacher = true;
        } else {
            $teacher = false;
        }
        $activity_count = count($activities);

        foreach ($activities as $key => $activity) {

            // peak at next activity.  If it's another section, don't print this one!
            // this means there are no activities in the current section
            if (($activity->type == "section") &&
                (($activity_count == ($key + 1)) ||
                ($activities[$key+1]->type == "section"))) {

                continue;

            }

            if (($activity->type == "section") && ($sortby == "default")) {
                if ($inbox) {
                    print_simple_box_end();
                    print_spacer(30);
                }
                print_simple_box_start("center", "90%");
                echo "<h2>$activity->name</h2>";
                $inbox = true;

            } else if ($activity->type == "activity") {

               if ($sortby == "default") {
                   if ($teacher && $activity->visible == 0) {
                       $linkformat = 'class="dimmed"';
                   } else {
                       $linkformat = '';
                   }
                   $image = "<img src=\"$CFG->modpixpath/" . $activity->content->modname . "/icon.gif\"" .
                            " class=\"icon\" alt=\"" . $activity->content->modfullname . "\" />";
                   echo "<ul><h4>$image " . $activity->content->modfullname .
                        " <a href=\"$CFG->wwwroot/mod/" . $activity->content->modname . "/view.php?" .
                        "id=" . $activity->content->modid . "\" $linkformat>" .
                        format_string($activity->name,true) . "</a></h4></ul>";
               }

            } else {

                if (!$inbox) {
                    print_simple_box_start("center", "90%");
                    $inbox = true;
                }

                $print_recent_mod_activity = $activity->type."_print_recent_mod_activity";

                if (function_exists($print_recent_mod_activity)) {
                    echo '<ul><ul>';
                    $print_recent_mod_activity($activity, $course->id, $detail);
                    echo '</ul></ul>';
                }
            }
        }

        if ($inbox) {
            print_simple_box_end();
        }


    } else {

        echo "<h4><center>" . get_string("norecentactivity") . "</center></h2>";

    }
// fix modid for selection form
    $modid =$tmpmodid;

    print_footer($course);

function compare_activities_by_time_desc($a, $b) {
    // make sure the activities actually have a timestamp property
    if ((!object_property_exists($a,'timestamp')) or (!object_property_exists($b,'timestamp'))) {
      return 0;
    }
    if ($a->timestamp == $b->timestamp)
        return 0;
    return ($a->timestamp > $b->timestamp) ? -1 : 1;
}

function compare_activities_by_time_asc($a, $b) {
    // make sure the activities actually have a timestamp property
    if ((!object_property_exists($a,'timestamp')) or (!object_property_exists($b,'timestamp'))) {
      return 0;
    }
    if ($a->timestamp == $b->timestamp)
        return 0;
    return ($a->timestamp < $b->timestamp) ? -1 : 1;
}
?>
