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
 * Tests events subsystems
 *
 * @package    core
 * @subpackage group
 * @copyright  2007 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class grouplib_testcase extends advanced_testcase {

    public function test_groups_get_group_by_idnumber() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $idnumber1 = 'idnumber1';
        $idnumber2 = 'idnumber2';

        /**
         * Test with an empty and a null idnumber
         */
        // An empty idnumber should always return a false value
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        // Even when a group exists which also has an empty idnumber
        $generator->create_group(array('courseid' => $course->id));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        /**
         * Test with a valid idnumber
         */
        // There is no matching idnumber at present
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber1));

        // We should now have a valid group returned by the idnumber search
        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals(groups_get_group_by_idnumber($course->id, $idnumber1), $group);

        // An empty idnumber should still return false
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        /**
         * Test with another idnumber
         */
        // There is no matching idnumber at present
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber2));

        // We should now have a valid group returned by the idnumber search
        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals(groups_get_group_by_idnumber($course->id, $idnumber2), $group);

        /**
         * Group idnumbers are unique within a course so test that we don't
         * retrieve groups for the first course
         */

        // Create a second course
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty idnumber should always return a false value
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        // Our existing idnumbers shouldn't be returned here as we're in a different course
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber1));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber2));

        // We should be able to reuse the idnumbers again since this is a different course
        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals(groups_get_group_by_idnumber($course->id, $idnumber1), $group);

        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals(groups_get_group_by_idnumber($course->id, $idnumber2), $group);
    }

    public function test_groups_get_grouping_by_idnumber() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $idnumber1 = 'idnumber1';
        $idnumber2 = 'idnumber2';

        /**
         * Test with an empty and a null idnumber
         */
        // An empty idnumber should always return a false value
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        // Even when a grouping exists which also has an empty idnumber
        $generator->create_grouping(array('courseid' => $course->id));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        /**
         * Test with a valid idnumber
         */
        // There is no matching idnumber at present
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber1));

        // We should now have a valid group returned by the idnumber search
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals(groups_get_grouping_by_idnumber($course->id, $idnumber1), $grouping);

        // An empty idnumber should still return false
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        /**
         * Test with another idnumber
         */
        // There is no matching idnumber at present
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber2));

        // We should now have a valid grouping returned by the idnumber search
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals(groups_get_grouping_by_idnumber($course->id, $idnumber2), $grouping);

        /**
         * Grouping idnumbers are unique within a course so test that we don't
         * retrieve groupings for the first course
         */

        // Create a second course
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty idnumber should always return a false value
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        // Our existing idnumbers shouldn't be returned here as we're in a different course
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber1));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber2));

        // We should be able to reuse the idnumbers again since this is a different course
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals(groups_get_grouping_by_idnumber($course->id, $idnumber1), $grouping);

        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals(groups_get_grouping_by_idnumber($course->id, $idnumber2), $grouping);
    }
}
