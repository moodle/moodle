<?php

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    if (!empty($CFG->enablestats)) {
   
        $strreports = get_string('reports');
        $strcourseoverview = get_string('courseoverview');

        print_heading("$strcourseoverview:");

        require_once($CFG->dirroot.'/lib/statslib.php');

        $report     = optional_param('report', STATS_REPORT_ACTIVE_COURSES, PARAM_INT);
        $time       = optional_param('time', 0, PARAM_INT);
        $numcourses = optional_param('numcourses', 20, PARAM_INT);

        $course = get_site();
        stats_check_uptodate($course->id);


        $reportoptions = stats_get_report_options($course->id,STATS_MODE_RANKED);

        $tableprefix = $CFG->prefix.'stats_';

        $earliestday = get_field_sql('SELECT timeend FROM '.$tableprefix.'daily ORDER BY timeend LIMIT 1');
        $earliestweek = get_field_sql('SELECT timeend FROM '.$tableprefix.'weekly ORDER BY timeend LIMIT 1');
        $earliestmonth = get_field_sql('SELECT timeend FROM '.$tableprefix.'monthly ORDER BY timeend LIMIT 1');

        if (empty($earliestday)) $earliestday = time();
        if (empty($earliestweek)) $earliestweek = time();
        if (empty($earliestmonth)) $earliestmonth = time();

        $now = stats_get_base_daily();
        $lastweekend = stats_get_base_weekly();
        $lastmonthend = stats_get_base_monthly();

        $timeoptions = stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);

        $table->width = '*';
        $table->align = array('left','left','left','left','left','left');
        $table->data[] = array(get_string('statsreporttype'),choose_from_menu($reportoptions,'report',$report,'','','',true),
                get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                '<input type="text" name="numcourses" size="3" maxlength="2" value="'.$numcourses.'" />',
                '<input type="submit" value="'.get_string('view').'" />') ;

        echo '<form action="report/courseoverview/index.php" method="post">'."\n";
        print_table($table);
        echo '</form>';
    }
?>

