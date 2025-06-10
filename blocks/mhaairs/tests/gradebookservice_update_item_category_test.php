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
 * PHPUnit Mhaairs gradebook service tests.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @copyright   2016 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__). '/lib.php');
require_once("$CFG->dirroot/blocks/mhaairs/externallib.php");
require_once("$CFG->libdir/gradelib.php");
require_once("$CFG->libdir/cronlib.php");

/**
 * PHPUnit mhaairs gradebook service update item category test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @group       block_mhaairs
 * @group       block_mhaairs_service
 * @group       block_mhaairs_gradebookservice
 * @group       block_mhaairs_gradebookservice_update_item_category
 * @copyright   2016 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_gradebookservice_update_item_category_testcase extends block_mhaairs_testcase {

    /**
     * Tests the gradebookservice update grade function for adding an item in a new category,
     * in the default foreground mode.
     * The test creates two grade items which are assigned to the same new category.
     * The category is created during the first update.
     *
     * @return void
     */
    public function test_update_item_new_category() {
        global $DB;

        $this->set_user('admin');

        $catname = 'testcat';
        $catparams = array(
            'fullname' => $catname,
            'courseid' => $this->course->id,
        );

        // Add item 101.
        $this->add_grade_item('101', $catname);

        // No tasks.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));

        // The category should now exist.
        $category = $DB->get_record('grade_categories', $catparams);
        $this->assertEquals(true, !empty($category));

        // The item should be assigned to the category.
        $params = array(
            'categoryid' => $category->id,
            'courseid' => $this->course->id,
        );
        $items = $DB->get_records_menu('grade_items', $params, 'iteminstance', 'id, iteminstance');
        $this->assertEquals(1, count($items));
        $this->assertEquals(true, in_array('101', $items));

        // Add item 102.
        $this->add_grade_item('102', $catname);

        // No tasks.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));

        // The items should be assigned to the category.
        $params = array(
            'categoryid' => $category->id,
            'courseid' => $this->course->id,
        );
        $items = $DB->get_records_menu('grade_items', $params, 'iteminstance', 'id, iteminstance');
        $this->assertEquals(2, count($items));
        $this->assertEquals(true, in_array('102', $items));
    }

    /**
     * Tests the gradebookservice update grade function for adding an item in a category,
     * where multiple categories with the same name already exist. The item should be assigned
     * to the oldest category. When moved to a category with a different name, the item should
     * be assigned to the new category. No tasks should be created and no locking should be triggered.
     *
     * @return void
     */
    public function test_update_item_multiple_categories() {
        global $DB, $CFG;

        $this->set_user('admin');

        // No tasks.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));

        $catname = 'testcat';
        $catname1 = 'testcat1';

        $catparams = array(
            'fullname' => $catname,
            'courseid' => $this->course->id,
            'hidden' => false,
        );

        // Create category.
        $category = new \grade_category($catparams, false);
        $categoryid1 = $category->insert();

        // Create second category with the same name.
        $category = new \grade_category($catparams, false);
        $categoryid2 = $category->insert();

        // Create another category with the different name.
        $catparams['fullname'] = $catname1;
        $category = new \grade_category($catparams, false);
        $categoryid3 = $category->insert();

        // Add item 101 to $catname.
        $this->add_grade_item('101', $catname);

        // No tasks.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));

        // The items should be assigned to category 1.
        $params = array(
            'iteminstance' => '101',
            'categoryid' => $categoryid1,
            'courseid' => $this->course->id,
        );
        $this->assertEquals(1, $DB->count_records('grade_items', $params));

        // Add item 102 to $catname.
        $this->add_grade_item('102', $catname);

        // No tasks.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));

        // The item should be assigned to the category 1.
        $params = array(
            'iteminstance' => '102',
            'categoryid' => $categoryid1,
            'courseid' => $this->course->id,
        );
        $this->assertEquals(1, $DB->count_records('grade_items', $params));

        // Add item 103 to $catname1.
        $this->add_grade_item('103', $catname1);

        // No tasks.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));

        // The item should be assigned to the category 3.
        $params = array(
            'iteminstance' => '103',
            'categoryid' => $categoryid3,
            'courseid' => $this->course->id,
        );
        $this->assertEquals(1, $DB->count_records('grade_items', $params));

        // Move item 102 to $catname1.
        $this->add_grade_item('102', $catname1);

        // No tasks.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));

        // There should be two items assigned to the category 3.
        $params = array(
            'categoryid' => $categoryid3,
            'courseid' => $this->course->id,
        );
        $this->assertEquals(2, $DB->count_records('grade_items', $params));

        // The item should be assigned to the category 3.
        $params['iteminstance'] = '102';
        $this->assertEquals(1, $DB->count_records('grade_items', $params));
    }


    /**
     *
     */
    protected function add_grade_item($iteminstance, $catname) {
        $callback = 'block_mhaairs_gradebookservice_external::update_grade';

        // Service params.
        $serviceparams = array(
            'source' => 'mhaairs',
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => $iteminstance,
            'itemnumber' => '0',
            'grades' => null,
            'itemdetails' => null,
        );

        // Item details.
        $itemdetails = array(
            'categoryid' => $catname,
            'itemname' => $iteminstance,
            'idnumber' => 0,
            'gradetype' => GRADE_TYPE_VALUE,
            'grademax' => 100,
            'hidden' => '',
            'deleted' => '',
            'identity_type' => '',
            'needsupdate' => '',
            'useexisting' => '',
        );

        // Create first grade item.
        $itemdetailsjson = urlencode(json_encode($itemdetails));
        $serviceparams['itemdetails'] = $itemdetailsjson;

        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals(GRADE_UPDATE_OK, $result);
    }

}
