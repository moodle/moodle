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

namespace enrol_lti\local\ltiadvantage\viewobject;

/**
 * Tests for published_resource view objects.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\viewobject\published_resource
 */
class published_resource_test extends \advanced_testcase {

    /**
     * Test creating a simple published_resource view object and fetching information about it.
     *
     * @covers ::__construct
     */
    public function test_create() {
        $pr = new published_resource(
            'Assignment one',
            'Course 1',
            23,
            45,
            2,
            'uuid-123-abc',
            true,
            110.50,
            false
        );
        $this->assertEquals('Assignment one', $pr->get_name());
        $this->assertEquals('Course 1', $pr->get_coursefullname());
        $this->assertEquals(23, $pr->get_courseid());
        $this->assertEquals(45, $pr->get_contextid());
        $this->assertEquals(2, $pr->get_id());
        $this->assertEquals('uuid-123-abc', $pr->get_uuid());
        $this->assertEquals(true, $pr->supports_grades());
        $this->assertEquals(110.50, $pr->get_grademax());
        $this->assertEquals(false, $pr->is_course());
    }
}
