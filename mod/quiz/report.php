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
 * This script controls the display of the quiz reports.
 *
 * @package   mod_quiz
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');
require_once($CFG->dirroot . '/mod/quiz/report/default.php');

$id = optional_param('id', 0, PARAM_INT);
$q = optional_param('q', 0, PARAM_INT);
$mode = optional_param('mode', '', PARAM_ALPHA);

if ($id) {
    if (!$cm = get_coursemodule_from_id('quiz', $id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        throw new \moodle_exception('coursemisconf');
    }
    if (!$quiz = $DB->get_record('quiz', array('id' => $cm->instance))) {
        throw new \moodle_exception('invalidcoursemodule');
    }

} else {
    if (!$quiz = $DB->get_record('quiz', array('id' => $q))) {
        throw new \moodle_exception('invalidquizid', 'quiz');
    }
    if (!$course = $DB->get_record('course', array('id' => $quiz->course))) {
        throw new \moodle_exception('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }
}

$url = new moodle_url('/mod/quiz/report.php', array('id' => $cm->id));
if ($mode !== '') {
    $url->param('mode', $mode);
}
$PAGE->set_url($url);

require_login($course, false, $cm);
$context = context_module::instance($cm->id);
$PAGE->set_pagelayout('report');
$PAGE->activityheader->disable();
$reportlist = quiz_report_list($context);
if (empty($reportlist)) {
    throw new \moodle_exception('erroraccessingreport', 'quiz');
}

// Validate the requested report name.
if ($mode == '') {
    // Default to first accessible report and redirect.
    $url->param('mode', reset($reportlist));
    redirect($url);
} else if (!in_array($mode, $reportlist)) {
    throw new \moodle_exception('erroraccessingreport', 'quiz');
}
if (!is_readable("report/$mode/report.php")) {
    throw new \moodle_exception('reportnotfound', 'quiz', '', $mode);
}

// Open the selected quiz report and display it.
$file = $CFG->dirroot . '/mod/quiz/report/' . $mode . '/report.php';
if (is_readable($file)) {
    include_once($file);
}
$reportclassname = 'quiz_' . $mode . '_report';
if (!class_exists($reportclassname)) {
    throw new \moodle_exception('preprocesserror', 'quiz');
}

$report = new $reportclassname();
$report->display($quiz, $cm, $course);

// Print footer.
echo $OUTPUT->footer();

// Log that this report was viewed.
$params = array(
    'context' => $context,
    'other' => array(
        'quizid' => $quiz->id,
        'reportname' => $mode
    )
);
$event = \mod_quiz\event\report_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('quiz', $quiz);
$event->trigger();
