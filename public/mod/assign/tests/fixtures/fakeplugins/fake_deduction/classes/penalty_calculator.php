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

namespace gradepenalty_fake_deduction;

use core_grades\penalty_container;

/**
 * Penalty plugins must override this class to implement their own penalty calculation.
 *
 * @package   mod_assign
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class penalty_calculator extends \core_grades\penalty_calculator {
    /**
     * Calculate the penalty for the given activity.
     *
     * @param penalty_container $container The penalty container.
     */
    public static function calculate_penalty(penalty_container $container): void {

        // Dates.
        debugging('Submission date: ' . $container->get_submission_date());
        debugging('Due date: ' . $container->get_due_date());

        // Calculate the deducted grade based on the max grade.
        if ($container->get_submission_date() > $container->get_due_date()) {
            $deductedgrade = $container->get_max_grade() * 10 / 100;
            $container->aggregate_penalty($deductedgrade);
        }
    }
}
