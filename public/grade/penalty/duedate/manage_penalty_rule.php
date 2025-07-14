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
 * Site configuration settings for the gradepenalty_duedate plugin
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use core\url;
use gradepenalty_duedate\output\edit_penalty_rule_action_bar;
use gradepenalty_duedate\output\form\edit_penalty_form;
use gradepenalty_duedate\output\view_penalty_rule_action_bar;
use gradepenalty_duedate\penalty_rule;
use gradepenalty_duedate\table\penalty_rule_table;

require_once(__DIR__ . '/../../../config.php');
require_once("$CFG->libdir/adminlib.php");

// Page parameters.
$contextid = optional_param('contextid', 1, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$edit = optional_param('edit', 0, PARAM_INT);
$reset = optional_param('reset', 0, PARAM_INT);
$deleteeall = optional_param('deleteallrules', 0, PARAM_INT);

// Check login and permissions.
[$context, $course, $cm] = get_context_info_array($contextid);
if ($context->contextlevel == CONTEXT_SYSTEM) {
    require_admin();
} else {
    require_login($course, false, $cm);
    require_capability('gradepenalty/duedate:manage', $context);
}

$PAGE->set_context($context);
$url = new url('/grade/penalty/duedate/manage_penalty_rule.php', ['contextid' => $contextid]);
$PAGE->set_url($url);

// Return to this page without edit mode.
if (!$returnurl) {
    $returnurl = $url;
}

// Display page according to context.
if ($context->contextlevel == CONTEXT_COURSE) {
    $course = get_course($context->instanceid);
    $PAGE->set_heading($course->fullname);
} else if ($context->contextlevel == CONTEXT_MODULE) {
    $PAGE->set_heading($PAGE->activityrecord->name);
} else {
    $PAGE->set_heading(get_string('administrationsite'));
}

// Print the header and tabs.
$PAGE->set_cacheable(false);
if (!$edit) {
    $title = get_string('duedaterule', 'gradepenalty_duedate');
} else {
    $title = get_string('editduedaterule', 'gradepenalty_duedate');
    // Add edit navigation node.
    $PAGE->navbar->add(get_string('editduedaterule', 'gradepenalty_duedate'));
}
$PAGE->set_title($title);
$PAGE->set_pagelayout('admin');
$PAGE->activityheader->disable();

// If reset button is clicked, reset the penalty rules.
if ($reset || $deleteeall) {
    // Show message for user confirmation.
    $confirmurl = new url($url->out(), [
        'contextid' => $contextid,
        'resetconfirm' => 1,
    ]);
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('resetconfirm', 'gradepenalty_duedate'), $confirmurl, $url);
    echo $OUTPUT->footer();
    die;
} else if (optional_param('resetconfirm', 0, PARAM_INT)) {
    // Reset the penalty rules.
    penalty_rule::reset_rules($contextid);
}

// Only initialize the form if we are in edit mode.
if ($edit) {
    // Create a form to add / edit penalty rules.
    $mform = new edit_penalty_form($url->out(), [
        'contextid' => $contextid,
        'edit' => $edit,
    ]);

    if ($mform->is_cancelled()) {
        redirect($returnurl);
    } else if ($fromform = $mform->get_data()) {
        // Save the form data.
        $mform->save_data($fromform);

        // Redirect to the same page.
        redirect($url, get_string('changessaved'), 0, notification::NOTIFY_SUCCESS);
    }

    // Add javascript to handle the repeater.
    $PAGE->requires->js_call_amd('gradepenalty_duedate/edit_penalty_form', 'init');
}

// Start output.
echo $OUTPUT->header();

if (!$edit) {
    $actionbar = new view_penalty_rule_action_bar($context, $title, $url);
    $renderer = $PAGE->get_renderer('core_grades');
    echo $renderer->render_action_bar($actionbar);

    // Display the penalty table.
    echo $OUTPUT->heading(get_string('existingrule', 'gradepenalty_duedate'), 5);
    $penaltytable = new penalty_rule_table('penalty_rule_table', $contextid);
    $penaltytable->define_baseurl($url);
    $penaltytable->out(30, true);
} else {
    $actionbar = new edit_penalty_rule_action_bar($context, $title, $url);
    $renderer = $PAGE->get_renderer('core_grades');
    echo $renderer->render_action_bar($actionbar);

    // Wrap the form in a container, so we can replace the form.
    echo $OUTPUT->box_start('generalbox', 'penalty_rule_form_container');
    // Display the form.
    $mform->display();
    // End of the box.
    echo $OUTPUT->box_end();
}

// Footer.
echo $OUTPUT->footer();
