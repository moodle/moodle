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
 * Page to view the course reuse actions.
 *
 * @package    core_backup
 * @copyright  2023 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');

// Course id.
$courseid = required_param('id', PARAM_INT);

$PAGE->set_url(new moodle_url('/backup/view.php', ['id' => $courseid]));

// Basic access checks.
if (!$course = $DB->get_record('course', ['id' => $courseid])) {
    throw new \moodle_exception('invalidcourseid');
}
require_login($course);

$title = get_string('coursereuse');
// Only append the course name if the course ID is not the site ID.
if ($courseid != SITEID) {
    $title .= moodle_page::TITLE_SEPARATOR . $course->fullname;
}
// Otherwise, output the page with a notification stating that there are no available course reuse actions.
$PAGE->set_title($title);
$PAGE->set_pagelayout('incourse');
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);
$PAGE->add_body_class('limitedwidth');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('coursereuse'));

// Check if there is at least one displayable course reuse action.
$hasactions = false;
if ($coursereusenode = $PAGE->settingsnav->find('coursereuse', \navigation_node::TYPE_CONTAINER)) {
    foreach ($coursereusenode->children as $child) {
        if ($child->display) {
            $hasactions = true;
            break;
        }
    }
}

if ($hasactions) {
    echo $OUTPUT->render_from_template('core/report_link_page', ['node' => $coursereusenode]);
} else {
    throw new \moodle_exception(
        'accessdenied',
        'admin',
        new moodle_url('/course/view.php', ['id' => $courseid])
    );
}
echo $OUTPUT->footer();
