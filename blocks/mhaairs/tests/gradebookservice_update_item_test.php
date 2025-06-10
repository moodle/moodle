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
 * @copyright   2015 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__). '/lib.php');
require_once("$CFG->dirroot/blocks/mhaairs/externallib.php");
require_once("$CFG->libdir/gradelib.php");

/**
 * PHPUnit mhaairs gradebook service update item test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @group       block_mhaairs
 * @group       block_mhaairs_service
 * @group       block_mhaairs_gradebookservice
 * @group       block_mhaairs_gradebookservice_update_item_test
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_gradebookservice_update_item_testcase extends block_mhaairs_testcase {

    /**
     * Tests the gradebookservice update grade function for adding and item.
     *
     * @return void
     */
    public function test_gradebookservice_update_item() {
        global $DB;

        $callback = 'block_mhaairs_gradebookservice_external::gradebookservice';
        $this->set_user('admin');

        // Item details.
        $itemdetails = array(
            'categoryid' => '',
            'itemname' => 'testassignment',
            'idnumber' => 0,
            'gradetype' => GRADE_TYPE_VALUE,
            'grademax' => 100,
            'hidden' => '',
            'deleted' => '',
            'identity_type' => '',
            'needsupdate' => '',
            'useexisting' => '',
        );
        $itemdetailsjson = urlencode(json_encode($itemdetails));

        // Service params.
        $serviceparams = array(
            'source' => 'mhaairs',
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => '101',
            'itemnumber' => '0',
            'grades' => null,
            'itemdetails' => null,
        );
        $serviceparams['itemdetails'] = $itemdetailsjson;

        // CREATE.
        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        // Check grade item created.
        // Expected 2: one for the course and one for the item.
        $this->assertEquals(2, $DB->count_records('grade_items'));

        // Fetch the item.
        $giparams = array(
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 101,
            'courseid' => $this->course->id,
            'itemnumber' => 0
        );
        $gitem = grade_item::fetch($giparams);
        $this->assertInstanceOf('grade_item', $gitem);
        $this->assertEquals(100, $gitem->grademax);

        // UPDATE.
        // Identify course by idnumber.
        $serviceparams['courseid'] = 'tc1';
        // Item details.
        $itemdetails['grademax'] = 95;
        $itemdetailsjson = urlencode(json_encode($itemdetails));
        $serviceparams['itemdetails'] = $itemdetailsjson;

        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        // Check grade item updated.
        // Expected 2: one for the course and one for the item.
        $this->assertEquals(2, $DB->count_records('grade_items'));

        // Fetch the item.
        $giparams = array(
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 101,
            'courseid' => $this->course->id,
            'itemnumber' => 0
        );
        $gitem = grade_item::fetch($giparams);
        $this->assertInstanceOf('grade_item', $gitem);
        $this->assertEquals(95, $gitem->grademax);

        // UPDATE that should fail.
        // Identify course by idnumber.
        $serviceparams['courseid'] = 'tc1';
        // Try to update by id only.
        $itemdetails['identity_type'] = 'internal';
        $itemdetails['grademax'] = 90;
        $itemdetailsjson = urlencode(json_encode($itemdetails));
        $serviceparams['itemdetails'] = $itemdetailsjson;

        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals(GRADE_UPDATE_FAILED, $result);

        // Check no update.
        // Expected 2: one for the course and one for the item.
        $this->assertEquals(2, $DB->count_records('grade_items'));

        // Fetch the item.
        $giparams = array(
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 101,
            'courseid' => $this->course->id,
            'itemnumber' => 0
        );
        $gitem = grade_item::fetch($giparams);
        $this->assertInstanceOf('grade_item', $gitem);
        $this->assertEquals(95, $gitem->grademax);

        // DELETE ITEM.
        // Identify course by id.
        $serviceparams['courseid'] = $this->course->id;
        // Set delete in item details.
        $itemdetails['deleted'] = 1;
        $itemdetailsjson = urlencode(json_encode($itemdetails));
        $serviceparams['itemdetails'] = $itemdetailsjson;

        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        $this->assertEquals(1, $DB->count_records('grade_items'));

        // Fetch the item.
        $giparams = array(
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 101,
            'courseid' => $this->course->id,
            'itemnumber' => 0
        );
        $gitem = grade_item::fetch($giparams);
        $this->assertEquals(false, $gitem);
    }

    /**
     * Tests the gradebookservice update grade with cases that should
     * result in success.
     *
     * @return void
     */
    public function test_update_grade_update_item() {
        global $DB;

        $callback = 'block_mhaairs_gradebookservice_external::update_grade';
        $this->set_user('admin');

        // CREATE/UPDATE.
        $cases = $this->get_cases('tc_update_grade');
        foreach ($cases as $case) {
            if (!empty($case->hidden)) {
                continue;
            }

            try {
                $result = call_user_func_array($callback, $case->servicedata);
                $this->assertEquals($case->result, $result);
            } catch (Exception $e) {
                $result = get_class($e);
                $this->assertEquals($case->result, $result);
                continue;
            }

            // Fetch the item.
            $giparams = array(
                'itemtype' => 'manual',
                'itemmodule' => 'mhaairs',
                'iteminstance' => $case->iteminstance,
                'courseid' => $this->course->id,
                'itemnumber' => $case->itemnumber,
            );
            $gitem = grade_item::fetch($giparams);

            if (is_numeric($case->result) and (int) $case->result === 0) {
                // Verify successful update.
                $this->assertInstanceOf('grade_item', $gitem);

                $maxgrade = !empty($case->item_grademax) ? (int) $case->item_grademax : 100;
                $this->assertEquals($maxgrade, $gitem->grademax);

                if (!empty($case->item_categoryid)) {
                    // Fetch the category.
                    $fetchparams = array(
                        'fullname' => $case->item_categoryid,
                        'courseid' => $this->course->id,
                    );
                    $category = grade_category::fetch($fetchparams);
                    $categoryid = $category->id;
                    $this->assertEquals($gitem->categoryid, $categoryid);
                }

            } else {
                // Verify failed update.
                $this->assertEquals(false, $gitem);
            }
        }
    }

    /**
     * Tests the gradebookservice update grade with different course ids.
     * In particular since the service tries the fetch the course by both
     * id number and internal id, we want to make sure that incidental conflicts
     * are handled correctly.
     * Cases:
     *  - Idnumber of course B is like the id of course A; item should be added to course B.
     *  - Idnumber of course B is like the id of course A and identity type = internal;
     *    item should be added to course A.
     *  - The int of non-integer idnumber of course B equals the id of course A;
     *    item should be added to course B.
     *  - The int of non-integer idnumber of course B equals the id of course A,
     *    and identity type = internal;
     *    item update should fail.
     *
     * @return void
     */
    public function test_item_course() {
        global $DB;

        $this->set_user('admin');

        $iteminstance = 100;

        // Create a course with id number which is the internal id
        // of tc1.
        $idnumber = $this->course->id;
        $record = array('idnumber' => $idnumber);
        $course = $this->getDataGenerator()->create_course($record);

        // Item should be added to course.
        $iteminstance++;
        $options = array('category' => 'homework');
        $result = $this->add_grade_item_by_service($idnumber, $iteminstance, $options);
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        $params = array(
            'itemmodule' => 'mhaairs',
            'itemname' => $iteminstance,
            'courseid' => $course->id,
        );
        $this->assertEquals(1, $DB->count_records('grade_items', $params));

        // With idonly: Item should be added to tc1.
        $iteminstance++;
        $options = array('category' => 'homework', 'identitytype' => 'internal');
        $result = $this->add_grade_item_by_service($idnumber, $iteminstance, $options);
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        $params = array(
            'itemmodule' => 'mhaairs',
            'itemname' => $iteminstance,
            'courseid' => $this->course->id,
        );
        $this->assertEquals(1, $DB->count_records('grade_items', $params));

        // Create a course with a non-int id number whose int is the internal id
        // of tc1.
        $idnumber = $this->course->id. '.123';
        $record = array('idnumber' => $idnumber);
        $course = $this->getDataGenerator()->create_course($record);

        // Item should be added to course.
        $iteminstance++;
        $options = array('category' => 'homework');
        $result = $this->add_grade_item_by_service($idnumber, $iteminstance, $options);
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        // Verify the item added to the new course.
        $params = array(
            'itemmodule' => 'mhaairs',
            'itemname' => $iteminstance,
            'courseid' => $course->id,
        );
        $this->assertEquals(1, $DB->count_records('grade_items', $params));

        // With internal id: update should fail.
        $iteminstance++;
        $options = array('category' => 'homework', 'identitytype' => 'internal');
        $result = $this->add_grade_item_by_service($idnumber, $iteminstance, $options);
        $this->assertEquals(GRADE_UPDATE_FAILED, $result);

        // Verify the item not added to any course.
        $params = array(
            'itemmodule' => 'mhaairs',
            'itemname' => $iteminstance,
        );
        $this->assertEquals(0, $DB->count_records('grade_items', $params));
    }

    /**
     * Returns the list of cases from the fixtures. For each case generates the service
     * params and item params.
     *
     * @return array
     */
    protected function get_cases($fixturename) {
        // Test cases.
        $fixture = __DIR__. "/fixtures/$fixturename.csv";
        $dataset = $this->createCsvDataSet(array('cases' => $fixture));
        $rows = $dataset->getTable('cases');
        $columns = $dataset->getTableMetaData('cases')->getColumns();

        $cases = array();
        for ($r = 0; $r < $rows->getRowCount(); $r++) {
            $cases[] = (object) array_combine($columns, $rows->getRow($r));
        }

        // Item details.
        $itemparams = array(
            'categoryid',
            'itemname',
            'idnumber',
            'gradetype',
            'grademax',
            'grademin',
            'hidden',
            'deleted',
            'identity_type',
            'needsupdate',
            'useexisting',
        );

        // Service params.
        $serviceparams = array(
            'source', // Source.
            'courseid', // Course id.
            'itemtype', // Item type.
            'itemmodule', // Item module.
            'iteminstance', // Item instance.
            'itemnumber', // Item number.
            'grades', // Grades.
            'itemdetails', // Item details.
        );

        foreach ($cases as $case) {
            // Compile the item details.
            $itemdetails = array();
            foreach ($itemparams as $param) {
                $caseparam = "item_$param";
                if (isset($case->$caseparam)) {
                    $itemdetails[$param] = $case->$caseparam;
                } else {
                    $itemdetails[$param] = null;
                }
            }
            $itemdetails = $itemdetails ? $itemdetails : null;
            $itemdetailsjson = urlencode(json_encode($itemdetails));

            // Compile the service params.
            $servicedata = array();
            foreach ($serviceparams as $param) {
                $value = isset($case->$param) ? $case->$param : '';
                $servicedata[$param] = $value;
            }
            if (!empty($case->courseinstance)) {
                $thiscourse = $this->{$case->courseinstance};
                $servicedata['courseid'] = $thiscourse->id;
            }
            $servicedata['itemdetails'] = $itemdetailsjson;
            $servicedata['grades'] = 'null';

            $case->servicedata = $servicedata;
        }

        return $cases;
    }

}
