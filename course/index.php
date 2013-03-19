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
 * Lists the course categories
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once("../config.php");
require_once("lib.php");

$site = get_site();

$systemcontext = context_system::instance();

$PAGE->set_url('/course/index.php');
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('admin');
$courserenderer = $PAGE->get_renderer('core', 'course');

if ($CFG->forcelogin) {
    require_login();
}

$countcategories = $DB->count_records('course_categories');
if (can_edit_in_category()) {
    $managebutton = $OUTPUT->single_button(new moodle_url('/course/manage.php'),
                    get_string('managecourses'), 'get');
    $PAGE->set_button($managebutton);
}
$PAGE->set_heading($COURSE->fullname);
$content = $courserenderer->course_category(0);

echo $OUTPUT->header();
echo $OUTPUT->skip_link_target();
echo $content;

echo $OUTPUT->footer();
