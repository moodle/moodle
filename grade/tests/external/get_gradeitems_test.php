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

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for the core_grades\external\get_gradeitems.
 *
 * @package    core_grades
 * @category   external
 * @copyright  2023 Mathew May <Mathew.solutions>
 * @covers     \core_grades\external\get_gradeitems
 */
class get_gradeitems_test extends \externallib_advanced_testcase {
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
}
