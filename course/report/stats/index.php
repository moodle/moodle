<?php

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');
    require_once($CFG->dirroot.'/course/report/stats/lib.php');
    require_once($CFG->libdir.'/adminlib.php');

    $courseid = optional_param('course', SITEID, PARAM_INT);
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
        redirect($CFG->wwwroot.'/course/report/stats/index.php?time='.$time);
    }

    if (!$course = $DB->get_record("course", array("id"=>$courseid))) {
        print_error("invalidcourseid");
    }

    if (!empty($userid)) {
        if (!$user = $DB->get_record('user', array('id'=>$userid))) {
            print_error("nousers");
        }
    }

    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('coursereport/stats:view', $context);

    $PAGE->set_url(new moodle_url('/course/report/stats/index.php', array('course' => $course->id,
                                                                          'report' => $report,
                                                                          'time'   => $time,
                                                                          'mode'   => $mode,
                                                                          'userid' => $userid)));

    add_to_log($course->id, "course", "report stats", "report/stats/index.php?course=$course->id", $course->id);
    stats_check_uptodate($course->id);

    if ($course->id == SITEID) {
        admin_externalpage_setup('reportstats');
        echo $OUTPUT->header();
    } else {
        $strreports = get_string("reports");
        $strstats = get_string('stats');

        $PAGE->set_title("$course->shortname: $strstats");
        $PAGE->set_heading($course->fullname);
        $PAGE->set_pagelayout('report');
        $PAGE->set_headingmenu(report_stats_mode_menu($course, $mode, $time, "$CFG->wwwroot/course/report/stats/index.php"));
        echo $OUTPUT->header();
    }

    require($CFG->dirroot.'/course/report/stats/report.php');

    if (empty($CFG->enablestats)) {
        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=stats", get_string('mustenablestats', 'admin'), 3);
        } else {
            print_error('statsdisable');
        }
    }

    echo $OUTPUT->footer();


