<?php // $Id$
      // Display link to live logs in separate window

    require_once('../../../config.php');
    require_once('../../lib.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/adminlib.php');

    $id = required_param('id', PARAM_INT);// Course ID

    if (!$course = get_record('course', 'id', $id) ) {
        error('That\'s an invalid course id'.$id);
    }

    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    require_capability('coursereport/log:viewlive', $context);

    $strlogs = get_string('logs');
    $strreports = get_string('reports');

    if ($course->id == SITEID) {
        admin_externalpage_setup('reportloglive','',array('id' => $course->id));
        admin_externalpage_print_header();

    } else {
        $navlinks = array();
        $navlinks[] = array('name' => $strreports, 'link' => "$CFG->wwwroot/course/report.php?id=$course->id", 'type' => 'misc');
        $navlinks[] = array('name' => $strlogs, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);
        print_header($course->shortname .': '. $strlogs, $course->fullname, $navigation, '');
    }

    print_heading(get_string('loglive', 'coursereport_log'));

    echo '<div class="info">';
    link_to_popup_window('/course/report/log/live.php?id='. $course->id,'livelog', get_string('livelogs'), 500, 800);
    echo '<div>';

    print_footer($course);

?>
