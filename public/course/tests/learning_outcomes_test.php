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

namespace core_course;

use grade_outcome;

/**
 * Tests for the course learning outcomes page's mapping of outcomes to activities.
 *
 * @package   core_course
 * @category  test
 * @copyright 2026 David Woloszyn <david.woloszyn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_outcomes_test extends \advanced_testcase {
    /**
     * Only the activities actually mapped to an outcome are returned, whether the outcome
     * is scaled (mapped via a grade item) or scale-less (mapped via grade_outcomes_modules).
     *
     * @covers \grade_outcome::get_modules_mapped_to_course_outcomes
     */
    public function test_multiple_modules_mapped_to_multiple_course_outcomes(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enableoutcomes = 1;

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $scale  = $generator->create_scale(['scale' => 'Poor,Good', 'courseid' => $course->id]);

        // Create some outcomes.
        $scaledoutcome = $this->create_scaled_outcome($course->id, $scale->id);
        $scalelessoutcome = $this->create_scaleless_outcome($course->id);

        // Create a module and map it to a scaled outcome.
        $generator->create_module('assign', [
            'course' => $course->id,
            'outcome_' . $scaledoutcome->id => 1,
        ]);

        // Create a couple modules and map it to a scale-less outcome.
        $generator->create_module('assign', [
            'course' => $course->id,
            'outcome_' . $scalelessoutcome->id => 1,
        ]);
        $generator->create_module('assign', [
            'course' => $course->id,
            'outcome_' . $scalelessoutcome->id => 1,
        ]);

        // Create a module and don't map it to anything.
        $unmapped = $generator->create_module('assign', ['course' => $course->id]);
        $unmappedcm = get_coursemodule_from_instance('assign', $unmapped->id, $course->id);

        $scaledcmids = grade_outcome::get_modules_mapped_to_course_outcomes((int)$scaledoutcome->id, (int)$course->id);
        $scalelesscmids = grade_outcome::get_modules_mapped_to_course_outcomes((int)$scalelessoutcome->id, (int)$course->id);

        $this->assertCount(1, $scaledcmids);
        $this->assertCount(2, $scalelesscmids);

        $this->assertNotContains((int)$unmappedcm->id, $scaledcmids);
        $this->assertNotContains((int)$unmappedcm->id, $scalelesscmids);
    }

    /**
     * A single activity mapped to multiple outcomes (scaled and scale-less) is returned
     * for each outcome it is mapped to.
     *
     * @covers \grade_outcome::get_modules_mapped_to_course_outcomes
     */
    public function test_singular_module_mapped_to_multiple_course_outcomes(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enableoutcomes = 1;

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $scale  = $generator->create_scale(['scale' => 'Poor,Good', 'courseid' => $course->id]);

        // Create some outcomes.
        $scaledoutcome = $this->create_scaled_outcome($course->id, $scale->id);
        $scalelessoutcomeone = $this->create_scaleless_outcome($course->id, 'noscale1');
        $scalelessoutcometwo = $this->create_scaleless_outcome($course->id, 'noscale2');

        // Create a single module and map it to all three outcomes at once.
        $generator->create_module('assign', [
            'course' => $course->id,
            'outcome_' . $scaledoutcome->id => 1,
            'outcome_' . $scalelessoutcomeone->id => 1,
            'outcome_' . $scalelessoutcometwo->id => 1,
        ]);

        // The module must be returned when queried against each of its three outcomes.
        $totalmappings = 0;
        foreach ([$scaledoutcome, $scalelessoutcomeone, $scalelessoutcometwo] as $outcome) {
            $cmids = grade_outcome::get_modules_mapped_to_course_outcomes((int)$outcome->id, (int)$course->id);
            $totalmappings += count($cmids);
        }

        $this->assertEquals(3, $totalmappings);
    }

    /**
     * Creates a scaled outcome for the given course.
     *
     * @param int $courseid The course ID.
     * @param int $scaleid The scale ID.
     * @param string $shortname The outcome shortname.
     * @return \stdClass The created outcome record.
     */
    private function create_scaled_outcome(int $courseid, int $scaleid, string $shortname = 'scaled'): \stdClass {
        return $this->getDataGenerator()->create_grade_outcome([
            'courseid' => $courseid,
            'shortname' => $shortname,
            'fullname' => 'Scaled outcome',
            'scaleid' => $scaleid,
        ]);
    }

    /**
     * Creates a scale-less outcome for the given course.
     *
     * @param int $courseid The course ID.
     * @param string $shortname The outcome shortname.
     * @return grade_outcome The created outcome.
     */
    private function create_scaleless_outcome(int $courseid, string $shortname = 'noscale'): grade_outcome {
        $outcome = new grade_outcome();
        $outcome->courseid = $courseid;
        $outcome->shortname = $shortname;
        $outcome->fullname = 'No scale outcome';
        $outcome->insert();
        return $outcome;
    }
}
