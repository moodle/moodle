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
 * @group       block_mhaairs_gradebookservice_get_grade_test
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_gradebookservice_get_grade_testcase extends block_mhaairs_testcase {

    /**
     * Tests the gradebookservice get grade service.
     *
     * @return void
     */
    public function test_get_grade() {
        global $DB;

        $callback = 'block_mhaairs_gradebookservice_external::get_grade';
        $this->set_user('admin');

        // Add mhaairs grade item directly.
        $params = array(
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 101,
            'itemname' => 'MH Assignment',
        );
        $gitem = new \grade_item($params, false);
        $gitem->insert('mhaairs');

        // Add user grade directly.
        $params = array(
            'itemid' => $gitem->id,
            'userid' => $this->student1->id,
            'finalgrade' => '95',
        );
        $ggrade = new \grade_grade($params, false);
        $ggrade->insert('mhaairs');

        // Service params.
        $serviceparams = array(
            'source' => 'mhaairs',
            'courseid' => $this->course->id,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => 101,
            'itemnumber' => 0,
            'grades' => null,
            'itemdetails' => null,
        );

        // Grade details.
        $grades = array(
            'userid' => 'student1',
            'identity_type' => '',
        );
        $gradesjson = urlencode(json_encode($grades));
        $serviceparams['grades'] = $gradesjson;

        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals('MH Assignment', $result['item']['itemname']);
        $this->assertEquals($this->student1->id, $result['grades'][0]['userid']);
        $this->assertEquals(95, $result['grades'][0]['grade']);
    }

}
