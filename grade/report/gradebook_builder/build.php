<?php

require_once '../../../config.php';
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->dirroot . '/grade/lib.php';
require_once $CFG->dirroot . '/grade/report/gradebook_builder/lib.php';

$courseid = required_param('id', PARAM_INT);
$templateid = required_param('template', PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

require_login($course);

$context = context_course::instance($course->id);

require_capability('gradereport/gradebook_builder:view', $context);
require_capability('moodle/grade:edit', $context);

$tp = array('id' => $templateid);

$template = $DB->get_record('gradereport_builder_template', $tp, '*', MUST_EXIST);

$result = grade_report_gradebook_builder::build_gradebook($courseid, $template);

if (is_string($result)) {
    print_error($result, 'gradereport_gradebook_builder');
}

redirect(new moodle_url('/grade/edit/tree/index.php', array('id' => $courseid)));
