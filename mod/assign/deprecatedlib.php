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
 * List of deprecated mod_assign functions.
 *
 * @package   mod_assign
 * @copyright 2021 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @deprecated since Moodle 3.11
 */
function assign_get_completion_state() {
    $completionclass = \mod_assign\completion\custom_completion::class;
    throw new coding_exception(__FUNCTION__ . "() has been removed, please use the '{$completionclass}' class instead");
}

/**
 * @deprecated since Moodle 3.3, when the block_course_overview block was removed.
 */
function assign_print_overview() {
    throw new coding_exception('assign_print_overview() can not be used any more and is obsolete.');
}

/**
 * @deprecated since Moodle 3.3, when the block_course_overview block was removed.
 */
function assign_get_mysubmission_details_for_print_overview() {
    throw new coding_exception('assign_get_mysubmission_details_for_print_overview() can not be used any more and is obsolete.');
}

/**
 * @deprecated since Moodle 3.3, when the block_course_overview block was removed.
 */
function assign_get_grade_details_for_print_overview() {
    throw new coding_exception('assign_get_grade_details_for_print_overview() can not be used any more and is obsolete.');
}

/**
 * @deprecated since Moodle 3.8
 */
function assign_scale_used() {
    throw new coding_exception('assign_scale_used() can not be used anymore. Plugins can implement ' .
        '<modname>_scale_used_anywhere, all implementations of <modname>_scale_used are now ignored');
}
