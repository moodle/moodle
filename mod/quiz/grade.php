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
 * This page is the entry page into the quiz UI. Displays information about the
 * quiz to students and teachers, and lets students see their previous attempts.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');


$id = required_param('id', PARAM_INT);

if (!$cm = get_coursemodule_from_id('quiz', $id)) {
    print_error('invalidcoursemodule');
}
if (!$quiz = $DB->get_record('quiz', array('id' => $cm->instance))) {
    print_error('invalidquizid');
}
if (!$course = $DB->get_record('course', array('id' => $quiz->course))) {
    print_error('coursemisconf');
}

require_login($course, false, $cm);

$reportlist = quiz_report_list(get_context_instance(CONTEXT_MODULE, $cm->id));
if (!empty($reportlist)) {
    redirect(new moodle_url('/mod/quiz/report.php', array(
            'id' => $cm->id, 'mode' => reset($reportlist))));
} else {
    redirect(new moodle_url('/mod/quiz/view.php', array('id' =>  $cm->id)));
}
