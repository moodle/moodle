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
 * This page allows authorised users to configure course sync to Microsoft.
 *
 * @package block_microsoft
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2021 onwards Microsoft, Inc. (http://microsoft.com/)
 */

require_once(__DIR__.'/../../config.php');
require_once($CFG->dirroot . '/blocks/microsoft/forms.php');
require_once($CFG->dirroot . '/blocks/microsoft/lib.php');
require_once($CFG->dirroot . '/local/o365/lib.php');

$courseid = required_param('course', PARAM_INT);
$coursecontext = context_course::instance($courseid);

require_login($courseid);

require_capability('local/o365:teamowner', $coursecontext);

$PAGE->set_context($coursecontext);

$redirecturl = new moodle_url('/course/view.php', ['id' => $courseid]);

// Validations.
$sitecoursesyncconfig = get_config('local_o365', 'coursesync');
if ($sitecoursesyncconfig != 'oncustom') {
    throw new moodle_exception('error_course_sync_not_configurable_per_course', 'block_microsoft', $redirecturl);
}

if (!get_config('local_o365', 'course_sync_per_course')) {
    throw new moodle_exception('error_course_sync_not_configurable_per_course', 'block_microsoft', $redirecturl);
}

$formdata = [
    'course' => $courseid,
    'sync' => block_microsoft_get_course_sync_option($courseid),
];

$mform = new block_microsoft_course_sync_form();
$mform->set_data($formdata);

if ($mform->is_cancelled()) {
    redirect($redirecturl);
} else if ($fromform = $mform->get_data()) {
    block_microsoft_set_course_sync_option($fromform->course, $fromform->sync);

    redirect($redirecturl, get_string('sync_setting_saved', 'block_microsoft'));
}

$pagetitle = get_string('configure_course_sync', 'block_microsoft');
$PAGE->set_url('/blocks/microsoft/configure_sync.php', ['course' => $courseid]);
$PAGE->navbar->add($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('sync_page_heading', 'block_microsoft', $COURSE->fullname));

$mform->display();

echo $OUTPUT->footer();
