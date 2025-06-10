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
 * @group       block_mhaairs_gradebookservice_test
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_gradebookservice_testcase extends block_mhaairs_testcase {

    /**
     * Gradebookservice update grade should fail when sync grades is disabled
     * in the plugin site settings.
     *
     * @return void
     */
    public function test_update_grade_no_sync() {
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

        // No sync.
        $DB->set_field('config', 'value', 0, array('name' => 'block_mhaairs_sync_gradebook'));

        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals(GRADE_UPDATE_FAILED, $result);
    }

    /**
     * Tests the migration of an old mhaairs (mod/quiz) grade item without grade.
     * (CONTRIB_5863)
     *
     * @return void
     */
    public function test_grade_item_migration_via_update_no_score() {
        global $DB;

        $this->set_user('admin');

        $quizitemparams = array('itemtype' => 'mod', 'itemmodule' => 'quiz');
        $mhaairsitemparams = array('itemtype' => 'manual', 'itemmodule' => 'mhaairs');

        $this->assertEquals(0, $DB->count_records('grade_items'));

        $service = 'block_mhaairs_gradebookservice_external::update_grade';
        $iteminstance = 24993;
        $itemdetails = array(
            'itemname' => 'mhaairs-quiz',
            'itemtype' => 'mod',
            'idnumber' => '0',
        );
        $itemdetailsjson = urlencode(json_encode($itemdetails));
        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 93,
        );
        $gradesjson = urlencode(json_encode($grades));

        // Create a mod/quiz grade item directly.
        $result = grade_update(
            'mhaairs',
            $this->course->id,
            'mod',
            'quiz',
            $iteminstance,
            0,
            null,
            $itemdetails
        );

        $itemcount = $DB->count_records('grade_items', $quizitemparams);
        $this->assertEquals(1, $itemcount);

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

        // No quiz items.
        $itemcount = $DB->count_records('grade_items', $quizitemparams);
        $this->assertEquals(0, $itemcount);

        // 1 mhaairs item.
        $itemcount = $DB->count_records('grade_items', $mhaairsitemparams);
        $this->assertEquals(1, $itemcount);

        // Update score.
        $servicedata['grades'] = $gradesjson;
        $result = call_user_func_array($service, $servicedata);
        $this->assertEquals(1, $itemcount);

        // Verify score updated.
        $usergrade = $DB->get_field('grade_grades', 'finalgrade', array('userid' => $this->student1->id));
        $this->assertEquals(93, (int) $usergrade);

        // No quiz items.
        $itemcount = $DB->count_records('grade_items', $quizitemparams);
        $this->assertEquals(0, $itemcount);

        // 1 mhaairs item.
        $itemcount = $DB->count_records('grade_items', $mhaairsitemparams);
        $this->assertEquals(1, $itemcount);
    }

    /**
     * Tests the migration of an old mhaairs (mod/quiz) grade item with grade.
     * (CONTRIB_5863)
     *
     * @return void
     */
    public function test_grade_item_migration_via_update_with_score() {
        global $DB;

        $this->set_user('admin');

        $quizitemparams = array('itemtype' => 'mod', 'itemmodule' => 'quiz');
        $mhaairsitemparams = array('itemtype' => 'manual', 'itemmodule' => 'mhaairs');

        $this->assertEquals(0, $DB->count_records('grade_items'));

        $service = 'block_mhaairs_gradebookservice_external::update_grade';
        $iteminstance = 24993;
        $itemdetails = array(
            'itemname' => 'mhaairs-quiz',
            'itemtype' => 'mod',
            'idnumber' => '0',
        );
        $itemdetailsjson = urlencode(json_encode($itemdetails));
        $grades = array(
            'userid' => 'student1',
            'rawgrade' => 93,
        );
        $gradesjson = urlencode(json_encode($grades));

        // Create a mod/quiz grade item directly.
        $result = grade_update(
            'mhaairs',
            $this->course->id,
            'mod',
            'quiz',
            $iteminstance,
            0,
            null,
            $itemdetails
        );

        $itemcount = $DB->count_records('grade_items', $quizitemparams);
        $this->assertEquals(1, $itemcount);

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
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        // No quiz items.
        $itemcount = $DB->count_records('grade_items', $quizitemparams);
        $this->assertEquals(0, $itemcount);

        // 1 mhaairs item.
        $itemcount = $DB->count_records('grade_items', $mhaairsitemparams);
        $this->assertEquals(1, $itemcount);

        // Update score.
        $servicedata['grades'] = $gradesjson;
        $result = call_user_func_array($service, $servicedata);
        $this->assertEquals(GRADE_UPDATE_OK, $result);

        // Verify score updated.
        $usergrade = $DB->get_field('grade_grades', 'finalgrade', array('userid' => $this->student1->id));
        $this->assertEquals(93, (int) $usergrade);
    }

}
