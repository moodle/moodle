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
 * Confirmation page to close a student attempt.
 *
 * @copyright  2013 Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/adaptivequiz/locallib.php');

use mod_adaptivequiz\local\attempt\attempt_state;

$attemptid = required_param('attempt', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$attempt = $DB->get_record('adaptivequiz_attempt', ['id' => $attemptid], '*', MUST_EXIST);
$adaptivequiz = $DB->get_record('adaptivequiz', ['id' => $attempt->instance], '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('adaptivequiz', $adaptivequiz->id, $adaptivequiz->course, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $adaptivequiz->course], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/adaptivequiz:viewreport', $context);

$returnurl = new moodle_url('/mod/adaptivequiz/viewattemptreport.php', ['cmid' => $cm->id, 'userid' => $attempt->userid]);

if ($attempt->attemptstate == attempt_state::COMPLETED) {
    throw new moodle_exception('errorclosingattempt_alreadycomplete', 'adaptivequiz', $returnurl);
}

$user = $DB->get_record('user', ['id' => $attempt->userid], '*', MUST_EXIST);

$PAGE->set_url('/mod/adaptivequiz/closeattempt.php', ['attempt' => $attempt->id]);
$PAGE->set_title(format_string($adaptivequiz->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$renderer = $PAGE->get_renderer('mod_adaptivequiz');

$performancecalculation = new stdClass();
$performancecalculation->measure = $attempt->measure;
$performancecalculation->stderror = $attempt->standarderror;
$performancecalculation->lowestlevel = $adaptivequiz->lowestlevel;
$performancecalculation->highestlevel = $adaptivequiz->highestlevel;

$a = new stdClass();
$a->name = fullname($user);
$a->started = userdate($attempt->timecreated);
$a->modified = userdate($attempt->timemodified);
$a->num_questions = format_string($attempt->questionsattempted);
$a->measure = $renderer->format_measure($performancecalculation);
$a->standarderror = $renderer->format_standard_error($performancecalculation);
$a->current_user_name = fullname($USER);
$a->current_user_id = format_string($USER->id);
$a->now = userdate(time());

if ($confirm) {
    $statusmessage = get_string('attemptclosedstatus', 'adaptivequiz', $a);
    $closemessage = get_string('attemptclosed', 'adaptivequiz', $a);

    adaptivequiz_complete_attempt($attempt->uniqueid, $adaptivequiz, $context, $attempt->userid, $attempt->standarderror,
        $statusmessage);
    redirect($returnurl, $closemessage, 4);
}

$message = html_writer::tag('p', get_string('confirmcloseattempt', 'adaptivequiz', $a)) .
    html_writer::tag('p', get_string('confirmcloseattemptstats', 'adaptivequiz', $a)) .
    html_writer::tag('p', get_string('confirmcloseattemptscore', 'adaptivequiz', $a));

$confirm = new moodle_url('/mod/adaptivequiz/closeattempt.php', ['attempt' => $attempt->id, 'confirm' => 1]);

echo $renderer->header();
echo $renderer->confirm($message, $confirm, $returnurl);
echo $renderer->footer();
