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

namespace core_grades\external;

use core_grades\external\get_gradeitems as get_gradeitems;
use core_external\external_api;
use grade_item;

/**
 * Unit tests for the core_grades\external\get_gradeitems.
 *
 * @package    core_grades
 * @category   external
 * @copyright  2023 Mathew May <Mathew.solutions>
 * @covers     \core_grades\external\get_gradeitems
 */
final class get_gradeitems_test extends \core_external\tests\externallib_testcase {
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course->id]);
        $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'name' => 'Assignment & grade items']);

        $result = get_gradeitems::execute($course->id);
        $result = external_api::clean_returnvalue(get_gradeitems::execute_returns(), $result);
        $allgradeitems = grade_item::fetch_all(['courseid' => $course->id]);
        $gradeitems = array_filter($allgradeitems, function($item) {
            $item->itemname = $item->get_name();
            $item->category = $item->get_parent_category()->get_name();
            return $item->gradetype != GRADE_TYPE_NONE && !$item->is_category_item() && !$item->is_course_item();
        });
        // Move back from grade items into an array of arrays.
        $mapped = array_map(function($item) {
            return [
                'id' => $item->id,
                'itemname' => $item->itemname,
                'category' => $item->category
            ];
        }, array_values($gradeitems));
        $this->assertEquals($mapped, $result['gradeItems']);
    }

    /**
     * Test that a teacher with the moodle/grade:viewall capability can fetch the grade items, including hidden ones.
     */
    public function test_execute_as_teacher(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'name' => 'Assignment']);
        $hiddenassign = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id,
            'name' => 'Hidden assignment',
        ]);

        // Hide the second grade item from students.
        $hiddengradeitem = grade_item::fetch([
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $hiddenassign->id,
        ]);
        $hiddengradeitem->set_hidden(1);
        $this->assertTrue($hiddengradeitem->is_hidden());

        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $result = get_gradeitems::execute($course->id);
        $result = external_api::clean_returnvalue(get_gradeitems::execute_returns(), $result);
        $itemnames = array_column($result['gradeItems'], 'itemname');
        $this->assertEqualsCanonicalizing(['Assignment', 'Hidden assignment'], $itemnames);
    }

    /**
     * Test that a student without the moodle/grade:viewall capability cannot fetch the grade items.
     */
    public function test_execute_without_capability(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // A student does not have the capability to view all grade items.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student);

        $this->expectException(\required_capability_exception::class);
        get_gradeitems::execute($course->id);
    }
}
