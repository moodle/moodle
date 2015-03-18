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
 * Rest endpoint for ajax editing for paging operations on the quiz structure.
 *
 * @package   mod_quiz
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$quizid = required_param('quizid', PARAM_INT);
$slotnumber = required_param('slot', PARAM_INT);
$repagtype = required_param('repag', PARAM_INT);

require_sesskey();
$quizobj = quiz::create($quizid);
require_login($quizobj->get_course(), false, $quizobj->get_cm());
require_capability('mod/quiz:manage', $quizobj->get_context());
if (quiz_has_attempts($quizid)) {
    $reportlink = quiz_attempt_summary_link_to_reports($quizobj->get_quiz(),
                    $quizobj->get_cm(), $quizobj->get_context());
    throw new \moodle_exception('cannoteditafterattempts', 'quiz',
            new moodle_url('/mod/quiz/edit.php', array('cmid' => $quizobj->get_cmid())), $reportlink);
}

$slotnumber++;
$repage = new \mod_quiz\repaginate($quizid);
$repage->repaginate_slots($slotnumber, $repagtype);

$structure = $quizobj->get_structure();
$slots = $structure->refresh_page_numbers_and_update_db($structure->get_quiz());

redirect(new moodle_url('edit.php', array('cmid' => $quizobj->get_cmid())));
