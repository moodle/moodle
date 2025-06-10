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
 * Adaptive quiz attempt script
 *
 * @copyright  2013 Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/adaptivequiz/locallib.php');

use mod_adaptivequiz\output\ability_measure;

$cmid = required_param('cmid', PARAM_INT); // Course module id.
$instance = required_param('id', PARAM_INT); // Activity instance id.
$uniqueid = required_param('uattid', PARAM_INT); // Attempt unique id.

if (!$cm = get_coursemodule_from_id('adaptivequiz', $cmid)) {
    throw new moodle_exception('invalidcoursemodule');
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    throw new moodle_exception('coursemisconf');
}

$adaptivequiz = $DB->get_record('adaptivequiz', ['id' => $cm->instance], '*', MUST_EXIST);

$abilitymeasurerenderable = null;
if ($adaptivequiz->showabilitymeasure) {
    $abilitymeasurevalue = $DB->get_field('adaptivequiz_attempt', 'measure', ['uniqueid' => $uniqueid], MUST_EXIST);
    $abilitymeasurerenderable = ability_measure::of_attempt_on_adaptive_quiz($adaptivequiz, $abilitymeasurevalue);
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// TODO - check if user has capability to attempt.

// Check if this is the owner of the attempt.
$validattempt = adaptivequiz_uniqueid_part_of_attempt($uniqueid, $instance, $USER->id);

// Display an error message if this is not the owner of the attempt.
if (!$validattempt) {
    $url = new moodle_url('/mod/adaptivequiz/attempt.php', array('cmid' => $cm->id));
    throw new moodle_exception('notyourattempt', 'adaptivequiz', $url);
}

$PAGE->set_url('/mod/adaptivequiz/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($adaptivequiz->name));
$PAGE->set_context($context);
$PAGE->activityheader->disable();
$PAGE->add_body_class('limitedwidth');

$output = $PAGE->get_renderer('mod_adaptivequiz');

// Init secure window if enabled.
$popup = false;
if (!empty($adaptivequiz->browsersecurity)) {
    $PAGE->blocks->show_only_fake_blocks();
    $output->init_browser_security(false);
    $popup = true;
} else {
    $PAGE->set_heading(format_string($course->fullname));
}

echo $output->header();
echo $output->attempt_feedback($adaptivequiz->attemptfeedback, $cm->id, $abilitymeasurerenderable, $popup);
echo $output->footer();
