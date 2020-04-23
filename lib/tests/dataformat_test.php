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
 * Tests for the dataformat plugins
 *
 * @package    core
 * @copyright  2020 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use core_component;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/dataformatlib.php");

/**
 * Dataformat tests
 *
 * @package    core
 * @copyright  2020 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataformat_testcase extends \advanced_testcase {

    /**
     * Data provider for {@see test_write_file_as_dataformat}
     *
     * @return array
     */
    public function write_file_as_dataformat_provider(): array {
        $data = [];

        $dataformats = core_component::get_plugin_list('dataformat');
        foreach ($dataformats as $dataformat => $unused) {
            $data[] = [$dataformat];
        }

        return $data;
    }

    /**
     * Test writing dataformat export to local file
     *
     * @param string $dataformat
     * @return void
     *
     * @dataProvider write_file_as_dataformat_provider
     */
    public function test_write_file_as_dataformat(string $dataformat): void {
        $columns = ['fruit', 'colour', 'animal'];
        $rows = [
            ['banana', 'yellow', 'monkey'],
            ['apple', 'red', 'wolf'],
            ['melon', 'green', 'aardvark'],
        ];

        // Export to file. Assert that the exported file exists and is non-zero in size.
        $exportfile = write_file_as_dataformat('My export', $dataformat, $columns, $rows);
        $this->assertFileExists($exportfile);
        $this->assertGreaterThan(0, filesize($exportfile));
    }
}
