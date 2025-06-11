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
 * View sections in a crosssplited shell.
 *
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include required Moodle core.
require('../../config.php');

// Get the main wdsprefs class.
require_once("$CFG->dirroot/blocks/wdsprefs/classes/wdsprefs.php");

// Require user to be logged in.
require_login();

// Get system context for permissions.
$context = context_system::instance();

// Get the crosssplit ID.
$id = required_param('id', PARAM_INT);

// Check if we're undoing the crosssplit.
$undo = optional_param('undo', 0, PARAM_BOOL);

// Set up the page.
$url = new moodle_url('/blocks/wdsprefs/crosssplit_sections.php', ['id' => $id]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('wdsprefs:crosssplitsections', 'block_wdsprefs'));
$PAGE->set_heading(get_string('wdsprefs:crosssplitsections', 'block_wdsprefs'));

// Load required CSS.
$PAGE->requires->css('/blocks/wdsprefs/styles.css');

// If undoing, process it.
if ($undo) {
    $result = wdsprefs::undo_crosssplit($id);

    if ($result) {
        redirect(
            new moodle_url('/blocks/wdsprefs/crosssplit.php'),
            get_string('wdsprefs:undosuccess', 'block_wdsprefs'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        redirect(
            new moodle_url('/blocks/wdsprefs/crosssplit_sections.php', ['id' => $id]),
            get_string('wdsprefs:undofailed', 'block_wdsprefs'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

// Add breadcrumbs.
$PAGE->navbar->add(
    get_string('home'),
    new moodle_url('/')
);
$PAGE->navbar->add(
    get_string('wdsprefs:crosssplit', 'block_wdsprefs'),
    new moodle_url('/blocks/wdsprefs/crosssplit.php')
);
$PAGE->navbar->add(
    get_string('wdsprefs:crosssplitsections', 'block_wdsprefs'),
    new moodle_url('/blocks/wdsprefs/crosssplit_sections.php', ['id' => $id])
);

// Output the header.
echo $OUTPUT->header();

// Get the crosssplit info.
$crosssplit = wdsprefs::get_crosssplit_info($id);

// Check if crosssplit exists and belongs to current user.
if (!$crosssplit || $crosssplit->userid != $USER->id) {
    echo $OUTPUT->notification(get_string('wdsprefs:nocrosssplit', 'block_wdsprefs'), 'notifyerror');
    echo $OUTPUT->footer();
    exit;
}

// Display crosssplit information.
echo html_writer::tag('h3', $crosssplit->shell_name);

// Display course link if it exists.
if ($crosssplit->moodle_course_id) {
    $courseurl = new moodle_url('/course/view.php', ['id' => $crosssplit->moodle_course_id]);
    echo html_writer::tag('p',
        html_writer::link(
            $courseurl,
            get_string('wdsprefs:viewcourse', 'block_wdsprefs'),
            ['class' => 'btn btn-primary', 'target' => '_blank']
        )
    );
}

// Get sections in this crosssplit.
$sections = wdsprefs::get_crosssplit_sections($id);

if (empty($sections)) {
    echo html_writer::tag('p', get_string('wdsprefs:nosections', 'block_wdsprefs'));
} else {
    // Create table for sections.
    $table = new html_table();
    $table->head = [
        get_string('wdsprefs:course', 'block_wdsprefs'),
        get_string('wdsprefs:section', 'block_wdsprefs'),
        get_string('wdsprefs:status', 'block_wdsprefs')
    ];

    foreach ($sections as $section) {
        $row = [];
        $row[] = $section->course_subject_abbreviation . ' ' . $section->course_number;
        $row[] = $section->section_number;
        $row[] = get_string('wdsprefs:sectionstatus_' . $section->status, 'block_wdsprefs');

        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

// Add button row with back and undo buttons.
echo html_writer::start_div('mt-4 buttons-container');

// Undo button.
$undourl = new moodle_url('/blocks/wdsprefs/crosssplit_sections.php', ['id' => $id, 'undo' => 1]);
$confirmmessage = get_string('wdsprefs:undoconfirm', 'block_wdsprefs');
echo html_writer::link(
    $undourl,
    get_string('wdsprefs:undo', 'block_wdsprefs'),
    ['class' => 'btn btn-warning mr-2', 'onclick' => "return confirm('$confirmmessage');"]
);

// Space between buttons.
echo ' ';

// Add back button.
echo html_writer::tag('div',
    html_writer::link(
        new moodle_url('/blocks/wdsprefs/crosssplit.php'),
        get_string('back'),
        ['class' => 'btn btn-secondary']
    ),
    ['class' => 'mt-4']
);

// Output the footer.
echo $OUTPUT->footer();
