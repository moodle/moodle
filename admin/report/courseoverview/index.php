<?php

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');
    require_once($CFG->libdir.'/adminlib.php');

    $report     = optional_param('report', STATS_REPORT_ACTIVE_COURSES, PARAM_INT);
    $time       = optional_param('time', 0, PARAM_INT);
    $numcourses = optional_param('numcourses', 20, PARAM_INT);

    admin_externalpage_setup('reportcourseoverview');
    admin_externalpage_print_header();

    if (empty($CFG->enablestats)) {
        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=stats", get_string('mustenablestats', 'admin'), 3);
        } else {
            error("Stats is not enabled.");
        }
    }

    $course = get_site();
    stats_check_uptodate($course->id);

    $strreports = get_string('reports');
    $strcourseoverview = get_string('courseoverview');

    $reportoptions = stats_get_report_options($course->id,STATS_MODE_RANKED);

    $tableprefix = $CFG->prefix.'stats_';

    $earliestday = get_field_sql('SELECT timeend FROM '.$tableprefix.'daily ORDER BY timeend');
    $earliestweek = get_field_sql('SELECT timeend FROM '.$tableprefix.'weekly ORDER BY timeend');
    $earliestmonth = get_field_sql('SELECT timeend FROM '.$tableprefix.'monthly ORDER BY timeend');

    if (empty($earliestday)) $earliestday = time();
    if (empty($earliestweek)) $earliestweek = time();
    if (empty($earliestmonth)) $earliestmonth = time();

    $now = stats_get_base_daily();
    $lastweekend = stats_get_base_weekly();
    $lastmonthend = stats_get_base_monthly();

    $timeoptions = stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);

    if (empty($timeoptions)) {
        print_error('nostatstodisplay', "", $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

    echo '<form action="index.php" method="post">'."\n";
    echo '<div>';

    $table->width = '*';
    $table->align = array('left','left','left','left','left','left');
    $table->data[] = array(get_string('statsreporttype'),choose_from_menu($reportoptions,'report',$report,'','','',true),
                           get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                           '<input type="text" name="numcourses" size="3" maxlength="2" value="'.$numcourses.'" />',
                           '<input type="submit" value="'.get_string('view').'" />') ;

    print_table($table);
    echo '</div>';
    echo '</form>';

    print_heading($reportoptions[$report]);


    if (!empty($report) && !empty($time)) {
        $param = stats_get_parameters($time,$report,SITEID,STATS_MODE_RANKED);
        if (!empty($param->sql)) {
            $sql = $param->sql;
        } else {
            $sql = "SELECT courseid,".$param->fields." FROM ".$CFG->prefix.'stats_'.$param->table
                ." WHERE timeend >= $param->timeafter AND stattype = 'activity' AND roleid = 0"
                ." GROUP BY courseid "
                .$param->extras
                ." ORDER BY ".$param->orderby;
        }
        error_log($sql);

        $courses = get_records_sql($sql, 0, $numcourses);

        if (empty($courses)) {
            notify(get_string('statsnodata'));echo '</td></tr></table>';echo '<p>after notify</p>';

        } else {
            if (empty($CFG->gdversion)) {
                echo '<div class="graph">(' . get_string("gdneed") .')</div>';
            } else {
                echo '<div class="graph"><img alt="'.get_string('courseoverviewgraph').'" src="'.$CFG->wwwroot.'/'.$CFG->admin.'/report/courseoverview/reportsgraph.php?time='.$time.'&report='.$report.'&numcourses='.$numcourses.'" /></div>';
            }

            $table = new StdClass;
            $table->align = array('left','center','center','center');
            $table->head = array(get_string('course'),$param->line1);
            if (!empty($param->line2)) {
                $table->head[] = $param->line2;
            }
            if (!empty($param->line3)) {
                $table->head[] = $param->line3;
            }

            foreach  ($courses as $c) {
                $a = array();
                $a[] = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$c->courseid.'">'.get_field('course','shortname','id',$c->courseid).'</a>';

                $a[] = $c->line1;
                if (isset($c->line2)) {
                    $a[] = $c->line2;
                }
                if (isset($c->line3)) {
                    $a[] = round($c->line3,2);
                }
                $table->data[] = $a;
            }
            print_table($table);
        }
    }
    admin_externalpage_print_footer();

?>
