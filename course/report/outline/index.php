<?php // $Id$

// Display user activity reports for a course (totals)

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/course/lib.php');

    $id = required_param('id',PARAM_INT);       // course id

    if (!$course = get_record('course', 'id', $id)) {
        error('Course id is incorrect.');
    }

    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('coursereport/outline:view', $context);

    add_to_log($course->id, 'course', 'report outline', "report/outline/index.php?id=$course->id", $course->id);

    $showlastaccess = true;
    $hiddenfields = explode(',', $CFG->hiddenuserfields);

    if (array_search('lastaccess', $hiddenfields) and !has_capability('moodle/user:viewhiddendetails', $coursecontext)) {
        $showlastaccess = false;
    }

    $stractivityreport = get_string('activityreport');
    $stractivity       = get_string('activity');
    $strlast           = get_string('lastaccess');
    $strreports        = get_string('reports');
    $strviews          = get_string('views');

    $navlinks = array();
    $navlinks[] = array('name' => $strreports, 'link' => "../../report.php?id=$course->id", 'type' => 'misc');
    $navlinks[] = array('name' => $stractivityreport, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header("$course->shortname: $stractivityreport", $course->fullname, $navigation);

    print_heading(format_string($course->fullname));

    if (!$logstart = get_field_sql("SELECT MIN(time) FROM {$CFG->prefix}log")) {
        error('Logs not available');
    }

    echo '<div class="loginfo">'.get_string('computedfromlogs', 'admin', userdate($logstart)).'</div>';

    echo '<table id="outlinetable" class="generaltable boxaligncenter" cellpadding="5"><tr>';
    echo '<th class="header c0" scope="col">'.$stractivity.'</th>';
    echo '<th class="header c1" scope="col">'.$strviews.'</th>';
    if ($showlastaccess) {
        echo '<th class="header c2" scope="col">'.$strlast.'</th>';
    }
    echo '</tr>';

    $modinfo = get_fast_modinfo($course);

    $sql = "SELECT cm.id, COUNT('x') AS numviews, MAX(time) AS lasttime
              FROM {$CFG->prefix}course_modules cm
                   JOIN {$CFG->prefix}modules m ON m.id = cm.module
                   JOIN {$CFG->prefix}log l     ON l.cmid = cm.id
             WHERE cm.course = $course->id AND l.action LIKE 'view%' AND m.visible = 1
          GROUP BY cm.id";
    $views = get_records_sql($sql);

    $ri = 0;
    $prevsecctionnum = 0;
    foreach ($modinfo->sections as $sectionnum=>$section) {
        foreach ($section as $cmid) {
            $cm = $modinfo->cms[$cmid];
            if ($cm->modname == 'label') {
                continue;
            }
            if (!$cm->uservisible) {
                continue;
            }
            if ($prevsecctionnum != $sectionnum) {
                echo '<tr class="r'.$ri++.' section"><td colspan="3"><h3>';
                switch ($course->format) {
                    case 'weeks': print_string('week'); break;
                    case 'topics': print_string('topic'); break;
                    default: print_string('section'); break;
                }
                echo ' '.$sectionnum.'</h3></td></tr>';

                $prevsecctionnum = $sectionnum;
            }

            $dimmed = $cm->visible ? '' : 'class="dimmed"';
            $modulename = get_string('modulename', $cm->modname);
            echo '<tr class="r'.$ri++.'">';
            echo "<td class=\"cell c0 actvity\"><img src=\"$CFG->modpixpath/$cm->modname/icon.gif\" class=\"icon\" alt=\"$modulename\" />";
            echo "<a $dimmed title=\"$modulename\" href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id\">".format_string($cm->name)."</a></td>";
            echo "<td class=\"cell c1 numviews\">";
            if (!empty($views[$cm->id]->numviews)) {
                echo $views[$cm->id]->numviews;
            } else {
                echo '-';
            }
            echo "</td>";
            if ($showlastaccess) {
                echo "<td class=\"cell c2 lastaccess\">";
                if (isset($views[$cm->id]->lasttime)) {
                    $timeago = format_time(time() - $views[$cm->id]->lasttime);
                    echo userdate($views[$cm->id]->lasttime)." ($timeago)";
                }
                echo "</td>";
            }
            echo '</tr>';
        }
    }
    echo '</table>';

    print_footer($course);


?>
