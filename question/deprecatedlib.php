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
 * List of deprecated question functions.
 *
 * @package   core_question
 * @copyright 2024 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function question_is_only_child_of_top_category_in_context() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function question_is_top_category() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function question_can_delete_cat() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function print_choose_qtype_to_add_form() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function create_new_question_button() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function core_question_output_fragment_tags_form() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function question_preview_question_pluginfile() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function question_preview_action_url() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function question_preview_form_url() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function restart_preview() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}
