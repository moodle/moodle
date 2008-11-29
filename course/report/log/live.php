<?php // $Id$
      //  Displays live view of recent logs

    require_once("../../../config.php");
    require_once("../../lib.php");

    $id   = required_param('id', PARAM_INT);
    $page = optional_param('page', 0, PARAM_INT);     // which page to show

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('coursereport/log:viewlive', $context);

    add_to_log($course->id, "course", "report live", "report/log/live.php?id=$course->id", $course->id); 

    session_write_close();

    // we override the default framename so header/footer
    // links open in a new window 
    if (empty($CFG->framename) || $CFG->framename==='_top') {
        $CFG->framename = '_blank';
    }

    $strlivelogs = get_string("livelogs");
    $strupdatesevery = get_string("updatesevery", "moodle", COURSE_LIVELOG_REFRESH);

    print_header("$strlivelogs ($strupdatesevery)", "$strlivelogs", "", "", 
                 '<meta http-equiv="Refresh" content="'.COURSE_LIVELOG_REFRESH.'; url=live.php?id='.$course->id.'" />');

    $user=0;
    $date=time() - 3600;

    print_log($course, $user, $date, "l.time DESC", $page, 500,
              "live.php?id=$course->id&amp;user=$user&amp;date=$date");

    print_footer('none');

    exit;

?>
