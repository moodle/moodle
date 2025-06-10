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

namespace dataformat_xml;

use advanced_testcase;
use core_xml_parser;
use core\dataformat;

/**
 * Writer tests
 *
 * @package    dataformat_xml
 * @covers     \dataformat_xml\writer
 * @copyright  2021 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class writer_test extends advanced_testcase {

    /**
     * Load required libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        parent::setUpBeforeClass();

        require_once("{$CFG->libdir}/xmlize.php");
    }

    /**
     * Test writing data
     */
    public function test_write_data(): void {
        $this->resetAfterTest(true);

        $columns = ['animal', 'colour'];
        $rows = [
            ['Cat', 'Green'],
            ['Dog', 'Blue'],
        ];

        $exportfile = dataformat::write_data('My export', 'xml', $columns, $rows);
        $exportfilecontent = file_get_contents($exportfile);

        // Parse file content to XML structure.
        $xml = (new core_xml_parser())->parse($exportfilecontent, 1, 'UTF-8', true);
        $this->assertCount(2, $xml['records']['#']['record']);

        // Assert each row.
        [$row0, $row1] = $xml['records']['#']['record'];

        $this->assertEquals(0, $row0['@']['rowNum']);
        $this->assertEquals('Cat', $row0['#']['animal'][0]['#']);
        $this->assertEquals('Green', $row0['#']['colour'][0]['#']);

        $this->assertEquals(1, $row1['@']['rowNum']);
        $this->assertEquals('Dog', $row1['#']['animal'][0]['#']);
        $this->assertEquals('Blue', $row1['#']['colour'][0]['#']);
    }
}
