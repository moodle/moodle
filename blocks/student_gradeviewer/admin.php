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
 * @package    block_student_gradeviewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/student_gradeviewer/admin/lib.php');

require_login();

$admintype = optional_param('type', 'person_mentor', PARAM_TEXT);

$context = context_system::instance();

$admin = (
    has_capability('block/student_gradeviewer:sportsadmin', $context) or
    has_capability('block/student_gradeviewer:academicadmin', $context)
);

if (!$admin) {
    print_error('no_permission', 'block_student_gradeviewer');
}

$classes = student_mentor_admin_page::gather_classes();

if (!isset($classes[$admintype])) {
    $admintype = 'person_mentor';
}

$form = $classes[$admintype];

$baseurl = new moodle_url('/blocks/student_gradeviewer/admin.php');

$s = ues::gen_str('block_student_gradeviewer');
$blockname = $s('pluginname');
$heading = $s('admin');

$PAGE->set_context($context);
$PAGE->set_url($baseurl);
$PAGE->set_title("$blockname: $heading");
$PAGE->set_heading("$blockname: $heading");
$PAGE->set_pagetype('mentor-administration');
$PAGE->navbar->add($SITE->shortname);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($heading);

echo $OUTPUT->header();

$toname = function($class) {
    return $class->get_name();
};

echo $OUTPUT->single_select(
    $baseurl, 'type',
    array_map($toname, $classes), $admintype
);

echo $OUTPUT->heading($form->get_name());

if ($data = data_submitted()) {
    $form->process_data($data);
}

echo $form->ui_filters();
echo $form->user_form();

echo $OUTPUT->footer();
