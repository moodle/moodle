<?php
    /**
    * This file is also required by /admin/reports/stats/index.php.
    */
    
    
    require_once($CFG->dirroot.'/lib/statslib.php');


    function report_stats_mode_menu($course, $mode, $time, $url) {
        global $CFG, $OUTPUT;
        /*        
        $reportoptions = stats_get_report_options($course->id, $mode);
        $timeoptions = report_stats_timeoptions($mode);
        if (empty($timeoptions)) {
            print_error('nostatstodisplay', '', $CFG->wwwroot.'/course/view.php?id='.$course->id);
        }
        */

        $options = array();
        $options[STATS_MODE_GENERAL] = get_string('statsmodegeneral');
        $options[STATS_MODE_DETAILED] = get_string('statsmodedetailed');
        if (has_capability('coursereport/stats:view', get_context_instance(CONTEXT_SYSTEM))) {
            $options[STATS_MODE_RANKED] = get_string('reports');
        }
        $popupurl = $url."?course=$course->id&time=$time";
        $select = html_select::make_popup_form($popupurl, 'mode', $options, 'switchmode', $mode);
        $select->nothinglabel = false;
        return $OUTPUT->select($select);
    }


    function report_stats_timeoptions($mode) {
        global $CFG, $DB;
        
        if ($mode == STATS_MODE_DETAILED) {
            $earliestday = $DB->get_field_sql('SELECT timeend FROM {stats_user_daily} ORDER BY timeend');
            $earliestweek = $DB->get_field_sql('SELECT timeend FROM {stats_user_weekly} ORDER BY timeend');
            $earliestmonth = $DB->get_field_sql('SELECT timeend FROM {stats_user_monthly} ORDER BY timeend');
        } else {
            $earliestday = $DB->get_field_sql('SELECT timeend FROM {stats_daily} ORDER BY timeend');
            $earliestweek = $DB->get_field_sql('SELECT timeend FROM {stats_weekly} ORDER BY timeend');
            $earliestmonth = $DB->get_field_sql('SELECT timeend FROM {stats_monthly} ORDER BY timeend');
        }


        if (empty($earliestday)) $earliestday = time();
        if (empty($earliestweek)) $earliestweek = time();
        if (empty($earliestmonth)) $earliestmonth = time();

        $now = stats_get_base_daily();
        $lastweekend = stats_get_base_weekly();
        $lastmonthend = stats_get_base_monthly();

        return stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);
    }


?>
