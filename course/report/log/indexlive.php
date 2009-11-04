<?php
      // Display link to live logs in separate window

    require_once('../../../config.php');
    require_once('../../lib.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/adminlib.php');

    $id = optional_param('id', 0, PARAM_INT);// Course ID

    if (!$course = $DB->get_record('course', array('id'=>$id)) ) {
        print_error('invalidcourseid');
    }

    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    require_capability('coursereport/log:viewlive', $context);

    $strlogs = get_string('logs');
    $strreports = get_string('reports');

    if ($course->id == SITEID) {
        admin_externalpage_setup('reportloglive');
        admin_externalpage_print_header();

    } else {
        $PAGE->set_title($course->shortname .': '. $strlogs);
        $PAGE->set_heading($course->fullname);
        $PAGE->navbar->add($strreports, new moodle_url($CFG->wwwroot.'/course/report.php', array('id'=>$course->id)), navigation_node::TYPE_CUSTOM);
        $PAGE->navbar->add($strlogs);
        echo $OUTPUT->header();
    }

    echo $OUTPUT->heading(get_string('loglive', 'coursereport_log'));

    echo $OUTPUT->container_start('info');
    $link = html_link::make('/course/report/log/live.php?id='. $course->id, get_string('livelogs'));
    $link->add_action(new popup_action('click', $link->url, 'livelog', array('height' => 500, 'width' => 800)));
    echo $OUTPUT->link($link);
    echo $OUTPUT->container_end();

    echo $OUTPUT->footer();


