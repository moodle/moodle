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

namespace mod_assign\penalty;

use assign;
use core\context\module as context_module;
use core_grades\penalty_manager;
use grade_item;

/**
 * Helper class for penalty in assignment module.
 *
 * @package   mod_assign
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Check if penalty is enabled for an assignment.
     *
     * @param int $assignid The assignment id.
     */
    public static function is_penalty_enabled(int $assignid): bool {
        // Get the assignment course module.
        $cm = get_coursemodule_from_instance('assign', $assignid);
        $context = context_module::instance($cm->id);

        // Get the assignment instance.
        $assign = new assign($context, $cm, $cm->course);

        // Check if due date is set.
        if (!$assign->get_instance()->duedate) {
            return false;
        }

        // Check if the grade type is set to GRADE_TYPE_VALUE (grade 1 to 100).
        if ($assign->get_instance()->grade < GRADE_TYPE_VALUE) {
            return false;
        }

        // Check if the assignment is set to use penalty.
        if (!$assign->get_instance()->gradepenalty) {
            return false;
        }

        return true;
    }

    /**
     * Apply penalty to a user.
     *
     * @param int $assignid The assignment id.
     * @param int $userid The user id.
     */
    public static function apply_penalty_to_user(int $assignid, int $userid): void {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        // Check if penalty is enabled for this assignment.
        if (!self::is_penalty_enabled($assignid)) {
            return;
        }

        // Get the assignment course module.
        $cm = get_coursemodule_from_instance('assign', $assignid);
        $context = context_module::instance($cm->id);

        // Get the assignment instance.
        $assign = new assign($context, $cm, $cm->course);

        // Find the graded attempt.
        $sql = "SELECT MAX(attemptnumber) as attemptnumber
                  FROM {assign_grades}
                 WHERE assignment = :assignid
                   AND userid = :userid
                   AND grade >= 0";
        $assigngrade = $DB->get_record_sql($sql, ['assignid' => $assignid, 'userid' => $userid]);

        // Get the submission.
        if ($assign->get_instance()->teamsubmission) {
            $submission = $assign->get_group_submission($userid, 0, false, $assigngrade->attemptnumber);
        } else {
            $submission = $assign->get_user_submission($userid, false, $assigngrade->attemptnumber);
        }

        // Check if the submission is null.
        if ($submission === null) {
            debugging(
                "Submission not found for user {$userid} in assignment {$assignid} attempt {$assigngrade->attemptnumber}",
            );
            return;
        }

        // Get submission date.
        $submissiondate = $submission->timemodified;

        // Check if we have valid submission date.
        if (empty($submissiondate)) {
            debugging(
                "Invalid submission date for user {$userid} in assignment {$assignid} attempt {$assigngrade->attemptnumber}",
            );
            return;
        }

        // Get the due date from the override if it exists. Otherwise, retrieve the date from the assignment settings.
        $duedate = $assign->override_exists($userid)->duedate ?? $assign->get_instance()->duedate;

        // Get extension.
        $userflags = $assign->get_user_flags($userid, false);
        if (!empty($userflags)) {
            $duedate = max($userflags->extensionduedate, $duedate);
        }

        // Get grade item.
        $gradeitem = grade_item::fetch([
            'courseid' => $assign->get_course()->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assign->get_instance()->id,
            'itemnumber' => 0,
        ]);

        // Apply penalty.
        $container = penalty_manager::apply_grade_penalty_to_user($userid, $gradeitem, $submissiondate, $duedate);
        if ($container->get_grade_before_penalties() == 0) {
            // There is no deduction applied to grade 0.
            $deductedpercentage = 0;
        } else {
            $deductedpercentage = $container->get_penalty() / $container->get_grade_before_penalties() * 100;
        }

        // Store the assign grade penalty.
        $DB->set_field_select(
            'assign_grades',
            'penalty',
            $deductedpercentage,
            'assignment = :assignid AND userid = :userid AND attemptnumber = :attemptnumber',
            ['assignid' => $assignid, 'userid' => $userid, 'attemptnumber' => $assigngrade->attemptnumber],
        );
    }
}
