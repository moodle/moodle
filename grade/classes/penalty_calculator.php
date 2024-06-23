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

use core_grades\penalty_container;

/**
 * Abstract class for defining the interface between the core penalty system and penalty plugins.
 * Penalty plugins must override this class under their own namespace to receive calls from the core penalty system.
 *
 * @package   core_grades
 * @copyright 2025 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class penalty_calculator {
    /**
     * Calculate the grade penalty based on the information provided in the penalty container.
     * The result should be stored in the penalty container.
     *
     * @param penalty_container $container
     */
    abstract public static function calculate_penalty(penalty_container $container): void;
}
