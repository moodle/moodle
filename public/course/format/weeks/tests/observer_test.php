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

namespace format_weeks;

/**
 * Unit tests for the event observers used by the weeks course format.
 *
 * @package format_weeks
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class observer_test extends \advanced_testcase {

    /**
     * Test setup.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Tests when we update a course with automatic end date set.
     */
    public function test_course_updated_with_automatic_end_date(): void {
        global $DB;

        // Generate a course with some sections.
        $numsections = 6;
        $startdate = time();
        $course = $this->getDataGenerator()->create_course(array(
            'numsections' => $numsections,
            'format' => 'weeks',
            'startdate' => $startdate,
            'automaticenddate' => 1));

        // Ok, let's update the course start date.
        $newstartdate = $startdate + WEEKSECS;
        update_course((object)['id' => $course->id, 'startdate' => $newstartdate]);

        // Get the updated course end date.
        $enddate = $DB->get_field('course', 'enddate', array('id' => $course->id));

        $format = course_get_format($course->id);
        $this->assertEquals($numsections, $format->get_last_section_number());
        $this->assertEquals($newstartdate, $format->get_course()->startdate);
        $dates = $format->get_section_dates($numsections);
        $this->assertEquals($dates->end, $enddate);
    }

    /**
     * Tests when we update a course with automatic end date set but no actual change is made.
     */
    public function test_course_updated_with_automatic_end_date_no_change(): void {
        global $DB;

        // Generate a course with some sections.
        $course = $this->getDataGenerator()->create_course(array(
            'numsections' => 6,
            'format' => 'weeks',
            'startdate' => time(),
            'automaticenddate' => 1));

        // Get the end date from the DB as the results will have changed from $course above after observer processing.
        $createenddate = $DB->get_field('course', 'enddate', array('id' => $course->id));

        // Ok, let's update the course - but actually not change anything.
        update_course((object)['id' => $course->id]);

        // Get the updated course end date.
        $updateenddate = $DB->get_field('course', 'enddate', array('id' => $course->id));

        // Confirm nothing changed.
        $this->assertEquals($createenddate, $updateenddate);
    }

    /**
     * Tests when we update a course without automatic end date set.
     */
    public function test_course_updated_without_automatic_end_date(): void {
        global $DB;

        // Generate a course with some sections.
        $startdate = time();
        $enddate = $startdate + WEEKSECS;
        $course = $this->getDataGenerator()->create_course(array(
            'numsections' => 6,
            'format' => 'weeks',
            'startdate' => $startdate,
            'enddate' => $enddate,
            'automaticenddate' => 0));

        // Ok, let's update the course start date.
        $newstartdate = $startdate + WEEKSECS;
        update_course((object)['id' => $course->id, 'startdate' => $newstartdate]);

        // Get the updated course end date.
        $updateenddate = $DB->get_field('course', 'enddate', array('id' => $course->id));

        // Confirm nothing changed.
        $this->assertEquals($enddate, $updateenddate);
    }

    /**
     * Tests when we adding a course section with automatic end date set.
     */
    public function test_course_section_created_with_automatic_end_date(): void {
        global $DB;

        $numsections = 6;
        $course = $this->getDataGenerator()->create_course(array(
            'numsections' => $numsections,
            'format' => 'weeks',
            'startdate' => time(),
            'automaticenddate' => 1));

        // Add a section to the course.
        course_create_section($course->id);

        // Get the updated course end date.
        $enddate = $DB->get_field('course', 'enddate', array('id' => $course->id));

        $format = course_get_format($course->id);
        $dates = $format->get_section_dates($numsections + 1);

        // Confirm end date was updated.
        $this->assertEquals($enddate, $dates->end);
    }

    /**
     * Tests when we update a course without automatic end date set.
     */
    public function test_create_section_without_automatic_end_date(): void {
        global $DB;

        // Generate a course with some sections.
        $startdate = time();
        $enddate = $startdate + WEEKSECS;
        $course = $this->getDataGenerator()->create_course(array(
            'numsections' => 6,
            'format' => 'weeks',
            'startdate' => $startdate,
            'enddate' => $enddate,
            'automaticenddate' => 0));

        // Delete automatic end date from the database.
        $DB->delete_records('course_format_options', ['courseid' => $course->id, 'name' => 'automaticenddate']);

        // Create a new section.
        course_create_section($course->id, 0);

        // Get the updated course end date.
        $updateenddate = $DB->get_field('course', 'enddate', array('id' => $course->id));

        // Confirm enddate is automatic now - since automatic end date is not set it is assumed default (which is '1').
        $format = course_get_format($course->id);
        $this->assertEquals(7, $format->get_last_section_number());
        $dates = $format->get_section_dates(7);
        $this->assertEquals($dates->end, $updateenddate);
    }

    /**
     * Tests when we deleting a course section with automatic end date set.
     */
    public function test_course_section_deleted_with_automatic_end_date(): void {
        global $DB;

        // Generate a course with some sections.
        $numsections = 6;
        $course = $this->getDataGenerator()->create_course(array(
            'numsections' => $numsections,
            'format' => 'weeks',
            'startdate' => time(),
            'automaticenddate' => 1));

        // Add a section to the course.
        course_delete_section($course, $numsections);

        // Get the updated course end date.
        $enddate = $DB->get_field('course', 'enddate', array('id' => $course->id));

        $format = course_get_format($course->id);
        $dates = $format->get_section_dates($numsections - 1);

        // Confirm end date was updated.
        $this->assertEquals($enddate, $dates->end);
    }
}
