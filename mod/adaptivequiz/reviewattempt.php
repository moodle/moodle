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
 * Page to view info about a certain attempt.
 *
 * @package    mod_adaptivequiz
 * @copyright  2013 Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/tag/lib.php');
require_once($CFG->dirroot . '/mod/adaptivequiz/locallib.php');

$attemptid = required_param('attempt', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$tab = optional_param('tab', 'attemptsummary', PARAM_ALPHA);

$attempt = $DB->get_record('adaptivequiz_attempt', ['id' => $attemptid], '*', MUST_EXIST);
$adaptivequiz = $DB->get_record('adaptivequiz', ['id' => $attempt->instance], '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('adaptivequiz', $adaptivequiz->id, $adaptivequiz->course, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $adaptivequiz->course], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/adaptivequiz:viewreport', $context);

$user = $DB->get_record('user', ['id' => $attempt->userid], '*', MUST_EXIST);
$quba = question_engine::load_questions_usage_by_activity($attempt->uniqueid);

$a = new stdClass();
$a->quizname = format_string($adaptivequiz->name);
$a->fullname = fullname($user);
$a->finished = userdate($attempt->timemodified);
$title = get_string('reportattemptreviewpageheading', 'adaptivequiz', $a);

$PAGE->set_url('/mod/adaptivequiz/reviewattempt.php', ['attempt' => $attempt->id, 'tab' => $tab]);
$PAGE->set_title($title);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->navbar->add(get_string('reports'));
$PAGE->navbar->add(get_string('reportuserattemptstitleshort', 'adaptivequiz', fullname($user)),
    new moodle_url('/mod/adaptivequiz/viewattemptreport.php', ['userid' => $user->id, 'cmid' => $cm->id]));
$PAGE->navbar->add(get_string('reviewattempt', 'adaptivequiz'));

$renderer = $PAGE->get_renderer('mod_adaptivequiz');

echo $renderer->print_header();
echo $renderer->heading($title);

echo $renderer->attempt_review_tabs($PAGE->url, $tab);
echo $renderer->attempt_report_page_by_tab($tab, $adaptivequiz, $attempt, $user, $quba, $PAGE->url, $page);

echo $renderer->print_footer();
