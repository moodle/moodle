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
 * Plagiarism Turnitin tests.
 *
 * @package    plagiarism_turnitin
 * @copyright  2016 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_competency\course_competency;

/**
 * Plagiarism Turnitin tests.
 *
 * @package    plagiarism_turnitin
 * @copyright  2016 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plagiarism_turnitin_testcase extends advanced_testcase {
    /**
     * Isolates a problem found running core tests in Moodle 31.
     */
    public function test_problem_moodle31_coretests() {
        $this->resetAfterTest();

        $c1 = $this->getDataGenerator()->create_course();

        reset_course_userdata((object)['id' => $c1->id, 'reset_competency_ratings' => true]);
    }
}
