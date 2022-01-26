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

use core_grades\external\create_gradecategories;
use external_api;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for the core_grades\external\create_gradecategories webservice.
 *
 * @package    core_grades
 * @category   external
 * @copyright  2021 Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.11
 */
class create_gradecategories_test extends \externallib_advanced_testcase {

    /**
     * Test create_gradecategories.
     *
     * @return void
     */
    public function test_create_gradecategories() {
        global $DB;
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $this->setAdminUser();

        // Test the most basic gradecategory creation.
        $status1 = create_gradecategories::execute($course->id,
            [['fullname' => 'Test Category 1', 'options' => []]]);
        $status1 = external_api::clean_returnvalue(create_gradecategories::execute_returns(), $status1);

        $courseparentcat = \grade_category::fetch_course_category($course->id);
        $record1 = $DB->get_record('grade_categories', ['id' => $status1['categoryids'][0]]);
        $this->assertEquals('Test Category 1', $record1->fullname);
        // Confirm that the parent category for this category is the top level category for the course.
        $this->assertEquals($courseparentcat->id, $record1->parent);
        $this->assertEquals(2, $record1->depth);

        // Now create a category as a child of the newly created category.
        $status2 = create_gradecategories::execute($course->id,
            [['fullname' => 'Test Category 2', 'options' => ['parentcategoryid' => $record1->id]]]);
        $status2 = external_api::clean_returnvalue(create_gradecategories::execute_returns(), $status2);
        $record2 = $DB->get_record('grade_categories', ['id' => $status2['categoryids'][0]]);
        $this->assertEquals($record1->id, $record2->parent);
        $this->assertEquals(3, $record2->depth);
        // Check the path is correct.
        $this->assertEquals('/' . implode('/', [$courseparentcat->id, $record1->id, $record2->id]) . '/', $record2->path);

        /* MDL-72377 commenting broken test.
        // Now create a category with some customised data and check the returns. This customises every value.
        $customopts = [
            'aggregation' => GRADE_AGGREGATE_MEAN,
            'aggregateonlygraded' => 0,
            'aggregateoutcomes' => 1,
            'droplow' => 1,
            'itemname' => 'item',
            'iteminfo' => 'info',
            'idnumber' => 'idnumber',
            'gradetype' => GRADE_TYPE_TEXT,
            'grademax' => 5,
            'grademin' => 2,
            'gradepass' => 3,
            'display' => GRADE_DISPLAY_TYPE_LETTER,
            // Hack. This must be -2 to use the default setting.
            'decimals' => 3,
            'hiddenuntil' => time(),
            'locktime' => time(),
            'weightoverride' => 1,
            'aggregationcoef2' => 20,
            'parentcategoryid' => $record2->id
        ];

        $status3 = create_gradecategories::execute($course->id,
            [['fullname' => 'Test Category 3', 'options' => $customopts]]);
        $status3 = external_api::clean_returnvalue(create_gradecategories::execute_returns(), $status3);
        $cat3 = new \grade_category(['courseid' => $course->id, 'id' => $status3['categoryids'][0]], true);
        $cat3->load_grade_item();

        // Lets check all of the data is in the right shape.
        $this->assertEquals(GRADE_AGGREGATE_MEAN, $cat3->aggregation);
        $this->assertEquals(0, $cat3->aggregateonlygraded);
        $this->assertEquals(1, $cat3->aggregateoutcomes);
        $this->assertEquals(1, $cat3->droplow);
        $this->assertEquals('item', $cat3->grade_item->itemname);
        $this->assertEquals('info', $cat3->grade_item->iteminfo);
        $this->assertEquals('idnumber', $cat3->grade_item->idnumber);
        $this->assertEquals(GRADE_TYPE_TEXT, $cat3->grade_item->gradetype);
        $this->assertEquals(5, $cat3->grade_item->grademax);
        $this->assertEquals(2, $cat3->grade_item->grademin);
        $this->assertEquals(3, $cat3->grade_item->gradepass);
        $this->assertEquals(GRADE_DISPLAY_TYPE_LETTER, $cat3->grade_item->display);
        $this->assertEquals(3, $cat3->grade_item->decimals);
        $this->assertGreaterThanOrEqual($cat3->grade_item->hidden, time());
        $this->assertGreaterThanOrEqual($cat3->grade_item->locktime, time());
        $this->assertEquals(1, $cat3->grade_item->weightoverride);
        // Coefficient is converted to percentage.
        $this->assertEquals(0.2, $cat3->grade_item->aggregationcoef2);
        $this->assertEquals($record2->id, $cat3->parent);*/

        // Now test creating 2 in parallel, and nesting them.
        $status4 = create_gradecategories::execute($course->id, [
            [
                'fullname' => 'Test Category 4',
                'options' => [
                    'idnumber' => 'secondlevel'
                ],
            ],
            [
                'fullname' => 'Test Category 5',
                'options' => [
                    'idnumber' => 'thirdlevel',
                    'parentcategoryidnumber' => 'secondlevel'
                ],
            ],
        ]);
        $status4 = external_api::clean_returnvalue(create_gradecategories::execute_returns(), $status4);

        $secondlevel = $DB->get_record('grade_categories', ['id' => $status4['categoryids'][0]]);
        $thirdlevel = $DB->get_record('grade_categories', ['id' => $status4['categoryids'][1]]);

        // Confirm that the parent category for secondlevel is the top level category for the course.
        $this->assertEquals($courseparentcat->id, $secondlevel->parent);
        $this->assertEquals(2, $record1->depth);

        // Confirm that the parent category for thirdlevel is the secondlevel category.
        $this->assertEquals($secondlevel->id, $thirdlevel->parent);
        $this->assertEquals(3, $thirdlevel->depth);
        // Check the path is correct.
        $this->assertEquals('/' . implode('/', [$courseparentcat->id, $secondlevel->id, $thirdlevel->id]) . '/', $thirdlevel->path);
    }
}
