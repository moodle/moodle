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
 * mod_workshop fixtures
 *
 * @package    mod_workshop
 * @category   phpunit
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class testable_workshop extends workshop {

    public function aggregate_submission_grades_process(array $assessments) {
        parent::aggregate_submission_grades_process($assessments);
    }

    public function aggregate_grading_grades_process(array $assessments, $timegraded = null) {
        parent::aggregate_grading_grades_process($assessments, $timegraded);
    }

}
