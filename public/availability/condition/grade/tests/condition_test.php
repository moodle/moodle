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

namespace availability_grade;

/**
 * Unit tests for the grade condition.
 *
 * @package availability_grade
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class condition_test extends \advanced_testcase {
    /**
     * Tests constructing and using grade condition.
     */
    public function test_usage(): void {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        $this->resetAfterTest();
        $CFG->enableavailability = true;

        // Make a test course and user.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Make assign module.
        $assignrow = $this->getDataGenerator()->create_module('assign', array(
                'course' => $course->id, 'name' => 'Test!'));
        $assign = new \assign(\context_module::instance($assignrow->cmid), false, false);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($assignrow->cmid);

        $info = new \core_availability\info_module($cm);

        // Get the actual grade item.
        $item = $assign->get_grade_item();

        // Construct tree with grade condition (any grade, specified item).
        $structure = (object)array('type' => 'grade', 'id' => (int)$item->id);
        $cond = new condition($structure);

        // Check if available (not available).
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~have a grade.*Test!~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Add grade and check available.
        self::set_grade($assignrow, $user->id, 37.2);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~do not have a grade.*Test!~', $information);

        // Construct directly and test remaining conditions; first, min grade (fail).
        self::set_grade($assignrow, $user->id, 29.99999);
        $structure->min = 30.0;
        $cond = new condition($structure);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~achieve higher than.*Test!~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Min grade (success).
        self::set_grade($assignrow, $user->id, 30);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~do not get certain scores.*Test!~', $information);

        // Max grade (fail).
        unset($structure->min);
        $structure->max = 30.0;
        $cond = new condition($structure);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~achieve lower than a certain score in.*Test!~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Max grade (success).
        self::set_grade($assignrow, $user->id, 29.99999);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~do not get certain scores.*Test!~', $information);

        // Max and min (fail).
        $structure->min = 30.0;
        $structure->max = 34.12345;
        $cond = new condition($structure);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~achieve a score within a certain range.*Test!~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Still fail (other end).
        self::set_grade($assignrow, $user->id, 34.12345);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));

        // Success (top end).
        self::set_grade($assignrow, $user->id, 34.12344);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~do not get certain scores.*Test!~', $information);

        // Success (bottom end).
        self::set_grade($assignrow, $user->id, 30.0);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~do not get certain scores.*Test!~', $information);
    }

    /**
     * Tests the constructor including error conditions. Also tests the
     * string conversion feature (intended for debugging only).
     */
    public function test_constructor(): void {
        // No parameters.
        $structure = new \stdClass();
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->id', $e->getMessage());
        }

        // Invalid id (not int).
        $structure->id = 'bourne';
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->id', $e->getMessage());
        }

        // Invalid min (not number).
        $structure->id = 42;
        $structure->min = 'ute';
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->min', $e->getMessage());
        }

        // Invalid max (not number).
        $structure->min = 3.89;
        $structure->max = '9000';
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->max', $e->getMessage());
        }

        // All valid.
        $structure->max = 4.0;
        $cond = new condition($structure);
        $this->assertEquals('{grade:#42 >= 3.89000, < 4.00000}', (string)$cond);

        // No max.
        unset($structure->max);
        $cond = new condition($structure);
        $this->assertEquals('{grade:#42 >= 3.89000}', (string)$cond);

        // No min.
        unset($structure->min);
        $structure->max = 32.768;
        $cond = new condition($structure);
        $this->assertEquals('{grade:#42 < 32.76800}', (string)$cond);

        // No nothing (only requires that grade exists).
        unset($structure->max);
        $cond = new condition($structure);
        $this->assertEquals('{grade:#42}', (string)$cond);
    }

    /**
     * Tests the save() function.
     */
    public function test_save(): void {
        $structure = (object)array('id' => 19);
        $cond = new condition($structure);
        $structure->type = 'grade';
        $this->assertEquals($structure, $cond->save());

        $structure = (object)array('id' => 19, 'min' => 4.12345, 'max' => 90);
        $cond = new condition($structure);
        $structure->type = 'grade';
        $this->assertEquals($structure, $cond->save());
    }

    /**
     * Updates the grade of a user in the given assign module instance.
     *
     * @param \stdClass $assignrow Assignment row from database
     * @param int $userid User id
     * @param float $grade Grade
     */
    protected static function set_grade($assignrow, $userid, $grade) {
        $grades = array();
        $grades[$userid] = (object)array(
                'rawgrade' => $grade, 'userid' => $userid);
        $assignrow->cmidnumber = null;
        assign_grade_item_update($assignrow, $grades);
    }

    /**
     * Tests the update_dependency_id() function.
     */
    public function test_update_dependency_id(): void {
        $cond = new condition((object)array('id' => 123));
        $this->assertFalse($cond->update_dependency_id('frogs', 123, 456));
        $this->assertFalse($cond->update_dependency_id('grade_items', 12, 34));
        $this->assertTrue($cond->update_dependency_id('grade_items', 123, 456));
        $after = $cond->save();
        $this->assertEquals(456, $after->id);
    }
}
