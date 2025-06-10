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
require_once('crosslist_form.php');

require_login();

if (!cps_crosslist::is_enabled()) {
    print_error('not_enabled', 'block_cps', '', cps_crosslist::name());
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

$s = ues::gen_str('block_cps');

$blockname = $s('pluginname');
$heading = cps_crosslist::name();

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_heading($blockname . ': '. $heading);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($heading);
$PAGE->set_url('/blocks/cps/crosslist.php');
$PAGE->set_title($heading);
$PAGE->set_pagetype('cps-crosslist');

$PAGE->requires->jquery();
$PAGE->requires->js('/blocks/cps/js/selection.js');
$PAGE->requires->js('/blocks/cps/js/crosslist.js');

$form = cps_form::create('crosslist', $semesters);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/my'));

} else if ($data = $form->get_data()) {

    if (isset($data->back)) {
        $form->next = $form->prev;

    } else if ($form->next == crosslist_form::FINISHED) {
        $form = new crosslist_form_finish();

        try {
            $form->process($data, $semesters);

            $form->display();
        } catch (Exception $e) {
            echo $OUTPUT->notification($s('application_errors', $e->getMessage()));
            echo $OUTPUT->continue_button('/my');
        }
        die();
    }

    $form = cps_form::next_from('crosslist', $form->next, $data, $semesters);
}

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($heading, 'crosslist', 'block_cps');

$form->display();

echo $OUTPUT->footer();