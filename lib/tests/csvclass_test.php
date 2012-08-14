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
 * Tests csv import and export functions
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/csvlib.class.php');

class csvclass_testcase extends advanced_testcase {

    var $testdata = array();
    var $teststring = '';

    protected function setUp(){

        $this->resetAfterTest(true);

        $csvdata = array();
        $csvdata[0][] = 'fullname';
        $csvdata[0][] = 'description of things';
        $csvdata[0][] = 'beer';
        $csvdata[1][] = 'William B Stacey';
        $csvdata[1][] = '<p>A field that contains "double quotes"</p>';
        $csvdata[1][] = 'Asahi';
        $csvdata[2][] = 'Phillip Jenkins';
        $csvdata[2][] = '<p>This field has </p>
<p>Multiple lines</p>
<p>and also contains "double quotes"</p>';
        $csvdata[2][] = 'Yebisu';
        $this->testdata = $csvdata;

        // Please note that each line needs a carriage return.
        $this->teststring = 'fullname,"description of things",beer
"William B Stacey","<p>A field that contains ""double quotes""</p>",Asahi
"Phillip Jenkins","<p>This field has </p>
<p>Multiple lines</p>
<p>and also contains ""double quotes""</p>",Yebisu
';
    }

    public function test_csv_functions() {
        $csvexport = new csv_export_writer();
        $csvexport->set_filename('unittest');
        foreach ($this->testdata as $data) {
            $csvexport->add_data($data);
        }
        $csvoutput = $csvexport->print_csv_data(true);
        $this->assertEquals($csvoutput, $this->teststring);

        $test_data = csv_export_writer::print_array($this->testdata, 'comma', '"', true);
        $this->assertEquals($test_data, $this->teststring);
    }
}
