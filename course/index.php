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
}

$showaddcoursebutton = true;
if ($countcategories > 1 || ($countcategories == 1 && $DB->count_records('course') > 200)) {
    $strcategories = get_string('categories');

    $PAGE->set_title("$site->shortname: $strcategories");
    $PAGE->set_heading($COURSE->fullname);
    if (isset($managebutton)) {
        $PAGE->set_button($managebutton);
    }
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strcategories);
    echo $OUTPUT->skip_link_target();
    echo $OUTPUT->box_start('categorybox');
    print_whole_category_list();
    echo $OUTPUT->box_end();
    echo $courserenderer->course_search_form();
} else {
    $strfulllistofcourses = get_string('fulllistofcourses');

    $PAGE->set_title("$site->shortname: $strfulllistofcourses");
    $PAGE->set_heading($COURSE->fullname);
    if (isset($managebutton)) {
        $PAGE->set_button($managebutton);
    }
    echo $OUTPUT->header();
    echo $OUTPUT->skip_link_target();
    echo $OUTPUT->box_start('courseboxes');
    $showaddcoursebutton = print_courses(0);
    echo $OUTPUT->box_end();
}

echo $OUTPUT->container_start('buttons');
if (has_capability('moodle/course:create', $systemcontext) && $showaddcoursebutton) {
    // Print link to create a new course, for the 1st available category.
    $options = array('category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat');
    echo $OUTPUT->single_button(new moodle_url('edit.php', $options), get_string('addnewcourse'), 'get');
}
print_course_request_buttons($systemcontext);
echo $OUTPUT->container_end();
echo $OUTPUT->footer();
