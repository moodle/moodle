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

namespace gradereport_singleview\external;

use grade_item;

/**
 * Unit tests for gradereport_singleview external functions.
 *
 * @package    gradereport_singleview
 * @category   external
 * @copyright  2026 Vincent Schneider <vincent.schneider@sudile.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \gradereport_singleview\external\singleview
 */
final class singleview_test extends \core_external\tests\externallib_testcase {
    /**
     * Call the protected get_grade_items_for_search_widget method.
     *
     * @param int $courseid
     * @return array
     */
    private static function call_get_grade_items(int $courseid): array {
        $method = new \ReflectionMethod(singleview::class, 'get_grade_items_for_search_widget');
        $result = $method->invoke(null, $courseid);
        return \core_external\external_api::clean_returnvalue(
            singleview::get_grade_items_for_search_widget_returns(),
            $result
        );
    }

    public function test_get_grade_items_requires_singleview_capability(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');

        $this->setUser($student);
        $this->expectException(\required_capability_exception::class);

        self::call_get_grade_items($course->id);
    }

    public function test_get_grade_items_returns_all_gradeable_items(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'name' => 'Visible']);
        $assign2 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'name' => 'Hidden']);

        $gi = grade_item::fetch(['itemtype' => 'mod', 'itemmodule' => 'assign', 'iteminstance' => $assign2->id]);
        $gi->set_hidden(1);

        $result = self::call_get_grade_items($course->id);
        $names = array_column($result['gradeitems'], 'name');

        $this->assertContains('Visible', $names);
        $this->assertContains('Hidden', $names);
    }
}
