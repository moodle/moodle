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
 * Unit tests for (some of) statisticslib.php.
 *
 * @package   quiz_statistics
 * @category  test
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/report/statistics/statisticslib.php');

/**
 * Unit tests for (some of) statisticslib.php.
 *
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_statistics_statisticslib_testcase extends basic_testcase {

    public function test_quiz_statistics_renumber_placeholders_no_op() {
        list($sql, $params) = quiz_statistics_renumber_placeholders(
                ' IN (:u1, :u2)', array('u1' => 1, 'u2' => 2), 'u');
        $this->assertEquals(' IN (:u1, :u2)', $sql);
        $this->assertEquals(array('u1' => 1, 'u2' => 2), $params);
    }

    public function test_quiz_statistics_renumber_placeholders_work_to_do() {
        list($sql, $params) = quiz_statistics_renumber_placeholders(
                'frog1 IN (:frog100 , :frog101)', array('frog100' => 1, 'frog101' => 2), 'frog');
        $this->assertEquals('frog1 IN (:frog1 , :frog2)', $sql);
        $this->assertEquals(array('frog1' => 1, 'frog2' => 2), $params);
    }
}
