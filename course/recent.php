<?php // $Id$

//  Display all recent activity in a flexible way

    require_once('../config.php');
    require_once('lib.php');
    require_once('recent_form.php');

    $id = required_param('id', PARAM_INT);

    if (!$course = get_record('course', 'id', $id) ) {
        error("That's an invalid course id");
    }

    require_login($course);

    add_to_log($course->id, "course", "recent", "recent.php?id=$course->id", $course->id);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $meta = '<meta name="robots" content="none" />'; // prevent duplicate content in search engines MDL-7299

    $lastlogin = time() - COURSE_MAX_RECENT_PERIOD;
    if (!isguestuser() and !empty($USER->lastcourseaccess[$COURSE->id])) {
        if ($USER->lastcourseaccess[$COURSE->id] > $lastlogin) {
            $lastlogin = $USER->lastcourseaccess[$COURSE->id];
        }
    }

    $param = new object();
    $param->user   = 0;
    $param->modid  = 'all';
    $param->group  = 0;
    $param->sortby = 'default';
    $param->date   = $lastlogin;
    $param->id     = $COURSE->id;

    $mform = new recent_form();
    $mform->set_data($param);
    if ($formdata = $mform->get_data(false)) {
        $param = $formdata;
    }

    $userinfo = get_string('allparticipants');
    $dateinfo = get_string('alldays');

    if (!empty($param->user)) {
        if (!$u = get_record('user', 'id', $param->user) ) {
            error("That's an invalid user!");
        }
        $userinfo = fullname($u);
    }

    $strrecentactivity = get_string('recentactivity');
    $navlinks = array();
    $navlinks[] = array('name' => $strrecentactivity, 'link' => "recent.php?id=$course->id", 'type' => 'misc');
    $navlinks[] = array('name' => $userinfo, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header("$course->shortname: $strrecentactivity", $course->fullname, $navigation, '', $meta);
    print_heading(format_string($course->fullname) . ": $userinfo", '', 3);

    $mform->display();

    $modinfo =& get_fast_modinfo($course);
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    if (has_capability('moodle/course:viewhiddensections', $context)) {
        $hiddenfilter = "";
    } else {
        $hiddenfilter = "AND cs.visible = 1";
    }
    $sections = array();
    if ($ss = get_records_sql("SELECT cs.id, cs.section, cs.sequence, cs.summary, cs.visible
                                 FROM {$CFG->prefix}course_sections cs
                                WHERE cs.course = $course->id AND cs.section <= $course->numsections
                                      $hiddenfilter
                             ORDER BY section")) {
        foreach ($ss as $section) {
            $sections[$section->section] = $section;
        }
    }

    if ($param->modid === 'all') {
        // ok

    } else if (strpos($param->modid, 'mod/') === 0) {
        $modname = substr($param->modid, strlen('mod/'));
        if (array_key_exists($modname, $modnames) and file_exists("$CFG->dirroot/mod/$modname/lib.php")) {
            $filter = $modname;
        }

    } else if (strpos($param->modid, 'section/') === 0) {
        $sectionid = substr($param->modid, strlen('section/'));
        if (isset($sections[$sectionid])) {
            $sections = array($sectionid=>$sections[$sectionid]);
        }

    } else if (is_numeric($param->modid)) {
        $section = $sections[$modinfo->cms[$param->modid]->sectionnum];
        $section->sequence = $param->modid;
        $sections = array($section->sequence=>$section);
    }

    switch ($course->format) {
        case 'weeks':  $sectiontitle = get_string('week'); break;
        case 'topics': $sectiontitle = get_string('topic'); break;
        default: $sectiontitle = get_string('section'); break;
    }

    if (is_null($modinfo->groups)) {
        $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
    }

    $activities = array();
    $index = 0;

    foreach ($sections as $section) {

        $activity = new object();
        $activity->type = 'section';
        if ($section->section > 0) {
            $activity->name = $sectiontitle.' '.$section->section;
        } else {
            $activity->name = '';
        }

        $activity->visible = $section->visible;
        $activities[$index++] = $activity;

        if (empty($section->sequence)) {
            continue;
        }

        $sectionmods = explode(",", $section->sequence);

        foreach ($sectionmods as $cmid) {
            if (!isset($mods[$cmid]) or !isset($modinfo->cms[$cmid])) {
                continue;
            }

            $cm = $modinfo->cms[$cmid];

            if (!$cm->uservisible) {
                continue;
            }

            if (!empty($filter) and $cm->modname != $filter) {
                continue;
            }

            $libfile = "$CFG->dirroot/mod/$cm->modname/lib.php";

            if (file_exists($libfile)) {
                require_once($libfile);
                $get_recent_mod_activity = $cm->modname."_get_recent_mod_activity";

                if (function_exists($get_recent_mod_activity)) {
                    $activity = new object();
                    $activity->type    = 'activity';
                    $activity->cmid    = $cmid;
                    $activities[$index++] = $activity;
                    $get_recent_mod_activity($activities, $index, $param->date, $course->id, $cmid, $param->user, $param->group);
                }
            }
        }
    }

    $detail = true;

    switch ($param->sortby) {
        case 'datedesc' : usort($activities, 'compare_activities_by_time_desc'); break;
        case 'dateasc'  : usort($activities, 'compare_activities_by_time_asc'); break;
        case 'default'  :
        default         : $detail = false; $param->sortby = 'default';

    }

    if (!empty($activities)) {

        $newsection   = true;
        $lastsection  = '';
        $newinstance  = true;
        $lastinstance = '';
        $inbox        = false;

        $section = 0;

        $activity_count = count($activities);
        $viewfullnames  = array();

        foreach ($activities as $key => $activity) {

            if ($activity->type == 'section') {
                if ($param->sortby != 'default') {
                    continue; // no section if ordering by date
                }
                if ($activity_count == ($key + 1) or $activities[$key+1]->type == 'section') {
                // peak at next activity.  If it's another section, don't print this one!
                // this means there are no activities in the current section
                    continue;
                }
            }

            if (($activity->type == 'section') && ($param->sortby == 'default')) {
                if ($inbox) {
                    print_simple_box_end();
                    print_spacer(30);
                }
                print_simple_box_start('center', '90%');
                echo "<h2>$activity->name</h2>";
                $inbox = true;

            } else if ($activity->type == 'activity') {

                if ($param->sortby == 'default') {
                    $cm = $modinfo->cms[$activity->cmid];

                    if ($cm->visible) {
                        $linkformat = '';
                    } else {
                        $linkformat = 'class="dimmed"';
                    }
                    $name        = format_string($cm->name);
                    $modfullname = $modnames[$cm->modname];

                    $image = "<img src=\"$CFG->modpixpath/$cm->modname/icon.gif\" class=\"icon\" alt=\"$modfullname\" />";
                    echo "<h4>$image $modfullname".
                         " <a href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id\" $linkformat>$name</a></h4>";
               }

            } else {

                if (!isset($viewfullnames[$activity->cmid])) {
                    $cm_context = get_context_instance(CONTEXT_MODULE, $activity->cmid);
                    $viewfullnames[$activity->cmid] = has_capability('moodle/site:viewfullnames', $cm_context);
                }

                if (!$inbox) {
                    print_simple_box_start('center', '90%');
                    $inbox = true;
                }

                $print_recent_mod_activity = $activity->type.'_print_recent_mod_activity';

                if (function_exists($print_recent_mod_activity)) {
                    $print_recent_mod_activity($activity, $course->id, $detail, $modnames, $viewfullnames[$activity->cmid]);
                }
            }
        }

        if ($inbox) {
            print_simple_box_end();
        }


    } else {

        echo '<h4><center>' . get_string('norecentactivity') . '</center></h2>';

    }

    print_footer($course);

function compare_activities_by_time_desc($a, $b) {
    // make sure the activities actually have a timestamp property
    if ((!array_key_exists('timestamp', $a)) or (!array_key_exists('timestamp', $b))) {
      return 0;
    }
    if ($a->timestamp == $b->timestamp)
        return 0;
    return ($a->timestamp > $b->timestamp) ? -1 : 1;
}

function compare_activities_by_time_asc($a, $b) {
    // make sure the activities actually have a timestamp property
    if ((!array_key_exists('timestamp', $a)) or (!array_key_exists('timestamp', $b))) {
      return 0;
    }
    if ($a->timestamp == $b->timestamp)
        return 0;
    return ($a->timestamp < $b->timestamp) ? -1 : 1;
}
?>
