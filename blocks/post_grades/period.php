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
require_once('posting_period_form.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
ues::require_daos();

require_login();

$id = optional_param('id', null, PARAM_INT);

$system = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);

$context = context_system::instance();

require_capability('block/post_grades:canconfigure', $context);

$s = ues::gen_str('block_post_grades');

$pluginname = $s('pluginname');
$heading = $s('posting_period');

$postingsurl = new moodle_url('/blocks/post_grades/posting_periods.php');
$baseurl = new moodle_url('/blocks/post_grades/period.php');
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

$semesters = ues_semester::get_all(array(), false, 'classes_start DESC');

$form = new posting_period_form(null, array('semesters' => $semesters));

if ($form->is_cancelled()) {
    redirect($postingsurl);
} else if ($data = $form->get_data()) {
    if (!isset($data->export_number)) {
        $data->export_number = 0;
    }

    if (empty($data->id)) {
        $id = $DB->insert_record('block_post_grades_periods', $data);
    } else {
        $DB->update_record('block_post_grades_periods', $data);
    }

    redirect(new moodle_url($postingsurl, array('flash' => 1)));
}

if ($id) {
    $table = 'block_post_grades_periods';
    $params = array('id' => $id);

    $period = $DB->get_record($table, $params, '*', MUST_EXIST);

    $form->set_data($period);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

$form->display();

echo $OUTPUT->footer();
