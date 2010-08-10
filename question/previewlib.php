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
 * Library functions used by question/preview.php.
 *
 * @package    core
 * @subpackage questionengine
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Called via pluginfile.php -> question_pluginfile to serve files belonging to
 * a question in a question_attempt when that attempt is a preview.
 *
 * @param object $course course settings object
 * @param object $context context object
 * @param string $component the name of the component we are serving files for.
 * @param string $filearea the name of the file area.
 * @param array $args the remaining bits of the file path.
 * @param bool $forcedownload whether the user must be forced to download the file.
 * @return bool false if file not found, does not return if found - justsend the file
 */
function question_preview_question_pluginfile($course, $context, $component,
        $filearea, $attemptid, $questionid, $args, $forcedownload) {
    global $USER, $SESSION, $DB, $CFG;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    if (!$question = $DB->get_record('question', array('id' => $questionid))) {
        return send_file_not_found();
    }

    if (!question_has_capability_on($question, 'use', $question->category)) {
        send_file_not_found();
    }

    if (!isset($SESSION->quizpreview->states) || $SESSION->quizpreview->questionid != $questionid) {
        send_file_not_found();
    }

    $states = end($SESSION->quizpreview->states);
    if (!array_key_exists($question->id, $states)) {
        send_file_not_found();
    }
    $state = $states[$question->id];

    // Build fake cmoptions
    $quiz = new cmoptions;
    $quiz->id = 0;
    $quiz->review = get_config('quiz', 'review');
    if (empty($course->id)) {
        $quiz->course = SITEID;
    } else {
        $quiz->course = $course->id;
    }
    $quiz->decimalpoints = get_config('quiz', 'decimalpoints');

    $questions[$question->id] = $question;
    get_question_options($questions);

    // Build fake attempt
    $timenow = time();
    $attempt = new stdclass;
    $attempt->quiz = $quiz->id;
    $attempt->userid = $USER->id;
    $attempt->attempt = 0;
    $attempt->sumgrades = 0;
    $attempt->timestart = $timenow;
    $attempt->timefinish = 0;
    $attempt->timemodified = $timenow;
    $attempt->uniqueid = 0;
    $attempt->id = 0;
    $attempt->layout = $question->id;

    $options = quiz_get_renderoptions($quiz, $attempt, $context, $state);
    $options->noeditlink = true;
    // XXX: mulitichoice type needs quiz id to get maxgrade
    $options->quizid = 0;

    if (!question_check_file_access($question, $state, $options, $context->id, $component,
            $filearea, $args, $forcedownload)) {
        send_file_not_found();
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/$component/$filearea/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload);
}
