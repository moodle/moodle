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
 * Page to view the course reports
 *
 * @package    core_grades
 * @subpackage report
 * @copyright  2021 Sujith Haridasan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use core\output\notification;
use core\url;

// Course id.
$contextid = required_param('contextid', PARAM_INT);
$cmid = optional_param('cm', null, PARAM_INT);
$recalculate = optional_param('recalculate', 0, PARAM_INT);

// Page URL.
$url = new moodle_url('/grade/penalty/view.php', ['contextid' => $contextid]);
if ($cmid !== null) {
    $url->param('cm', $cmid);
}
$PAGE->set_url($url);

$context = context::instance_by_id($contextid);

$courseid = $context->get_course_context()->instanceid;
$course = get_course($courseid);

$cm = null;

if (!is_null($cmid)) {
    $cm = get_coursemodule_from_id(null, $cmid, $course->id, false, MUST_EXIST);
}

require_login($course, false, $cm);

$PAGE->set_title(get_string('gradepenalty', 'core_grades'));
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->disable();

// Check if the recalculate button is clicked.
if ($recalculate) {
    // Show message for user confirmation.
    $confirmurl = new url($url->out(), [
        'contextid' => $contextid,
        'recalculateconfirm' => 1,
        'sesskey' => sesskey(),
    ]);
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('recalculatepenaltyconfirm', 'core_grades'), $confirmurl, $url);
    echo $OUTPUT->footer();
    die;

} else if (optional_param('recalculateconfirm', 0, PARAM_INT) && confirm_sesskey()) {
    \core_grades\penalty_manager::recalculate_penalty($context);
    redirect($url, get_string('recalculatepenaltysuccess', 'core_grades'), 0, notification::NOTIFY_SUCCESS);
}

// Show the page.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('gradepenalty', 'core_grades'));

// Display the penalty recalculation button at course/module context.
if ($context->contextlevel == CONTEXT_COURSE || $context->contextlevel == CONTEXT_MODULE) {
    $buttonurl = $url;
    $buttonurl->params(['contextid' => $contextid, 'recalculate' => 1]);
    echo $OUTPUT->single_button($buttonurl, get_string('recalculatepenalty', 'core_grades'), 'get',
        ['type' => 'secondary']);
    // The empty paragraph is used as a spacer.
    echo $OUTPUT->paragraph('');
}

// Penalty plugins.
$haspenaltypluginnode = false;
if ($penaltynode = $PAGE->settingsnav->find('gradepenalty', \navigation_node::TYPE_CONTAINER)) {
    foreach ($penaltynode->children as $child) {
        if ($child->display) {
            $haspenaltypluginnode = true;
            break;
        }
    }
}

if ($haspenaltypluginnode) {
    echo $OUTPUT->heading(get_string('settings'));

    // Reuse the report link template.
    echo $OUTPUT->render_from_template('core/report_link_page', ['node' => $penaltynode]);
}

echo $OUTPUT->footer();
