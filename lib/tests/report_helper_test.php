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
 * Tests for report_helper.
 *
 * @package    core
 * @category   test
 * @copyright  2021 Sujith Haridasan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use moodle_url;
use core\report_helper;

/**
 * Tests the functions for report_helper class.
 */
class report_helper_test extends \advanced_testcase {
    /**
     * Data provider for testing selected report for same and different courses
     *
     * @return array
     */
    public function data_selected_report(): array {
        return [
            ['course_url_id' => [
                ['url' => '/test', 'id' => 1],
                ['url' => '/foo', 'id' => 1]]
            ],
            ['course_url_id' => [
                ['url' => '/test', 'id' => 1],
                ['url' => '/foo/bar', 'id' => 2]]
            ]
        ];
    }

    /**
     * Testing selected report saved in $USER session.
     *
     * @dataProvider data_selected_report
     * @param array $courseurlid The array has both course url and course id
     */
    public function test_save_selected_report(array $courseurlid): void {
        global $USER;

        $url1 = new moodle_url($courseurlid[0]['url']);
        $courseid1 = $courseurlid[0]['id'];
        report_helper::save_selected_report($courseid1, $url1);
        $this->assertDebuggingCalled('save_selected_report() has been deprecated because it is no ' .
            'longer used and will be removed in future versions of Moodle');

        $this->assertEquals($USER->course_last_report[$courseid1], $url1);

        $url2 = new moodle_url($courseurlid[1]['url']);
        $courseid2 = $courseurlid[1]['id'];
        report_helper::save_selected_report($courseid2, $url2);
        $this->assertDebuggingCalled('save_selected_report() has been deprecated because it is no ' .
            'longer used and will be removed in future versions of Moodle');

        $this->assertEquals($USER->course_last_report[$courseid2], $url2);
    }
}
