<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     local_aiquestions
 * @category    admin
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
defined('MOODLE_INTERNAL') || die();

// Get course id for creating the questions in it's bank.
$courseid = optional_param('courseid', 0, PARAM_INT);

if ($courseid == 0) {
    redirect(new moodle_url('/local/aiquestions/index.php'));
}

require_login($courseid);

// Check if the user has the capability to create questions.
$context = context_course::instance($courseid);
require_capability('moodle/question:add', $context);

require_once("$CFG->libdir/formslib.php");
require_once(__DIR__ . '/locallib.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_heading(get_string('pluginname', 'local_aiquestions'));
$PAGE->set_title(get_string('pluginname', 'local_aiquestions'));
$PAGE->set_url('/local/aiquestions/story.php?courseid=' . $courseid);
$PAGE->set_pagelayout('standard');
$PAGE->navbar->add(get_string('pluginname', 'local_aiquestions'), new moodle_url('/local/aiquestions/'));
$PAGE->navbar->add(get_string('story', 'local_aiquestions'),
                    new moodle_url('/local/aiquestions/story.php?courseid=' . $courseid));
$PAGE->requires->js_call_amd('local_aiquestions/state');

echo $OUTPUT->header();

$mform = new local_aiquestions_story_form();

if ($mform->is_cancelled()) {
    if (empty($returnurl)) {
        redirect($CFG->wwwroot . '/local/aiquestions/');
    } else {
        redirect($returnurl);
    }
} else if ($fromform = $mform->get_data()) {
    $story = $fromform->story;
    $numofquestions = $fromform->numofquestions;
    error_log("story here: " . json_encode($story));
    // Call the adhoc task.
    $task = new \local_aiquestions\task\questions();

    error_log("task here: " . json_encode($task));
    if ($task) {
        error_log("task here 2: " . json_encode($task));
        $uniqid = uniqid($USER->id, true);
        $task->set_custom_data(['story' => $story,
                                'numofquestions' => $numofquestions,
                                'courseid' => $courseid,
                                'userid' => $USER->id,
                                'uniqid' => $uniqid ]);
        \core\task\manager::queue_adhoc_task($task);
        $success = get_string('tasksuccess', 'local_aiquestions');
    } else {
        error_log("task here 3: " . json_encode($task));
        $error = get_string('taskerror', 'local_aiquestions');
    }
    // Check if the cron is overdue.
    $lastcron = get_config('tool_task', 'lastcronstart');
    $cronoverdue = ($lastcron < time() - 3600 * 24);

    // Prepare the data for the template.
    $datafortemplate = [
        'courseid' => $courseid,
        'wwwroot' => $CFG->wwwroot,
        'uniqid' => $uniqid,
        'userid' => $USER->id,
        'cron' => $cronoverdue,
    ];
    // Load the ready template.
    echo $OUTPUT->render_from_template('local_aiquestions/loading', $datafortemplate);
} else {
    $mform->display();
}

echo $OUTPUT->footer();
