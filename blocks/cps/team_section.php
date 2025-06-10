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

/**
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('classes/lib.php');
require_once('team_section_form.php');

require_login();

if (!cps_team_request::is_enabled()) {
    print_error('not_enabled', 'block_cps', '', cps_team_request::name());
}

if (!ues_user::is_teacher()) {
    print_error('not_teacher', 'block_cps');
}

$teacher = ues_teacher::get(array('userid' => $USER->id));

$sections = cps_unwant::active_sections_for($teacher);

if (empty($sections)) {
    print_error('no_section', 'block_cps');
}

$semesters = ues_semester::merge_sections($sections);

$key = required_param('id', PARAM_RAW);
list($semid, $couid) = explode('_', $key);

if (!isset($semesters[$semid]) or !isset($semesters[$semid]->courses[$couid])) {
    print_error('not_course', 'block_cps');
}

$semester = $semesters[$semid];
$course = $semester->courses[$couid];

$currentrequests = cps_team_request::in_course($course, $semester, true);

if (empty($currentrequests)) {
    print_error('not_approved', 'block_cps');
}

$initialdata = array('course' => $course
                   , 'semester' => $semester
                   , 'requests' => $currentrequests
);

$s = ues::gen_str('block_cps');

$blockname = $s('pluginname');
$heading = cps_team_request::name();

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_heading($blockname . ': ' . $heading);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($heading);
$PAGE->set_title($heading);
$PAGE->set_url('/blocks/cps/team_section.php', array('id' => $key));
$PAGE->set_pagetype('cps-teamteach');

$PAGE->requires->jquery();
$PAGE->requires->js('/blocks/cps/js/selection.js');
$PAGE->requires->js('/blocks/cps/js/crosslist.js');

$form = cps_form::create('team_section', $initialdata);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/blocks/cps/team_request.php'));

} else if ($data = $form->get_data()) {

    if (isset($data->back)) {
        $form->next = $form->prev;

    } else if ($form->next == team_section_form::FINISHED) {
        $form = new team_section_form_finish();

        try {
            $form->process($data, $initialdata);

            $form->display();
        } catch (Exception $e) {
            echo $OUTPUT->notification($s('application_errors', $e->getMessage()));
            echo $OUTPUT->continue_button('/my');
        }
        die();
    }

    $form = cps_form::next_from('team_section', $form->next, $data, $initialdata);
}

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($heading, 'team_manage_sections', 'block_cps');

$form->display();

echo $OUTPUT->footer();