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
 * PHPUnit mhaairs gradebook service test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @group       block_mhaairs
 * @group       block_mhaairs_service
 * @group       block_mhaairs_gradebookservice
 * @group       block_mhaairs_gradebookservice_update_score_test
 * @copyright   2015 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_gradebookservice_update_score_testcase extends block_mhaairs_testcase {

    /**
     * Tests update score without sending item details.
     * Grade item should not be created without item name.
     *
     * @return void
     */
    public function test_update_score() {
        global $DB;

        $this->set_user('admin');

        $this->assertEquals(0, $DB->count_records('grade_items'));

        // Add an mhaairs item directly.
        $iteminstance = 24993;
        $itemparams = array(
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => $iteminstance,
            'itemname' => 'MH Assignment',
        );

        $gitem = new \grade_item($itemparams, false);
        $gitem->insert('mhaairs');

        $this->assertEquals(2, $DB->count_records('grade_items'));

        $service = 'block_mhaairs_gradebookservice_external::update_grade';

        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 93,
        );
        $gradesjson = urlencode(json_encode($grades));

        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 111;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = $gradesjson;
        $servicedata['itemdetails'] = null;

        // Send score via service without details, item by instance doesn't exist.
        $result = call_user_func_array($service, $servicedata);
        $this->assertEquals(GRADE_UPDATE_FAILED, $result);
        $this->assertEquals(2, $DB->count_records('grade_items'));

        // Set the existing item instance.
        $servicedata['iteminstance'] = $iteminstance;

        // Send score via service without details, item by instance exists.
        $result = call_user_func_array($service, $servicedata);
        $this->assertEquals(GRADE_UPDATE_OK, $result);
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(1, $DB->count_records('grade_grades'));
        $usergrade = $DB->get_field('grade_grades', 'finalgrade', array('userid' => $this->student1->id));
        $this->assertEquals(93, $usergrade);

        // Set typical item details for score update.
        $itemdetails = array(
            'courseid' => '3',
            'idnumber' => '111',
            'identity_type' => null,
            'needsupdate' => 1,
            'useexisting' => 0,
        );
        $itemdetailsjson = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = $itemdetailsjson;

        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 94,
        );
        $gradesjson = urlencode(json_encode($grades));
        $servicedata['grades'] = $gradesjson;

        // Send score via service to item with details.
        $result = call_user_func_array($service, $servicedata);
        $this->assertEquals(GRADE_UPDATE_OK, $result);
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(1, $DB->count_records('grade_grades'));
        $usergrade = $DB->get_field('grade_grades', 'finalgrade', array('userid' => $this->student1->id));
        $this->assertEquals(94, $usergrade);
    }

    /**
     * Tests the gradebookservice update grade with cases that relate
     * to the fix in CONTRIB_5853, that is, removing the ";,-" characters
     * cleanup that was performed on categoryid, itemtype and userid.
     *
     * @return void
     */
    public function test_update_score_userid_with_hyphen() {
        global $DB;

        $callback = 'block_mhaairs_gradebookservice_external::update_grade';
        $this->set_user('admin');

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 101;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = null;
        $servicedata['itemdetails'] = null;

        // Item details.
        $itemdetails = array();
        $itemdetails['itemname'] = 'assignment5853';
        $itemdetails['gradetype'] = 1;
        $itemdetails['grademax'] = 100;
        $itemdetailsjson = urlencode(json_encode($itemdetails));

        $servicedata['itemdetails'] = $itemdetailsjson;

        // Create the grade item.
        $result = call_user_func_array($callback, $servicedata);
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        // Grade for username student1.
        $grades = array();
        $grades['userid'] = 'student1';
        $grades['rawgrade'] = 93;
        $gradesjson = urlencode(json_encode($grades));

        $servicedata['itemdetails'] = null;
        $servicedata['grades'] = $gradesjson;

        // Update user grade.
        $result = call_user_func_array($callback, $servicedata);
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        // Grade for username student-1.
        $user = $this->getDataGenerator()->create_user(array('username' => 'student-1'));
        $this->getDataGenerator()->enrol_user($user->id, $this->course->id, $this->roles['student']);

        $grades = array();
        $grades['userid'] = 'student-1';
        $grades['rawgrade'] = 93;
        $gradesjson = urlencode(json_encode($grades));

        $servicedata['grades'] = $gradesjson;

        // Update user grade.
        $result = call_user_func_array($callback, $servicedata);
        $this->assertEquals(GRADE_UPDATE_OK, $result);
    }

}
