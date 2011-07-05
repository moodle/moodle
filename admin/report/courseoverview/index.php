<?php

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');
    require_once($CFG->libdir.'/adminlib.php');

    $report     = optional_param('report', STATS_REPORT_ACTIVE_COURSES, PARAM_INT);
    $time       = optional_param('time', 0, PARAM_INT);
    $numcourses = optional_param('numcourses', 20, PARAM_INT);

    if (empty($CFG->enablestats)) {
        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=stats", get_string('mustenablestats', 'admin'), 3);
        } else {
            print_error('statsdisable');
        }
    }

    admin_externalpage_setup('reportcourseoverview');
    echo $OUTPUT->header();

    $course = get_site();
    stats_check_uptodate($course->id);

    $strreports = get_string('reports');
    $strcourseoverview = get_string('courseoverview');

    $reportoptions = stats_get_report_options($course->id,STATS_MODE_RANKED);

    $earliestday = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_daily}');
    $earliestweek = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_weekly}');
    $earliestmonth = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_monthly}');

    if (empty($earliestday)) $earliestday = time();
    if (empty($earliestweek)) $earliestweek = time();
    if (empty($earliestmonth)) $earliestmonth = time();

    $now = stats_get_base_daily();
    $lastweekend = stats_get_base_weekly();
    $lastmonthend = stats_get_base_monthly();

    $timeoptions = stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);

    if (empty($timeoptions)) {
        print_error('nostatstodisplay', 'error', $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

    echo '<form action="index.php" method="post">'."\n";
    echo '<div>';

    $table = new html_table();
    $table->width = '*';
    $table->align = array('left','left','left','left','left','left');

    $reporttypemenu = html_writer::select($reportoptions,'report',$report, false);
    $timeoptionsmenu = html_writer::select($timeoptions,'time',$time, false);

    $table->data[] = array(get_string('statsreporttype'),$reporttypemenu,
                           get_string('statstimeperiod'),$timeoptionsmenu,
                           '<input type="text" name="numcourses" size="3" maxlength="2" value="'.$numcourses.'" />',
                           '<input type="submit" value="'.get_string('view').'" />') ;

    echo html_writer::table($table);
    echo '</div>';
    echo '</form>';

    echo $OUTPUT->heading($reportoptions[$report]);


    if (!empty($report) && !empty($time)) {
        $param = stats_get_parameters($time,$report,SITEID,STATS_MODE_RANKED);
        if (!empty($param->sql)) {
            $sql = $param->sql;
        } else {
            $sql = "SELECT courseid,".$param->fields."
                      FROM {".'stats_'.$param->table."}
                     WHERE timeend >= $param->timeafter AND stattype = 'activity' AND roleid = 0
                  GROUP BY courseid
                           $param->extras
                  ORDER BY $param->orderby";
        }

        $courses = $DB->get_records_sql($sql, $param->params, 0, $numcourses);

        if (empty($courses)) {
            echo $OUTPUT->notification(get_string('statsnodata'));
            echo '</td></tr></table>';

        } else {
            if (empty($CFG->gdversion)) {
                echo '<div class="graph">(' . get_string("gdneed") .')</div>';
            } else {
                echo '<div class="graph"><img alt="'.get_string('courseoverviewgraph').'" src="'.$CFG->wwwroot.'/'.$CFG->admin.'/report/courseoverview/reportsgraph.php?time='.$time.'&report='.$report.'&numcourses='.$numcourses.'" /></div>';
            }

            $table = new html_table();
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
                $a[] = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$c->courseid.'">'.$DB->get_field('course', 'shortname', array('id'=>$c->courseid)).'</a>';

                $a[] = $c->line1;
                if (isset($c->line2)) {
                    $a[] = $c->line2;
                }
                if (isset($c->line3)) {
                    $a[] = round($c->line3,2);
                }
                $table->data[] = $a;
            }
            echo html_writer::table($table);
        }
    }
    echo $OUTPUT->footer();
