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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/enrol/ues/classes/dao/base.php');
require_once($CFG->dirroot.'/enrol/ues/classes/dao/filter.php');
require_once($CFG->dirroot.'/enrol/ues/publiclib.php');
require_once($CFG->dirroot.'/enrol/ues/classes/dao/daos.php');

class test_fetch_sections extends advanced_testcase{

    public function setup() {
        global $CFG;
        $this->loadDataSet($this->dataset());
        $this->resetAfterTest();
    }

    /**
     * https://github.com/lsuits/cps/issues/51
     *
     */
    public function test_get_sections() {

        $all = ues_semester::get_all();
        $this->assertNotEmpty($all);
        $this->assertEquals(3, count($all));

        // Looking for sections in semesters where classes end > time().
        $filters = ues::where()->grades_due->greater_equal(time());
        $unit = ues_semester::get_all($filters);
        $this->assertNotEmpty($unit);
        $this->assertEquals(2, count($unit));
    }

    private function dataset() {
        $ds = array(
            'enrol_ues_semesters' => array(
                array('id' => 5
                        , 'year' => 2013
                        , 'name' => 'Spring'
                        , 'campus' => 'LSU'
                        , 'session_key' => null
                        , 'classes_start' => time() - 864000
                        , 'grades_due' => time() + 864000),
                array('id' => 4
                        , 'year' => 2011
                        , 'name' => 'Spring'
                        , 'campus' => 'LSU'
                        , 'session_key' => null
                        , 'classes_start' => time() - 19872000
                        , 'grades_due' => time() - 15552000),
                array('id' => 7
                        , 'year' => 2013
                        , 'name' => 'Spring'
                        , 'campus' => 'LSU'
                        , 'session_key' => null
                        , 'classes_start' => time() + 15552000
                        , 'grades_due' => time() + 19872000)
            ),
            'enrol_ues_sections' => array(
                array('id' => 1
                        , 'courseid' => 1
                        , 'semesterid' => 4
                        , 'idnumber' => ' lhjgvfg'
                        , 'sec_number' => '009'
                        , 'status' => 'skipped'),
                array('id' => 2
                        , 'courseid' => 2
                        , 'semesterid' => 4
                        , 'idnumber' => '8035uig'
                        , 'sec_number' => '008'
                        , 'status' => 'manifested'),
                array('id' => 3
                        , 'courseid' => 3
                        , 'semesterid' => 5
                        , 'idnumber' => '8035u456'
                        , 'sec_number' => '007'
                        , 'status' => 'manifested'),
                array('id' => 4
                        , 'courseid' => 4
                        , 'semesterid' => 7
                        , 'idnumber' => '8035u4345'
                        , 'sec_number' => '006'
                        , 'status' => 'manifested'),
            )
        );
        return $this->createArrayDataSet($ds);
    }
}