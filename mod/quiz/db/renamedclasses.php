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
 * Lists renamed classes so that the autoloader can make the old names still work.
 *
 * @package   mod_quiz
 * @copyright 2014 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Array 'old_class_name' => 'new\class_name'.
$renamedclasses = array(

    // Changed in Moodle 2.8.
    'quiz_question_bank_view'                 => 'mod_quiz\question\bank\custom_view',
    'question_bank_add_to_quiz_action_column' => 'mod_quiz\question\bank\add_action_column',
    'question_bank_question_name_text_column' => 'mod_quiz\question\bank\question_name_text_column',
);
