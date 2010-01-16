<?php
      //  Displays live view of recent logs

    require_once("../../../config.php");
    require_once("../../lib.php");

    $id   = required_param('id', PARAM_INT);
    $page = optional_param('page', 0, PARAM_INT);     // which page to show

    if (! $course = $DB->get_record("course", array("id"=>$id))) {
        print_error('invalidcourseid');
    }

    require_login($course);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('coursereport/log:viewlive', $context);

    add_to_log($course->id, "course", "report live", "report/log/live.php?id=$course->id", $course->id);

    session_get_instance()->write_close();

    // we override the default framename so header/footer
    // links open in a new window
    if (empty($CFG->framename) || $CFG->framename==='_top') {
        $CFG->framename = '_blank';
    }

    $strlivelogs = get_string("livelogs");
    $strupdatesevery = get_string("updatesevery", "moodle", COURSE_LIVELOG_REFRESH);

    $PAGE->set_url('/course/report/log/live.php', array('id'=>$course->id));
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title("$strlivelogs ($strupdatesevery)");
    $PAGE->set_periodic_refresh_delay(COURSE_LIVELOG_REFRESH);
    $PAGE->set_heading($strlivelogs);
    echo $OUTPUT->header();

    $user=0;
    $date=time() - 3600;

    print_log($course, $user, $date, "l.time DESC", $page, 500,
              "live.php?id=$course->id&amp;user=$user&amp;date=$date");

    echo $OUTPUT->footer();

    exit;


