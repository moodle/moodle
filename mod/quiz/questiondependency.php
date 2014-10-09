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
 * Set question dependency.
 *
 * @package   mod_quiz
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$cmid = required_param('cmid', PARAM_INT);
$quizid = required_param('quizid', PARAM_INT);
$slotid = required_param('slotid', PARAM_INT);

require_sesskey();
$quizobj = quiz::create($quizid);
require_login($quizobj->get_course(), false, $quizobj->get_cm());
require_capability('mod/quiz:manage', $quizobj->get_context());

$structure = $quizobj->get_structure();

// Update dependency settings on this slot.
$slot = $structure->get_slot_by_id($slotid);
$structure->update_question_dependency($slot);

redirect(new moodle_url('edit.php', array('cmid' => $quizobj->get_cmid())));
