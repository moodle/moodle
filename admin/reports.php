<?php

    require_once(dirname(dirname(__FILE__)).'/config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');

    $report = optional_param('report',STATS_REPORT_ACTIVE_COURSES,PARAM_INT);
    $time = optional_param('time',0,PARAM_INT);
    $numcourses = optional_param('numcourses',20,PARAM_INT);

    if (empty($CFG->enablestats)) {
        error("Stats is not enabled.");
    }

    require_login();

    if (!isadmin()) {
        error("This page is for admins only");
    }

    $course = get_site();
    stats_check_uptodate($course->id);

    $strheader = get_string('reports');

    $strnav = '<a href="'.$CFG->wwwroot.'/admin/index.php">'.get_string('administration').'</a> -> '.$strheader;
    
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

    if (empty($timeoptions)) {
        error(get_string('nostatstodisplay'), $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

    print_header($strheader,$strheader,$strnav,'','',true,'&nbsp');

    echo '<form action="reports.php" method="post">'."\n";

    $table->width = '*';

    $table->align = array('left','left','left','left','left','left');
    $table->data[] = array(get_string('statsreporttype'),choose_from_menu($reportoptions,'report',$report,'','','',true),
                           get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                           '<input type="text" name="numcourses" size="3" maxlength="2" value="'.$numcourses.'" />',
                           '<input type="submit" value="'.get_string('view').'" />') ;

    print_table($table);
    echo '</form>';

    if (!empty($report) && !empty($time)) {
        $param = stats_get_parameters($time,$report,SITEID,STATS_MODE_RANKED);
        
        $sql = "SELECT courseid,".$param->fields." FROM ".$CFG->prefix.'stats_'.$param->table
            ." WHERE timeend >= ".$param->timeafter
            ." GROUP BY courseid "
            .$param->extras
            ." ORDER BY ".$param->orderby
            ." LIMIT ".$numcourses;
        
        $courses = get_records_sql($sql);

        if (empty($courses)) {
            error(get_string('statsnodata'),$CFG->wwwroot.'/admin/reports.php');
        }

        echo '<center><img src="'.$CFG->wwwroot.'/admin/reportsgraph.php?time='.$time.'&report='.$report.'&numcourses='.$numcourses.'" /></center>';
        
        $table = new object();
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
            $a[] = print_numeric_value($c->line1);
            if (isset($c->line2)) {
                $a[] = print_numeric_value($c->line2);
            }
            if (isset($c->line3)) {
                $a[] = print_numeric_value($c->line3);
            }
            $table->data[] = $a;
        }
        print_table($table);
    }
    
    print_footer();

function print_numeric_value($value) {
    list($whole, $decimals) = split ('[.,]', $value, 2);
    if (intval($decimals) > 0)
        return number_format($value,2,".",",");
    else
        return $value;
}

?>