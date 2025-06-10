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
 * @copyright   2020 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__). '/lib.php');
require_once("$CFG->dirroot/blocks/mhaairs/externallib.php");
require_once("$CFG->libdir/gradelib.php");

/**
 * PHPUnit mhaairs gradebook service log test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @group       block_mhaairs
 * @group       block_mhaairs_service
 * @group       block_mhaairs_gradebookservice
 * @group       block_mhaairs_gradebookservice_log_test
 * @copyright   2020 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_gradebookservice_log_testcase extends block_mhaairs_testcase {

    /**
     * Test log for sync disabled.
     *
     * @return void
     */
    public function test_no_grade_sync() {
        global $DB;

        // Grade item params.
        $itemparams = null;

        // Grades info.
        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 93,
        );

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 111;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = null;

        // No sync.
        $DB->set_field('config', 'value', 0, array('name' => 'block_mhaairs_sync_gradebook'));

        // Run the test.
        $logcontent = 'Grade sync is not enabled in global settings';
        $this->run_test_case($itemparams, $servicedata, $logcontent);
    }

    /**
     * Test log for incorrect course id.
     *
     * @return void
     */
    public function test_incorrect_course_id() {
        // Grade item params.
        $itemparams = null;

        // Grades info.
        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 93,
        );

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc12';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 111;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = null;

        // Run the test.
        $logcontent = 'Could not find course with id tc12';
        $this->run_test_case($itemparams, $servicedata, $logcontent);
    }

    /**
     * Test log for incorrect course id.
     *
     * @return void
     */
    public function test_empty_course_id() {
        // Grade item params.
        $itemparams = null;

        // Grades info.
        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 93,
        );

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = '';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 111;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = null;

        // Run the test.
        $logcontent = 'Empty course id';
        $this->run_test_case($itemparams, $servicedata, $logcontent);
    }

    /**
     * Test log for missing user id.
     *
     * @return void
     */
    public function test_missing_user_id() {
        // Grade item params.
        $itemparams = array(
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 24993,
            'itemname' => 'MH Assignment',
        );

        // Grades info.
        $grades = array(
            'userid' => '',
            'rawgrade' => 93,
        );

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 24993;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = null;

        // Run the test.
        $logcontent = 'Missing user id/username value';
        $this->run_test_case($itemparams, $servicedata, $logcontent);
    }

    /**
     * Test log for incorrect user id.
     *
     * @return void
     */
    public function test_incorrect_user_id() {
        // Grade item params.
        $itemparams = array(
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 24993,
            'itemname' => 'MH Assignment',
        );

        // Grades info.
        $grades = array(
            'userid' => 'stu',
            'rawgrade' => 93,
        );

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 24993;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = null;

        // Run the test.
        $logcontent = 'Could not find user id for username';
        $this->run_test_case($itemparams, $servicedata, $logcontent);
    }

    /**
     * Test log for missing raw grade.
     *
     * @return void
     */
    public function test_missing_raw_grade() {
        // Grade item params.
        $itemparams = array(
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 24993,
            'itemname' => 'MH Assignment',
        );

        // Grades info.
        $grades = array(
            'userid' => 'student1',
            'rawgrade' => '',
        );

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 24993;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = null;

        // Run the test.
        $logcontent = 'Grade info is missing raw grade';
        $this->run_test_case($itemparams, $servicedata, $logcontent);
    }

    /**
     * Test log for missing item instance.
     *
     * @return void
     */
    public function test_missing_item_instance() {
        // Grade item params.
        $itemparams = array(
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 24993,
            'itemname' => 'MH Assignment',
        );

        // Grades info.
        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 94,
        );

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = null;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = null;

        // Run the test.
        $logcontent = 'Cannot get grade item - missing item instance';
        $this->run_test_case($itemparams, $servicedata, $logcontent);
    }

    /**
     * Test log for missing item name for creating new item.
     *
     * @return void
     */
    public function test_missing_item_name() {
        // Item details.
        $itemdetails = array(
            'categoryid' => '',
            'itemname' => '',
            'idnumber' => 0,
            'gradetype' => GRADE_TYPE_VALUE,
            'grademax' => 100,
            'hidden' => '',
            'deleted' => '',
            'identity_type' => '',
            'needsupdate' => '',
            'useexisting' => '',
        );

        // Grades info.
        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 94,
        );

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = null;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = urlencode(json_encode($itemdetails));

        // Run the test.
        $logcontent = 'Cannot get grade item - missing item instance';
        $this->run_test_case(null, $servicedata, $logcontent);
    }

    /**
     * Tests update score without sending item details.
     * Grade item should not be created without item name.
     *
     * @return void
     */
    public function test_no_grade_item() {
        // Grade item params.
        $itemparams = null;

        // Grades info.
        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 93,
        );

        // Service data.
        $servicedata = array();
        $servicedata['source'] = 'mhaairs';
        $servicedata['courseid'] = 'tc1';
        $servicedata['itemtype'] = 'manual';
        $servicedata['itemmodule'] = 'mhaairs';
        $servicedata['iteminstance'] = 111;
        $servicedata['itemnumber'] = 0;
        $servicedata['grades'] = urlencode(json_encode($grades));
        $servicedata['itemdetails'] = null;

        // Run the test.
        $logcontent = 'User grade update failed - Could not find grade item with params:';
        $this->run_test_case($itemparams, $servicedata, $logcontent);
    }

    /**
     * Execute the generic steps for testing the log of the gradebookservice.
     *
     * @return void
     */
    protected function run_test_case($itemparams, $servicedata, $logcontent) {
        $this->set_user('admin');

         // Enable logs and get a logger.
        set_config('block_mhaairs_gradelog', 1);
        $logger = MHLog::instance();

        if ($itemparams) {
            $gitem = new \grade_item($itemparams, false);
            $gitem->insert('mhaairs');
        }

        $service = 'block_mhaairs_gradebookservice_external::update_grade';

        // Send score via service.
        $result = call_user_func_array($service, $servicedata);

        // Check the log.
        $log = file_get_contents($logger->dirpath. DIRECTORY_SEPARATOR . $logger->logs[0]);
        $this->assertContains($logcontent, $log);
    }

}
