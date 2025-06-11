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
 * @package    block_lsu_people
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Basic requires.
require('../../config.php');
require_once($CFG->libdir . '/tablelib.php');

// Get the course id from the url.
$courseid = required_param('id', PARAM_INT);

$download = optional_param('download', '', PARAM_ALPHA);

$group = optional_param('group', '', PARAM_INT);

// Make sure the user is logged in.
require_login($courseid);

// Set the context.
$context = context_course::instance($courseid);

// Make sure they can view the participants of THIS course.
require_capability('moodle/course:viewparticipants', $context);

// Make sure they can use this tool.
require_capability('block/lsu_people:view', $context);

// Build the url.
$url = new moodle_url('/blocks/lsu_people/view.php', ['id' => $courseid]);

// If we're downloading.
if (!empty($download)) {
    $url->param('download', $download);
}

// Build out the $PAGE.
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'block_lsu_people'));
$PAGE->set_heading(get_string('pluginname', 'block_lsu_people'));
$PAGE->set_pagelayout('report');

// Get the course object.
$course = get_course($courseid);

// Group and role selector.
$groupmode = groups_get_course_groupmode($course);

// If we're viewing the data on screen.
if (!$download) {
    echo $OUTPUT->header();

    // Group selector bar
    if ($groupmode) {
        groups_print_course_menu($course, $PAGE->url);
    }

    // Start the container.
    echo $OUTPUT->container_start('userlist-controls d-flex flex-wrap justify-content-between mb-3');
    echo $OUTPUT->container_end();

    // Render the table using our renderer.
    $output = $PAGE->get_renderer('block_lsu_people');

    // Build out the table using our rendable.
    $renderable = new \block_lsu_people\output\lsu_people($courseid, $download, $group);
    echo $output->render($renderable);
    echo $OUTPUT->footer();

// We're downloading.
} else {

    // Build out the table using our rendable.
    $renderable = new \block_lsu_people\output\lsu_people($courseid, $download, $group);

    // Render the table using our renderer.
    $output = $PAGE->get_renderer('block_lsu_people');

    // Export the data.
    $renderable->export_for_template($output);
}
