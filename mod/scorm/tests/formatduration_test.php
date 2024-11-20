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

namespace mod_scorm;

defined('MOODLE_INTERNAL') || die;

// Make sure the code being tested is accessible.
global $CFG;
require_once($CFG->dirroot . '/mod/scorm/locallib.php'); // Include the code to test.

/**
 * Unit tests for scorm_formatduration function from locallib.php
 *
 * @package    mod_scorm
 * @category   test
 * @copyright  2009 Dan Marsden
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class formatduration_test extends \basic_testcase {
    public function test_scorm2004_format(): void {
        $stryears = get_string('years');
        $strmonths = trim(get_string('nummonths'));
        $strdays = get_string('days');
        $strhours = get_string('hours');
        $strminutes = get_string('minutes');
        $strseconds = get_string('seconds');

        $suts = array(1 => 'PT001H012M0043.12S', 2 => 'PT15.3S', 3 => 'P01Y02M5DT0H7M', 4 => 'P0Y0M0DT0H1M00.00S',
            5 => 'P1YT15M00.01S', 6 => 'P0Y0M0DT0H0M0.0S', 7 => 'P1MT4M0.30S', 8 => 'PT', 9 => 'P1DT2H3S', 10 => 'P4M');
        $validates = array(1 => "1 $strhours 12 $strminutes 43.12 $strseconds",
                            2 => "15.3 $strseconds",
                            3 => "1 $stryears 2 $strmonths 5 $strdays 7 $strminutes ",
                            4 => "1 $strminutes ",
                            5 => "1 $stryears 15 $strminutes 0.01 $strseconds",
                            6 => '',
                            7 => "1 $strmonths 4 $strminutes 0.30 $strseconds",
                            8 => '',
                            9 => "1 $strdays 2 $strhours 3 $strseconds",
                            10 => "4 $strmonths ");
        foreach ($suts as $key => $sut) {
            $formatted = scorm_format_duration($sut);
            $this->assertEquals($formatted, $validates[$key]);
        }
    }

    public function test_scorm12_format(): void {
        $stryears = get_string('years');
        $strmonths = trim(get_string('nummonths'));
        $strdays = get_string('days');
        $strhours = get_string('hours');
        $strminutes = get_string('minutes');
        $strseconds = get_string('seconds');

        $suts = array(1 => '00:00:00', 2 => '1:2:3', 3 => '12:34:56.78', 4 => '00:12:00.03', 5 => '01:00:23', 6 => '00:12:34.00',
            7 => '00:01:02.03', 8 => '00:00:00.1', 9 => '1:23:00', 10 => '2:00:00');
        $validates = array(1 => '',
                            2 => "1 $strhours 2 $strminutes 3 $strseconds",
                            3 => "12 $strhours 34 $strminutes 56.78 $strseconds",
                            4 => "12 $strminutes 0.03 $strseconds",
                            5 => "1 $strhours 23 $strseconds",
                            6 => "12 $strminutes 34 $strseconds",
                            7 => "1 $strminutes 2.03 $strseconds",
                            8 => "0.1 $strseconds",
                            9 => "1 $strhours 23 $strminutes ",
                            10 => "2 $strhours ");
        foreach ($suts as $key => $sut) {
            $formatted = scorm_format_duration($sut);
            $this->assertEquals($formatted, $validates[$key]);
        }
    }

    public function test_non_datetime(): void {
    }
}
