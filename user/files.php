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
 * Manage files in folder in private area.
 *
 * @package   core_user
 * @category  files
 * @copyright 2010 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');

require_login();
if (isguestuser()) {
    die();
}

$context = context_user::instance($USER->id);
require_capability('moodle/user:manageownfiles', $context);

$title = get_string('privatefiles');

$PAGE->set_url('/user/files.php');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading(fullname($USER));
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('user-files');

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');

echo html_writer::start_div('', ['id' => 'userfilesform']);
$form = new \core_user\form\private_files();
$form->set_data_for_dynamic_submission();
$form->display();
echo html_writer::end_div();
$PAGE->requires->js_call_amd('core_user/private_files', 'initDynamicForm',
    ['#userfilesform', \core_user\form\private_files::class]);

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
