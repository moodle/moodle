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
require_once('team_request_form.php');

require_login();

if (!cps_team_request::is_enabled()) {
    print_error('not_enabled', 'block_cps', '', cps_team_request::name());
}

if (!ues_user::is_teacher()) {
    print_error('not_teacher', 'block_cps');
}

$teacher = ues_teacher::get(array('userid' => $USER->id));

$nonprimaries = (bool) get_config('block_cps', 'team_request_nonprimary');

$sections = cps_unwant::active_sections_for($teacher, !$nonprimaries);

if (empty($sections)) {
    print_error('no_section', 'block_cps');
}

$semesters = ues_semester::merge_sections($sections);

$s = ues::gen_str('block_cps');

$blockname = $s('pluginname');
$heading = cps_team_request::name();

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_heading($blockname . ': ' . $heading);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($heading);
$PAGE->set_title($heading);
$PAGE->set_url('/blocks/cps/team_request.php');
$PAGE->set_pagetype('cps-teamteach');

$form = cps_form::create('team_request', $semesters);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/my'));

} else if ($data = $form->get_data()) {

    if (isset($data->back)) {
        $form->next = $form->prev;

    } else if ($form->next == team_request_form::FINISHED) {
        $form = new team_request_form_finish();

        $form->process($data, $semesters);

        $form->display();

        die();
    } else if ($form->next == team_request_form::SECTIONS) {
        redirect(new moodle_url('/blocks/cps/team_section.php', array(
            'id' => $data->selected
        )));
    }

    $form = cps_form::next_from('team_request', $form->next, $data, $semesters);
}

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($heading, 'team_request', 'block_cps');

$form->display();

echo $OUTPUT->footer();
