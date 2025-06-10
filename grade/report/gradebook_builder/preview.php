<?php

require_once '../../../config.php';
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->dirroot . '/grade/lib.php';

$courseid = required_param('id', PARAM_INT);
$templateid = required_param('template', PARAM_INT);

$c_param = array('id' => $courseid);
$t_param = array('id' => $templateid);

$PAGE->set_url(new moodle_url('/grade/report/gradebook_builder/index.php', $c_param));

$course = $DB->get_record('course', $c_param, '*', MUST_EXIST);

require_login($course);

$context = context_course::instance($courseid);

require_capability('gradereport/gradebook_builder:view', $context);
require_capability('moodle/grade:edit', $context);

$template = $DB->get_record('gradereport_builder_template', $t_param, '*', MUST_EXIST);

$_s = function ($key, $a=null) {
    return get_string($key, 'gradereport_gradebook_builder', $a);
};

$reportname = $_s('pluginname');

print_grade_page_head($course->id, 'report', 'gradebook_builder', $reportname);

// Scan template for modules of any kind
$interest = array();
$preview = json_decode($template->data);
foreach ($preview->categories as $category) {
    foreach ($category->items as $item) {
        if ($item->itemtype != 'manual') {
            $item->explain = $_s('explain_' . $item->itemmodule);
            $interest[] = html_writer::tag('li', $_s('explain', $item));
        }
    }
}

$warning = '';
if (!empty($interest)) {
    $preview_html = html_writer::tag('ul', implode(' ', $interest));
    $warning = html_writer::tag('p', $_s('warning', $preview_html));
}

$msg = $_s('are_you_sure', $warning);

$base_url = '/grade/report/gradebook_builder/%s.php';
$url_params = array('id' => $courseid, 'template' => $templateid);

$cancel_url = new moodle_url(sprintf($base_url, 'index'), $url_params);
$confirm_url = new moodle_url(sprintf($base_url, 'build'), $url_params);

echo $OUTPUT->confirm($msg, $confirm_url, $cancel_url);

echo $OUTPUT->footer();
