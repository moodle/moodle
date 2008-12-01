<?php // $Id$

// Display user activity reports for a course

    require_once("../config.php");
    require_once("lib.php");

    $id      = required_param('id',PARAM_INT);       // course id
    $user    = required_param('user',PARAM_INT);     // user id
    $mode    = optional_param('mode', "todaylogs", PARAM_ALPHA);
    $page    = optional_param('page', 0, PARAM_INT);
    $perpage = optional_param('perpage', 100, PARAM_INT);

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    if (! $user = get_record("user", "id", $user)) {
        error("User ID is incorrect");
    }

    $coursecontext   = get_context_instance(CONTEXT_COURSE, $course->id);
    $personalcontext = get_context_instance(CONTEXT_USER, $user->id);

    require_login();
    if (has_capability('moodle/user:viewuseractivitiesreport', $personalcontext) and !has_capability('moodle/course:view', $coursecontext)) {
        // do not require parents to be enrolled in courses ;-)
        course_setup($course);
    } else {
        require_login($course);
    }

    if ($user->deleted) {
        print_header();
        print_heading(get_string('userdeleted'));
        print_footer();
        die;
    }

    // prepare list of allowed modes
    $myreports  = ($course->showreports and $USER->id == $user->id);
    $anyreport  = has_capability('moodle/user:viewuseractivitiesreport', $personalcontext);

    $modes = array();

    if ($myreports or $anyreport or has_capability('coursereport/outline:view', $coursecontext)) {
        $modes[] = 'outline';
    }

    if ($myreports or $anyreport or has_capability('coursereport/outline:view', $coursecontext)) {
        $modes[] = 'complete';
    }

    if ($myreports or $anyreport or has_capability('coursereport/log:viewtoday', $coursecontext)) {
        $modes[] = 'todaylogs';
    }

    if ($myreports or $anyreport or has_capability('coursereport/log:view', $coursecontext)) {
        $modes[] = 'alllogs';
    }

    if ($myreports or $anyreport or has_capability('coursereport/stats:view', $coursecontext)) {
        $modes[] = 'stats';
    }

    if (has_capability('moodle/grade:viewall', $coursecontext)) {
        //ok - can view all course grades
        $modes[] = 'grade';

    } else if ($course->showgrades and $user->id == $USER->id and has_capability('moodle/grade:view', $coursecontext)) {
        //ok - can view own grades
        $modes[] = 'grade';

    } else if ($course->showgrades and has_capability('moodle/grade:viewall', $personalcontext)) {
        // ok - can view grades of this user - parent most probably
        $modes[] = 'grade';

    } else if ($course->showgrades and $anyreport) {
        // ok - can view grades of this user - parent most probably
        $modes[] = 'grade';
    }

    if (empty($modes)) {
        require_capability('moodle/user:viewuseractivitiesreport', $personalcontext);
    }

    if (!in_array($mode, $modes)) {
        // forbidden or non-exitent mode
        $mode = reset($modes);
    }

    add_to_log($course->id, "course", "user report", "user.php?id=$course->id&amp;user=$user->id&amp;mode=$mode", "$user->id");

    $stractivityreport = get_string("activityreport");
    $strparticipants   = get_string("participants");
    $stroutline        = get_string("outline");
    $strcomplete       = get_string("complete");
    $stralllogs        = get_string("alllogs");
    $strtodaylogs      = get_string("todaylogs");
    $strmode           = get_string($mode);
    $fullname          = fullname($user, true);

    $navlinks = array();

    if ($course->id != SITEID && has_capability('moodle/course:viewparticipants', $coursecontext)) {
        $navlinks[] = array('name' => $strparticipants, 'link' => "../user/index.php?id=$course->id", 'type' => 'misc');
    }

    $navlinks[] = array('name' => $fullname, 'link' => "../user/view.php?id=$user->id&amp;course=$course->id", 'type' => 'misc');
    $navlinks[] = array('name' => $stractivityreport, 'link' => null, 'type' => 'misc');
    $navlinks[] = array('name' => $strmode, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header("$course->shortname: $stractivityreport ($mode)", $course->fullname, $navigation);


/// Print tabs at top
/// This same call is made in:
///     /user/view.php
///     /user/edit.php
///     /course/user.php
    $currenttab = $mode;
    $showroles = 1;
    include($CFG->dirroot.'/user/tabs.php');

    switch ($mode) {
        case "grade":
            if (empty($CFG->grade_profilereport) or !file_exists($CFG->dirroot.'/grade/report/'.$CFG->grade_profilereport.'/lib.php')) {
                $CFG->grade_profilereport = 'user';
            }
            require_once $CFG->libdir.'/gradelib.php';
            require_once $CFG->dirroot.'/grade/lib.php';
            require_once $CFG->dirroot.'/grade/report/'.$CFG->grade_profilereport.'/lib.php';

            $functionname = 'grade_report_'.$CFG->grade_profilereport.'_profilereport';
            if (function_exists($functionname)) {
                $functionname($course, $user);
            }
            break;

        case "todaylogs" :
            echo '<div class="graph">';
            print_log_graph($course, $user->id, "userday.png");
            echo '</div>';
            print_log($course, $user->id, usergetmidnight(time()), "l.time DESC", $page, $perpage,
                      "user.php?id=$course->id&amp;user=$user->id&amp;mode=$mode");
            break;

        case "alllogs" :
            echo '<div class="graph">';
            print_log_graph($course, $user->id, "usercourse.png");
            echo '</div>';
            print_log($course, $user->id, 0, "l.time DESC", $page, $perpage,
                      "user.php?id=$course->id&amp;user=$user->id&amp;mode=$mode");
            break;
        case 'stats':

            if (empty($CFG->enablestats)) {
                error("Stats is not enabled.");
            }

            require_once($CFG->dirroot.'/lib/statslib.php');

            $statsstatus = stats_check_uptodate($course->id);
            if ($statsstatus !== NULL) {
                notify ($statsstatus);
            }

            $earliestday = get_field_sql('SELECT timeend FROM '.$CFG->prefix.'stats_user_daily ORDER BY timeend');
            $earliestweek = get_field_sql('SELECT timeend FROM '.$CFG->prefix.'stats_user_weekly ORDER BY timeend');
            $earliestmonth = get_field_sql('SELECT timeend FROM '.$CFG->prefix.'stats_user_monthly ORDER BY timeend');

            if (empty($earliestday)) $earliestday = time();
            if (empty($earliestweek)) $earliestweek = time();
            if (empty($earliestmonth)) $earliestmonth = time();

            $now = stats_get_base_daily();
            $lastweekend = stats_get_base_weekly();
            $lastmonthend = stats_get_base_monthly();

            $timeoptions = stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);

            if (empty($timeoptions)) {
                print_error('nostatstodisplay', '', $CFG->wwwroot.'/course/user.php?id='.$course->id.'&user='.$user->id.'&mode=outline');
            }

            // use the earliest.
            $time = array_pop(array_keys($timeoptions));

            $param = stats_get_parameters($time,STATS_REPORT_USER_VIEW,$course->id,STATS_MODE_DETAILED);

            $param->table = 'user_'.$param->table;

            $sql = 'SELECT timeend,'.$param->fields.' FROM '.$CFG->prefix.'stats_'.$param->table.' WHERE '
            .(($course->id == SITEID) ? '' : ' courseid = '.$course->id.' AND ')
                .' userid = '.$user->id
                .' AND timeend >= '.$param->timeafter
                .$param->extras
                .' ORDER BY timeend DESC';
            $stats = get_records_sql($sql);

            if (empty($stats)) {
                print_error('nostatstodisplay', '', $CFG->wwwroot.'/course/user.php?id='.$course->id.'&user='.$user->id.'&mode=outline');
            }

            // MDL-10818, do not display broken graph when user has no permission to view graph
            if ($myreports or has_capability('coursereport/stats:view', $coursecontext)) {
                echo '<center><img src="'.$CFG->wwwroot.'/course/report/stats/graph.php?mode='.STATS_MODE_DETAILED.'&course='.$course->id.'&time='.$time.'&report='.STATS_REPORT_USER_VIEW.'&userid='.$user->id.'" alt="'.get_string('statisticsgraph').'" /></center>';
            }

            // What the heck is this about?   -- MD
            $stats = stats_fix_zeros($stats,$param->timeafter,$param->table,(!empty($param->line2)),(!empty($param->line3)));

            $table = new object();
            $table->align = array('left','center','center','center');
            $param->table = str_replace('user_','',$param->table);
            switch ($param->table) {
                case 'daily'  : $period = get_string('day'); break;
                case 'weekly' : $period = get_string('week'); break;
                case 'monthly': $period = get_string('month', 'form'); break;
                default : $period = '';
            }
            $table->head = array(get_string('periodending','moodle',$period),$param->line1,$param->line2,$param->line3);
            foreach ($stats as $stat) {
                if (!empty($stat->zerofixed)) {  // Don't know why this is necessary, see stats_fix_zeros above - MD
                    continue;
                }
                $a = array(userdate($stat->timeend,get_string('strftimedate'),$CFG->timezone),$stat->line1);
                $a[] = $stat->line2;
                $a[] = $stat->line3;
                $table->data[] = $a;
            }
            print_table($table);
            break;

        case "outline" :
        case "complete" :
            get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);
            $sections = get_all_sections($course->id);

            for ($i=0; $i<=$course->numsections; $i++) {

                if (isset($sections[$i])) {   // should always be true

                    $section = $sections[$i];
                    $showsection = (has_capability('moodle/course:viewhiddensections', $coursecontext) or $section->visible or !$course->hiddensections);

                    if ($showsection) { // prevent hidden sections in user activity. Thanks to Geoff Wilbert!

                        if ($section->sequence) {
                            echo '<div class="section">';
                            echo '<h2>';
                            switch ($course->format) {
                                case "weeks": print_string("week"); break;
                                case "topics": print_string("topic"); break;
                                default: print_string("section"); break;
                            }
                            echo " $i</h2>";

                            echo '<div class="content">';

                            if ($mode == "outline") {
                                echo "<table cellpadding=\"4\" cellspacing=\"0\">";
                            }

                            $sectionmods = explode(",", $section->sequence);
                            foreach ($sectionmods as $sectionmod) {
                                if (empty($mods[$sectionmod])) {
                                    continue;
                                }
                                $mod = $mods[$sectionmod];

                                if (empty($mod->visible)) {
                                    continue;
                                }

                                $instance = get_record("$mod->modname", "id", "$mod->instance");
                                $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";

                                if (file_exists($libfile)) {
                                    require_once($libfile);

                                    switch ($mode) {
                                        case "outline":
                                            $user_outline = $mod->modname."_user_outline";
                                            if (function_exists($user_outline)) {
                                                $output = $user_outline($course, $user, $mod, $instance);
                                                print_outline_row($mod, $instance, $output);
                                            }
                                            break;
                                        case "complete":
                                            $user_complete = $mod->modname."_user_complete";
                                            if (function_exists($user_complete)) {
                                                $image = "<img src=\"../mod/$mod->modname/icon.gif\" ".
                                                         "class=\"icon\" alt=\"$mod->modfullname\" />";
                                                echo "<h4>$image $mod->modfullname: ".
                                                     "<a href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                                     format_string($instance->name,true)."</a></h4>";

                                                ob_start();

                                                echo "<ul>";
                                                $user_complete($course, $user, $mod, $instance);
                                                echo "</ul>";

                                                $output = ob_get_contents();
                                                ob_end_clean();

                                                if (str_replace(' ', '', $output) != '<ul></ul>') {
                                                    echo $output;
                                                }
                                            }
                                            break;
                                        }
                                    }
                                }

                            if ($mode == "outline") {
                                echo "</table>";
                            }
                            echo '</div>';  // content
                            echo '</div>';  // section
                        }
                    }
                }
            }
            break;
        default:
            // can not be reached ;-)
    }


    print_footer($course);


function print_outline_row($mod, $instance, $result) {
    global $CFG;

    $image = "<img src=\"$CFG->modpixpath/$mod->modname/icon.gif\" class=\"icon\" alt=\"$mod->modfullname\" />";

    echo "<tr>";
    echo "<td valign=\"top\">$image</td>";
    echo "<td valign=\"top\" style=\"width:300\">";
    echo "   <a title=\"$mod->modfullname\"";
    echo "   href=\"../mod/$mod->modname/view.php?id=$mod->id\">".format_string($instance->name,true)."</a></td>";
    echo "<td>&nbsp;&nbsp;&nbsp;</td>";
    echo "<td valign=\"top\">";
    if (isset($result->info)) {
        echo "$result->info";
    } else {
        echo "<p style=\"text-align:center\">-</p>";
    }
    echo "</td>";
    echo "<td>&nbsp;&nbsp;&nbsp;</td>";
    if (!empty($result->time)) {
        $timeago = format_time(time() - $result->time);
        echo "<td valign=\"top\" style=\"white-space: nowrap\">".userdate($result->time)." ($timeago)</td>";
    }
    echo "</tr>";
}

?>
