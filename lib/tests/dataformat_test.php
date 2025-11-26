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

namespace core;

use coding_exception;
use context_system;
use core_component;

/**
 * Tests for the dataformat plugins
 *
 * @package    core
 * @covers     \core\dataformat
 * @copyright  2020 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class dataformat_test extends \advanced_testcase {

    /**
     * Test getting writer instance for given dataformat
     */
    public function test_get_format_instance(): void {
        $instance = dataformat::get_format_instance('pdf');
        $this->assertInstanceOf(\dataformat_pdf\writer::class, $instance);
    }

    /**
     * Test getting writer instance for invalid dataformat
     */
    public function test_get_format_instance_invalid(): void {
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid dataformat (weird)');
        dataformat::get_format_instance('weird');
    }

    /**
     * Data provider to return array of dataformat types
     *
     * @return array
     */
    public static function write_data_provider(): array {
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
     * @dataProvider write_data_provider
     */
    public function test_write_data(string $dataformat): void {
        $columns = ['fruit', 'colour', 'animal'];
        $rows = [
            ['banana', 'yellow', 'monkey'],
            ['apple', 'red', 'wolf'],
            ['melon', 'green', 'aardvark'],
        ];

        // Export to file. Assert that the exported file exists and is non-zero in size.
        $exportfile = dataformat::write_data('My export', $dataformat, $columns, $rows);
        $this->assertFileExists($exportfile);
        $this->assertGreaterThan(0, filesize($exportfile));
    }

    /**
     * Test writing dataformat export to filearea
     *
     * @param string $dataformat
     * @return void
     *
     * @dataProvider write_data_provider
     */
    public function test_write_data_to_filearea(string $dataformat): void {
        $this->resetAfterTest();

        $columns = ['fruit', 'colour', 'animal'];
        $rows = [
            ['banana', 'yellow', 'monkey'],
            ['apple', 'red', 'wolf'],
            ['melon', 'green', 'aardvark'],
        ];

        // Export to filearea. Assert that the the file exists in file storage and matches the original file record.
        $filerecord = [
            'contextid' => context_system::instance()->id,
            'component' => 'core_dataformat',
            'filearea' => 'test',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'My export',
        ];

        $file = dataformat::write_data_to_filearea($filerecord, $dataformat, $columns, $rows);
        $this->assertEquals($filerecord['contextid'], $file->get_contextid());
        $this->assertEquals($filerecord['component'], $file->get_component());
        $this->assertEquals($filerecord['filearea'], $file->get_filearea());
        $this->assertEquals($filerecord['itemid'], $file->get_itemid());
        $this->assertEquals($filerecord['filepath'], $file->get_filepath());
        $this->assertStringStartsWith($filerecord['filename'], $file->get_filename());
        $this->assertGreaterThan(0, $file->get_filesize());
    }

    /**
     * Data provider for test_escape_spreadsheet_formula.
     *
     * @return array
     */
    public static function escape_spreadsheet_formula_provider(): array {
        return [
            'null stays null' => [
                null,
                null,
            ],
            'empty string stays empty' => [
                '',
                '',
            ],
            'Formula with tab' => [
                'value' => "\t=SUM(1+1)",
                'expected' => "'\t=SUM(1+1)",
            ],
            'Formula with carriage return' => [
                'value' => "\r=SUM(1+1)",
                'expected' => "'\r=SUM(1+1)",
            ],
            'Formula with new line' => [
                'value' => "\n=SUM(1+1)",
                'expected' => "'\n=SUM(1+1)",
            ],
            'Formula starting with "="' => [
                'value' => "=SUM(1+1)",
                'expected' => "'=SUM(1+1)",
            ],
            'Formula starting with "+"' => [
                'value' => "+1+1",
                'expected' => "'+1+1",
            ],
            'Formula starting with "-"' => [
                'value' => "-1+1",
                'expected' => "'-1+1",
            ],
            'Formula starting with "@"' => [
                'value' => "@A5",
                'expected' => "'@A5",
            ],
            'Null placeholder' => [
                'value' => "-",
                'expected' => "-",
            ],
            'dash with leading space is not placeholder, so escaped' => [
                ' -',
                "' -",
            ],
            'dash with trailing space is not placeholder, so escaped' => [
                '- ',
                "'- ",
            ],
            'Non-formula' => [
                'value' => "Hello there",
                'expected' => "Hello there",
            ],
        ];
    }

    /**
     * Test escape_spreadsheet_formula.
     *
     * @dataProvider escape_spreadsheet_formula_provider
     * @param string|null $value The value to test.
     * @param string|null $expected The expected value after escaping.
     */
    public function test_escape_spreadsheet_formula(?string $value, ?string $expected): void {
        $this->resetAfterTest();

        $escapedvalue = dataformat::escape_spreadsheet_formula($value);
        $this->assertEquals($expected, $escapedvalue);
    }
}
