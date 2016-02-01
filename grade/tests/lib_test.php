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
 * Unit tests for grade/lib.php.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2016 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/grade/lib.php');

/**
 * Unit tests for grade/lib.php.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_grade_lib_test extends advanced_testcase {

    /**
     * Test can_output_item.
     */
    public function test_can_output_item() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Course level grade category.
        $course = $generator->create_course();
        // Grade tree looks something like:
        // - Test course    (Rendered).
        $gradetree = grade_category::fetch_course_tree($course->id);
        $this->assertTrue(grade_tree::can_output_item($gradetree));

        // Add a grade category with default settings.
        $generator->create_grade_category(array('courseid' => $course->id));
        // Grade tree now looks something like:
        // - Test course n        (Rendered).
        // -- Grade category n    (Rendered).
        $gradetree = grade_category::fetch_course_tree($course->id);
        $this->assertNotEmpty($gradetree['children']);
        foreach ($gradetree['children'] as $child) {
            $this->assertTrue(grade_tree::can_output_item($child));
        }

        // Add a grade category with grade type = None.
        $nototalcategory = 'No total category';
        $nototalparams = [
            'courseid' => $course->id,
            'fullname' => $nototalcategory,
            'aggregation' => GRADE_AGGREGATE_WEIGHTED_MEAN
        ];
        $nototal = $generator->create_grade_category($nototalparams);
        $catnototal = grade_category::fetch(array('id' => $nototal->id));
        // Set the grade type of the grade item associated to the grade category.
        $catitemnototal = $catnototal->load_grade_item();
        $catitemnototal->gradetype = GRADE_TYPE_NONE;
        $catitemnototal->update();

        // Grade tree looks something like:
        // - Test course n        (Rendered).
        // -- Grade category n    (Rendered).
        // -- No total category   (Not rendered).
        $gradetree = grade_category::fetch_course_tree($course->id);
        foreach ($gradetree['children'] as $child) {
            if ($child['object']->fullname == $nototalcategory) {
                $this->assertFalse(grade_tree::can_output_item($child));
            } else {
                $this->assertTrue(grade_tree::can_output_item($child));
            }
        }

        // Add another grade category with default settings under 'No total category'.
        $normalinnototalparams = [
            'courseid' => $course->id,
            'fullname' => 'Normal category in no total category',
            'parent' => $nototal->id
        ];
        $generator->create_grade_category($normalinnototalparams);

        // Grade tree looks something like:
        // - Test course n                           (Rendered).
        // -- Grade category n                       (Rendered).
        // -- No total category                      (Rendered).
        // --- Normal category in no total category  (Rendered).
        $gradetree = grade_category::fetch_course_tree($course->id);
        foreach ($gradetree['children'] as $child) {
            // All children are now visible.
            $this->assertTrue(grade_tree::can_output_item($child));
            if (!empty($child['children'])) {
                foreach ($child['children'] as $grandchild) {
                    $this->assertTrue(grade_tree::can_output_item($grandchild));
                }
            }
        }

        // Add a grade category with grade type = None.
        $nototalcategory2 = 'No total category 2';
        $nototal2params = [
            'courseid' => $course->id,
            'fullname' => $nototalcategory2,
            'aggregation' => GRADE_AGGREGATE_WEIGHTED_MEAN
        ];
        $nototal2 = $generator->create_grade_category($nototal2params);
        $catnototal2 = grade_category::fetch(array('id' => $nototal2->id));
        // Set the grade type of the grade item associated to the grade category.
        $catitemnototal2 = $catnototal2->load_grade_item();
        $catitemnototal2->gradetype = GRADE_TYPE_NONE;
        $catitemnototal2->update();

        // Add a category with no total under 'No total category'.
        $nototalinnototalcategory = 'Category with no total in no total category';
        $nototalinnototalparams = [
            'courseid' => $course->id,
            'fullname' => $nototalinnototalcategory,
            'aggregation' => GRADE_AGGREGATE_WEIGHTED_MEAN,
            'parent' => $nototal2->id
        ];
        $nototalinnototal = $generator->create_grade_category($nototalinnototalparams);
        $catnototalinnototal = grade_category::fetch(array('id' => $nototalinnototal->id));
        // Set the grade type of the grade item associated to the grade category.
        $catitemnototalinnototal = $catnototalinnototal->load_grade_item();
        $catitemnototalinnototal->gradetype = GRADE_TYPE_NONE;
        $catitemnototalinnototal->update();

        // Grade tree looks something like:
        // - Test course n                                    (Rendered).
        // -- Grade category n                                (Rendered).
        // -- No total category                               (Rendered).
        // --- Normal category in no total category           (Rendered).
        // -- No total category 2                             (Not rendered).
        // --- Category with no total in no total category    (Not rendered).
        $gradetree = grade_category::fetch_course_tree($course->id);
        foreach ($gradetree['children'] as $child) {
            if ($child['object']->fullname == $nototalcategory2) {
                $this->assertFalse(grade_tree::can_output_item($child));
            } else {
                $this->assertTrue(grade_tree::can_output_item($child));
            }
            if (!empty($child['children'])) {
                foreach ($child['children'] as $grandchild) {
                    if ($grandchild['object']->fullname == $nototalinnototalcategory) {
                        $this->assertFalse(grade_tree::can_output_item($grandchild));
                    } else {
                        $this->assertTrue(grade_tree::can_output_item($grandchild));
                    }
                }
            }
        }
    }
}
