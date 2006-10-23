<?php
    /**
    * This file is also required by /admin/reports/stats/index.php.
    */
    
    
    require_once('../../../config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');


    function report_stats_mode_menu($course, $mode, $time) {
        global $CFG;
        /*        
        $reportoptions = stats_get_report_options($course->id, $mode);
        $timeoptions = report_stats_timeoptions($mode);
        if (empty($timeoptions)) {
            error(get_string('nostatstodisplay'), $CFG->wwwroot.'/course/view.php?id='.$course->id);
        }
        */

        $options = array();
        $options[STATS_MODE_GENERAL] = get_string('statsmodegeneral');
        $options[STATS_MODE_DETAILED] = get_string('statsmodedetailed');
        if (has_capability('moodle/site:viewreports', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            $options[STATS_MODE_RANKED] = get_string('reports');
        }

        $menu = choose_from_menu($options,'mode',$mode,'','this.form.submit();',0,true);

        $menu = '<form action="index.php" method="post">'."\n"
            .'<input type="hidden" name="course" value="'.$course->id.'" />'."\n"
            .'<input type="hidden" name="time" value="'.$time.'" />'."\n"
            .$menu."\n".'</form>';
        
        return $menu;
    }


    function report_stats_timeoptions($mode) {
        global $CFG;
        
        $tableprefix = $CFG->prefix.'stats_';

        if ($mode == STATS_MODE_DETAILED) {
            $tableprefix = $CFG->prefix.'stats_user_';
        }

        $earliestday = get_field_sql('SELECT timeend FROM '.$tableprefix.'daily ORDER BY timeend');
        $earliestweek = get_field_sql('SELECT timeend FROM '.$tableprefix.'weekly ORDER BY timeend');
        $earliestmonth = get_field_sql('SELECT timeend FROM '.$tableprefix.'monthly ORDER BY timeend');

        if (empty($earliestday)) $earliestday = time();
        if (empty($earliestweek)) $earliestweek = time();
        if (empty($earliestmonth)) $earliestmonth = time();

        $now = stats_get_base_daily();
        $lastweekend = stats_get_base_weekly();
        $lastmonthend = stats_get_base_monthly();

        return stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);
    }


?>
