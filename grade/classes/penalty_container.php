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

namespace core_grades;

use grade_grade;
use grade_item;
use moodle_exception;

/**
 * An object for storing and aggregating penalty information.
 *
 * @package   core_grades
 * @copyright 2025 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class penalty_container {
    /** @var float $penalty The number of points to deduct from the grade */
    private float $penalty = 0.0;

    /**
     * Constructor for the class.
     *
     * @param grade_item $gradeitem The grade item object
     * @param grade_grade $gradegrade The grade object
     * @param int $submissiondate The date and time the submission was made
     * @param int $duedate The date and time the activity is due
     */
    public function __construct(
        /** @var grade_item $gradeitem The grade item object*/
        private readonly grade_item $gradeitem,

        /** @var grade_grade $gradegrade The grade object */
        private readonly grade_grade $gradegrade,

        /** @var int $submissiondate The date and time the submission was made */
        private readonly int $submissiondate,

        /** @var int $duedate The date and time the activity is due */
        private readonly int $duedate,
    ) {
    }

    /**
     * Get the user id.
     *
     * @return int The user id
     */
    public function get_userid(): int {
        return $this->gradegrade->userid;
    }

    /**
     * Get the submission date.
     *
     * @return int The date and time the submission was made
     */
    public function get_submission_date(): int {
        return $this->submissiondate;
    }

    /**
     * Get the due date.
     *
     * @return int The date and time the activity is due
     */
    public function get_due_date(): int {
        return $this->duedate;
    }

    /**
     * Get the grade item object.
     * This object should not be modified.
     *
     * @return grade_item The grade item object
     */
    public function get_grade_item(): grade_item {
        return $this->gradeitem;
    }

    /**
     * Get the grade object.
     * This object should not be modified.
     *
     * @return grade_grade The grade object
     */
    public function get_grade_grade(): grade_grade {
        return $this->gradegrade;
    }

    /**
     * Get the grade before penalties are applied.
     *
     * @return float The grade before penalties are applied
     */
    public function get_grade_before_penalties(): float {
        return $this->gradegrade->finalgrade;
    }

    /**
     * Get the penalised grade.
     *
     * The penalised grade is clamped between the minimum and maximum grade for the grade item.
     *
     * @return float The penalised grade
     */
    public function get_grade_after_penalties(): float {
        // Prevent grades from becoming out of bounds which would otherwise be a fairly common occurrence.
        return self::clamp(
            $this->get_grade_before_penalties() - $this->penalty,
            $this->get_min_grade(),
            $this->get_max_grade()
        );
    }

    /**
     * Get the current penalty value.
     *
     * @return float The number of points to deduct from the grade
     */
    public function get_penalty(): float {
        return $this->penalty;
    }

    /**
     * Get the minimum grade for the grade item.
     *
     * @return float The minimum grade for the grade item
     */
    public function get_min_grade(): int {
        return $this->gradeitem->grademin;
    }

    /**
     * Get the maximum grade for the grade item.
     *
     * @return float The maximum grade for the grade item
     */
    public function get_max_grade(): float {
        return $this->gradeitem->grademax;
    }

    /**
     * Aggregate the number of points to deduct from the grade.
     * Each penalty plugin is expected to call this method from their calculate_penalty() method.
     *
     * For example, if a grade item has a maximum grade of 200 and a penalty plugin wants to deduct 10% from the maximum grade,
     * the penalty plugin should call this method with a penalty value of 20.
     *
     * Percentages must not be passed to this method. Any percentage values must be converted to points before calling this method.
     * Penalty values cannot be negative or an exception will be thrown.
     * After all penalty plugins have been called, the core penalty system will apply the aggregated penalty to the grade,
     * clamping the grade between the minimum and maximum grade for the grade item.
     *
     * @param float $penalty The number of points to deduct from the grade
     * @throws moodle_exception Thrown if the penalty value is negative
     */
    public function aggregate_penalty(float $penalty): void {
        if ($penalty < 0.0) {
            throw new moodle_exception('errornegativepenalty', 'core_grades', '', $this->get_grade_grade()->id);
        }

        $this->penalty += $penalty;
    }

    /**
     * Clamp a value between a minimum and maximum value.
     *
     * @param float $value The value to clamp
     * @param float $min The minimum value
     * @param float $max The maximum value
     * @return float The clamped value
     */
    private static function clamp(float $value, float $min, float $max): float {
        return max($min, min($max, $value));
    }
}
