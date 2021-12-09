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
 * Listing of the course administration pages for this course.
 *
 * @copyright 2016 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");

$courseid = required_param('courseid', PARAM_INT);

$PAGE->set_url('/course/admin.php', array('courseid'=>$courseid));

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_pagelayout('incourse');

if ($courseid == $SITE->id) {
    $title = get_string('frontpagesettings');
    $node = $PAGE->settingsnav->find('frontpage', navigation_node::TYPE_SETTING);
    $PAGE->set_primary_active_tab('home');
} else {
    $title = get_string('courseadministration');
    $node = $PAGE->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
}
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($title);
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

if ($node) {
    echo $OUTPUT->render_from_template('core/settings_link_page', ['node' => $node]);
}

echo $OUTPUT->footer();
