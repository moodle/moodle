<?php  // $Id$

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');
    require_once($CFG->dirroot.'/course/report/stats/lib.php');

    if (empty($CFG->enablestats)) {
        error("Stats is not enabled.");
    }

    $courseid = required_param('course', PARAM_INT);
    $report   = optional_param('report', 0, PARAM_INT);
    $time     = optional_param('time', 0, PARAM_INT);
    $mode     = optional_param('mode', STATS_MODE_GENERAL, PARAM_INT);
    $userid   = optional_param('userid', 0, PARAM_INT);
    $roleid   = 0;

    if ($report > 50) {
        $roleid = substr($report,1);
        $report = 5;
    }

    if ($report == STATS_REPORT_USER_LOGINS) {
        $courseid = SITEID; //override
    }

    if ($mode == STATS_MODE_RANKED) {
        redirect($CFG->wwwroot.'/'.$CFG->admin.'/report/stats/index.php?time='.$time);
    }

    if (!$course = get_record("course","id",$courseid)) {
        error("That's an invalid course id");
    }

    if (!empty($userid)) {
        if (!$user = get_record('user','id',$userid)) {
            error("That's an invalid user id");
        }
    }

    require_login();
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    
    if (!has_capability('moodle/site:viewreports', $context)) {
        error('You need do not have the required permission to view reports for this course');
    }

    add_to_log($course->id, "course", "report stats", "report/stats/index.php?course=$course->id", $course->id); 
    stats_check_uptodate($course->id);
    
    
    $strreports = get_string("reports");
    $strstats = get_string('stats');

    $menu = report_stats_mode_menu($course, $mode, $time, "$CFG->wwwroot/course/report/stats/index.php");

    $crumb = "<a href=\"../../view.php?id=$course->id\">" . format_string($course->shortname) . "</a> ->
              <a href=\"../../report.php?id=$course->id\">$strreports</a> ->
              $strstats";

    print_header("$course->shortname: $strstats", $course->fullname, 
                  $crumb, '', '', true, '&nbsp;', $menu);
    
    
    require_once($CFG->dirroot.'/course/report/stats/report.php');
    
    print_footer();

?>
