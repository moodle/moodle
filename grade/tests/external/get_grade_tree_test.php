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

use core_external\external_api;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for core_grades\external\get_grade_tree.
 *
 * @package    core_grades
 * @category   external
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @covers     \core_grades\external\get_grade_tree
 */
final class get_grade_tree_test extends \externallib_advanced_testcase {

    /**
     * Test the return value of the external function.
     *
     * @return void
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['fullname' => 'Course']);
        $coursegradecategory = \grade_category::fetch_course_category($course->id);
        // Create a grade item 'Grade item' and grade category 'Category 1' within the course grade category.
        $gradeitem = $this->getDataGenerator()->create_grade_item(
            ['courseid' => $course->id, 'itemname' => 'Grade item']);
        $gradecategory1 = $this->getDataGenerator()->create_grade_category(
            ['courseid' => $course->id, 'fullname' => 'Category 1']);
        // Create a grade item 'Grade item 1' and grade category 'Category 2' within 'Category 1'.
        $gradeitem1 = $this->getDataGenerator()->create_grade_item(
            ['courseid' => $course->id, 'itemname' => 'Grade item 1', 'categoryid' => $gradecategory1->id]);
        $gradecategory2 = $this->getDataGenerator()->create_grade_category(
            ['courseid' => $course->id, 'fullname' => 'Category 2', 'parent' => $gradecategory1->id]);
        // Create a grade item 'Grade item 2' and grade category 'Category 3' (with no children) within 'Category 2'.
        $gradeitem2 = $this->getDataGenerator()->create_grade_item(
            ['courseid' => $course->id, 'itemname' => 'Grade item 2', 'categoryid' => $gradecategory2->id]);
        $gradecategory3 = $this->getDataGenerator()->create_grade_category(
            ['courseid' => $course->id, 'fullname' => 'Category 3', 'parent' => $gradecategory2->id]);

        $result = get_grade_tree::execute($course->id);
        $result = external_api::clean_returnvalue(get_grade_tree::execute_returns(), $result);

        $expected = json_encode([
            'id' => $coursegradecategory->id,
            'name' => 'Course',
            'iscategory' => true,
            'haschildcategories' => true,
            'children' => [
                [
                    'id' => $gradeitem->id,
                    'name' => 'Grade item',
                    'iscategory' => false,
                    'children' => null
                ],
                [
                    'id' => $gradecategory1->id,
                    'name' => 'Category 1',
                    'iscategory' => true,
                    'haschildcategories' => true,
                    'children' => [
                        [
                            'id' => $gradeitem1->id,
                            'name' => 'Grade item 1',
                            'iscategory' => false,
                            'children' => null
                        ],
                        [
                            'id' => $gradecategory2->id,
                            'name' => 'Category 2',
                            'iscategory' => true,
                            'haschildcategories' => true,
                            'children' => [
                                [
                                    'id' => $gradeitem2->id,
                                    'name' => 'Grade item 2',
                                    'iscategory' => false,
                                    'children' => null
                                ],
                                [
                                    'id' => $gradecategory3->id,
                                    'name' => 'Category 3',
                                    'iscategory' => true,
                                    'haschildcategories' => false,
                                    'children' => null
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->assertEquals($expected, $result);
    }
}
