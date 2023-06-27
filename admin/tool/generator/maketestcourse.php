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
 * Script creates a standardised large course for testing reliability and performance.
 *
 * @package tool_generator
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable buffering so that the progress output displays gradually without
// needing to call flush().
define('NO_OUTPUT_BUFFERING', true);

require('../../../config.php');

require_once($CFG->libdir . '/adminlib.php');

// Initialise page and check permissions.
admin_externalpage_setup('toolgeneratorcourse');

// Start page.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('maketestcourse', 'tool_generator'));

// Information message.
$context = context_system::instance();
echo $OUTPUT->box(format_text(get_string('courseexplanation', 'tool_generator'),
        FORMAT_MARKDOWN, array('context' => $context)));

// Check debugging is set to DEVELOPER.
if (!debugging('', DEBUG_DEVELOPER)) {
    echo $OUTPUT->notification(get_string('error_notdebugging', 'tool_generator'));
    echo $OUTPUT->footer();
    exit;
}

// Set up the form.
$mform = new tool_generator_make_course_form('maketestcourse.php');
if ($data = $mform->get_data()) {
    // Do actual work.
    echo $OUTPUT->heading(get_string('creating', 'tool_generator'));
    $backend = new tool_generator_course_backend(
        $data->shortname,
        $data->size,
        false,
        false,
        true,
        $data->fullname,
        $data->summary['text'],
        $data->summary['format']
    );
    $id = $backend->make();

    echo html_writer::div(
            html_writer::link(new moodle_url('/course/view.php', array('id' => $id)),
                get_string('continue')));
} else {
    // Display form.
    $mform->display();
}

// Finish page.
echo $OUTPUT->footer();
