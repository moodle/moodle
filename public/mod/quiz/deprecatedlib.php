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
 * List of deprecated mod_quiz functions.
 *
 * @package   mod_quiz
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\quiz_settings;

defined('MOODLE_INTERNAL') || die();

/**
 * @deprecated in 4.1 use mod_quiz\structure::has_use_capability(...) instead. MDL-76898
 */
#[\core\attribute\deprecated('mod_quiz\structure::has_use_capability()', since: '4.1', mdl: 'MDL-76898', final: true)]
function quiz_has_question_use($quiz, $slot) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.2. Please use grade_calculator::recompute_quiz_sumgrades.
 */
#[\core\attribute\deprecated('grade_calculator::recompute_quiz_sumgrades()', since: '4.2', mdl: 'MDL-76897', final: true)]
function quiz_update_sumgrades($quiz) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.2. Please use grade_calculator::recompute_all_attempt_sumgrades.
 */
#[\core\attribute\deprecated('grade_calculator::recompute_all_attempt_sumgrades()', since: '4.2', mdl: 'MDL-76897', final: true)]
function quiz_update_all_attempt_sumgrades($quiz) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.2. Please use grade_calculator::recompute_all_final_grades.
 */
#[\core\attribute\deprecated('grade_calculator::recompute_all_final_grades()', since: '4.2', mdl: 'MDL-76897', final: true)]
function quiz_update_all_final_grades($quiz) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.2. Please use grade_calculator::update_quiz_maximum_grade.
 */
#[\core\attribute\deprecated('grade_calculator::update_quiz_maximum_grade()', since: '4.2', mdl: 'MDL-76897', final: true)]
function quiz_set_grade($newgrade, $quiz) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.2. Please use grade_calculator::recompute_final_grade.
 */
#[\core\attribute\deprecated('grade_calculator::recompute_final_grade()', since: '4.2', mdl: 'MDL-76897', final: true)]
function quiz_save_best_grade($quiz, $userid = null, $attempts = []) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.2. No direct replacement.
 */
#[\core\attribute\deprecated(null, since: '4.2', mdl: 'MDL-76897', final: true)]
function quiz_calculate_best_grade($quiz, $attempts) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.2. No direct replacement.
 */
#[\core\attribute\deprecated(null, since: '4.2', mdl: 'MDL-76897', final: true)]
function quiz_calculate_best_attempt($quiz, $attempts) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.4 MDL-80300
 */
#[\core\attribute\deprecated('override_manager::delete_override_by_id', since: '4.4', mdl: '80300', final: true)]
function quiz_delete_override($quiz, $overrideid, $log = true) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
    return true;
}

/**
 * @deprecated since Moodle 4.4 MDL-80300
 */
#[\core\attribute\deprecated('override_manager::delete_all_overrides', since: '4.4', mdl: '80300', final: true)]
function quiz_delete_all_overrides($quiz, $log = true) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated Since Moodle 4.3 MDL-72321
 */
#[\core\attribute\deprecated('mod_quiz\structure::add_random_questions()', since: '4.3', mdl: 'MDL-72321', final: true)]
function quiz_add_random_questions(stdClass $quiz, int $addonpage, int $categoryid, int $number): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}
