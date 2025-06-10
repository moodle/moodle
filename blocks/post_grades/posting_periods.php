<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
ues::require_daos();

require_login();

$flash = optional_param('flash', null, PARAM_INT);
$id = optional_param('id', null, PARAM_INT);
$action = optional_param('action', null, PARAM_TEXT);

$system = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);

$context = context_system::instance();

require_capability('block/post_grades:canconfigure', $context);

$s = ues::gen_str('block_post_grades');

$pluginname = $s('pluginname');
$heading = $s('posting_periods');

$createurl = new moodle_url('/blocks/post_grades/period.php');
$baseurl = new moodle_url('/blocks/post_grades/posting_periods.php');
$adminurl = new moodle_url('/admin/settings.php', array(
    'section' => 'blocksettingpost_grades'
));

$title = "$system->shortname: $heading";

$PAGE->set_url($baseurl);
$PAGE->set_context($context);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->navbar->add($pluginname, $adminurl);
$PAGE->navbar->add($heading);

$periods = $DB->get_records('block_post_grades_periods', null, 'start_time ASC');
$semesters = ues_semester::get_all();

if ($action == 'confirm' and isset($periods[$id])) {
    // Cleanup.
    $DB->delete_records('block_post_grades_postings', array('periodid' => $id));
    $DB->delete_records('block_post_grades_periods', array('id' => $id));

    redirect(new moodle_url($baseurl, array('flash' => 1)));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

if ($action == 'delete' and isset($periods[$id])) {
    $semester = $semesters[$periods[$id]->semesterid];
    $msg = $s('are_you_sure', "$semester");

    $params = array('action' => 'confirm', 'id' => $id);
    $confirmurl = new moodle_url($baseurl, $params);

    echo $OUTPUT->confirm($msg, $confirmurl, $baseurl);
    echo $OUTPUT->footer();
    exit;
}

if ($flash) {
    echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
}

if (empty($periods)) {
    echo $OUTPUT->notification($s('no_posting'));
    echo $OUTPUT->continue_button($createurl);
    echo $OUTPUT->footer();
    exit;
}

$createlink = html_writer::link($createurl, $s('new_posting'));

echo html_writer::tag('div', $createlink, array('class' => 'centered controls'));

$table = new html_table();

$table->head = array(
    $s('semester'),
    $s('post_type'),
    $s('start_time'),
    $s('end_time'),
    get_string('active'),
    get_string('action')
);

$pattern = 'm/d/Y g:00:00 a';

$now = time();

$editicon = $OUTPUT->pix_icon('i/edit', get_string('edit'));
$deleteicon = $OUTPUT->pix_icon('i/delete', get_string('delete'));

foreach ($periods as $period) {
    $line = new html_table_row();

    $semester = $semesters[$period->semesterid];

    $active = ($now >= $period->start_time and $now <= $period->end_time) ? 'Y' : 'N';

    $params = array('id' => $period->id);
    $editurl = new moodle_url($createurl, $params);
    $edit = html_writer::link($editurl, $editicon);

    $params['action'] = 'delete';
    $deleteurl = new moodle_url($baseurl, $params);
    $delete = html_writer::link($deleteurl, $deleteicon);

    $line->cells[] = "$semester";
    $line->cells[] = $s($period->post_type);
    $line->cells[] = date($pattern, $period->start_time);
    $line->cells[] = date($pattern, $period->end_time);
    $line->cells[] = $active;
    $line->cells[] = $edit . ' ' . $delete;

    $table->data[] = $line;
}

echo html_writer::table($table);

echo $OUTPUT->footer();
