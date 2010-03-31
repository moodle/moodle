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
        echo $OUTPUT->header();

    } else {
        $PAGE->set_title($course->shortname .': '. $strlogs);
        $PAGE->set_heading($course->fullname);
        $PAGE->navbar->add($strreports, new moodle_url('/course/report.php', array('id'=>$course->id)), navigation_node::TYPE_CUSTOM);
        $PAGE->navbar->add($strlogs);
        echo $OUTPUT->header();
    }

    echo $OUTPUT->heading(get_string('loglive', 'coursereport_log'));

    echo $OUTPUT->container_start('info');
    $link = new moodle_url('/course/report/log/live.php?id='. $course->id);
    echo $OUTPUT->action_link($link, get_string('livelogs'), new popup_action('click', $link, 'livelog', array('height' => 500, 'width' => 800)));
    echo $OUTPUT->container_end();

    echo $OUTPUT->footer();


