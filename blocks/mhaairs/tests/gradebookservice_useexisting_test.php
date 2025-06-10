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
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__). '/lib.php');
require_once("$CFG->dirroot/blocks/mhaairs/externallib.php");
require_once("$CFG->libdir/gradelib.php");

/**
 * PHPUnit mhaairs gradebook service test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @group       block_mhaairs
 * @group       block_mhaairs_service
 * @group       block_mhaairs_gradebookservice
 * @group       block_mhaairs_gradebookservice_useexisting
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_gradebookservice_useexisting_testcase extends block_mhaairs_testcase {

    /**
     * Tests update grade service with an existing quiz item with the same name
     * and iteminstance as requested. Should not update the quiz item. Should add
     * a new manual item.
     *
     * @return void
     */
    public function test_update_item_with_quiz_item() {
        global $DB;

        $this->set_user('admin');

        $this->assertEquals(0, $DB->count_records('grade_items'));

        $itemname = 'Existing grade item';

        // Create a mod/quiz grade item directly.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $generator->create_instance(array(
            'course' => $this->course->id,
            'name' => $itemname,
            'grade' => 100
        ));

        $itemcount = $DB->count_records('grade_items');
        $this->assertEquals(2, $itemcount);

        // Use the quiz id as the item instance for the update.
        $iteminstance = $DB->get_field('quiz', 'id', array('name' => $itemname));

        $service = 'block_mhaairs_gradebookservice_external::update_grade';
        $itemdetails = array(
            'itemname' => $itemname,
            'useexisting' => 1
        );
        $itemdetailsjson = urlencode(json_encode($itemdetails));

        // Update item via service.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = $iteminstance;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = null;
        $servicedata['itemdetails'] = $itemdetailsjson;

        $result = call_user_func_array($service, $servicedata);

        // 3 grade items overall.
        $itemcount = $DB->count_records('grade_items');
        $this->assertEquals(3, $itemcount);

        // 1 quiz item remaining.
        $itemcount = $DB->count_records('grade_items', array(
            'itemtype' => 'mod',
            'itemmodule' => 'quiz'
        ));
        $this->assertEquals(1, $itemcount);

        // 1 new mhaairs item.
        $itemcount = $DB->count_records('grade_items', array(
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs'
        ));
        $this->assertEquals(1, $itemcount);
    }

    /**
     * Tests update grade service with existing regular manual items with the same name
     * as requested. Should update the first item and turn it into mhaairs item. Should not change
     * any item property other than the item instance.
     *
     * @return void
     */
    public function test_update_item_with_manual_item() {
        global $DB;

        $this->set_user('admin');

        $this->assertEquals(0, $DB->count_records('grade_items'));

        $catname = 'Existing grade category';
        $itemname = 'Existing grade item';

        // Create category.
        $catparams = array(
            'fullname' => $catname,
            'courseid' => $this->course->id,
            'hidden' => false,
        );
        $category = new \grade_category($catparams, false);
        $category->id = $category->insert();

        // Add two manual grade item directly.
        $itemparams = array(
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemname' => $itemname,
            'categoryid' => $category->id,
        );

        $gitem = new \grade_item($itemparams, false);
        $gitem->insert('manual');

        $gitem = new \grade_item($itemparams, false);
        $gitem->insert('manual');

        // There should be 4 items (including course and category items).
        $itemcount = $DB->count_records('grade_items');
        $this->assertEquals(4, $itemcount);

        $service = 'block_mhaairs_gradebookservice_external::update_grade';
        $itemdetails = array(
            'itemname' => $itemname,
            'grademax' => 90,
            'useexisting' => 1,
            'categoryid' => 'New grade category',
        );
        $itemdetailsjson = urlencode(json_encode($itemdetails));

        // Update item via service.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 345;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = null;
        $servicedata['itemdetails'] = $itemdetailsjson;

        $result = call_user_func_array($service, $servicedata);

        // 2 grade categories overall.
        $itemcount = $DB->count_records('grade_categories');
        $this->assertEquals(2, $itemcount);

        // 4 grade items overall.
        $itemcount = $DB->count_records('grade_items');
        $this->assertEquals(4, $itemcount);

        // 2 manual items remaining.
        $itemcount = $DB->count_records('grade_items', array(
            'itemtype' => 'manual',
        ));
        $this->assertEquals(2, $itemcount);

        // 1 mhaairs item.
        $itemcount = $DB->count_records('grade_items', array(
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
        ));
        $this->assertEquals(1, $itemcount);

        // 1 mhaairs item with the item instance and original grade.
        $itemcount = $DB->count_records('grade_items', array(
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 345,
            'grademax' => 100.00000,
            'categoryid' => $category->id,
        ));
        $this->assertEquals(1, $itemcount);
    }

    /**
     * Tests update grade service with an existing mhaairs item with the same name
     * as requested but different iteminstance.
     * Should update the existing item.
     *
     * @return void
     */
    public function test_update_item_with_mhaairs_item() {
        global $DB;

        $this->set_user('admin');

        $this->assertEquals(0, $DB->count_records('grade_items'));

        $catname = 'New grade category';
        $itemname = 'Existing grade item';

        $service = 'block_mhaairs_gradebookservice_external::update_grade';
        $itemdetails = array(
            'itemname' => $itemname,
            'useexisting' => 1
        );
        $itemdetailsjson = urlencode(json_encode($itemdetails));

        // Create first item via the service.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 345;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = null;
        $servicedata['itemdetails'] = $itemdetailsjson;

        $result = call_user_func_array($service, $servicedata);

        // 2 grade items overall.
        $itemcount = $DB->count_records('grade_items');
        $this->assertEquals(2, $itemcount);

        // Get the item record.
        $itemrec = $DB->get_record('grade_items', array('itemmodule' => 'mhaairs', 'itemname' => $itemname));

        // Create second item via the service, differnt iteminstance, different category.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 3458;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = null;
        $servicedata['itemdetails'] = null;

        $itemdetails = array(
            'categoryid' => $catname,
            'itemname' => $itemname,
            'useexisting' => 1
        );
        $itemdetailsjson = urlencode(json_encode($itemdetails));
        $servicedata['itemdetails'] = $itemdetailsjson;

        $result = call_user_func_array($service, $servicedata);

        // 2 grade items overall.
        $itemcount = $DB->count_records('grade_items');
        $this->assertEquals(2, $itemcount);

        // 1 3458 mhaairs item in the original category.
        $itemcount = $DB->count_records('grade_items', array(
            'categoryid' => $itemrec->categoryid,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 3458
        ));
        $this->assertEquals(1, $itemcount);

        // The new category was not created.
        $itemcount = $DB->count_records('grade_categories', array('fullname' => $catname));
        $this->assertEquals(0, $itemcount);
    }
}
