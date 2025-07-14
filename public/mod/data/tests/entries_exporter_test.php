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

namespace mod_data;

use context_module;
use mod_data\local\exporter\csv_entries_exporter;
use mod_data\local\exporter\ods_entries_exporter;
use mod_data\local\exporter\utils;

/**
 * Unit tests for entries_exporter and csv_entries_exporter classes.
 *
 * Also {@see entries_export_test} class which provides module tests for exporting entries.
 *
 * @package    mod_data
 * @covers     \mod_data\local\exporter\entries_exporter
 * @covers     \mod_data\local\exporter\csv_entries_exporter
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class entries_exporter_test extends \advanced_testcase {

    /**
     * Tests get_records_count method.
     *
     * @covers \mod_data\local\exporter\entries_exporter::get_records_count
     * @dataProvider get_records_count_provider
     * @param array $rows the rows from the data provider to be tested by the exporter
     * @param int $expectedcount the expected count of records to be exported
     */
    public function test_get_records_count(array $rows, int $expectedcount): void {
        $exporter = new csv_entries_exporter();
        foreach ($rows as $row) {
            $exporter->add_row($row);
        }
        $this->assertEquals($expectedcount, $exporter->get_records_count());
    }

    /**
     * Data provider method for self::test_get_records_count.
     *
     * @return array data for testing
     */
    public static function get_records_count_provider(): array {
        return [
            'onlyheader' => [
                'rows' => [
                    ['numberfield', 'textfield', 'filefield1', 'filefield2', 'picturefield']
                ],
                'expectedcount' => 0 // Only header present, so we expect record count 0.
            ],
            'onerecord' => [
                'rows' => [
                    ['numberfield', 'textfield', 'filefield1', 'filefield2', 'picturefield'],
                    ['3', 'a simple text', 'samplefile.png', 'samplefile_1.png', 'picturefile.png']
                ],
                'expectedcount' => 1
            ],
            'tworecords' => [
                'rows' => [
                    ['numberfield', 'textfield', 'filefield1', 'filefield2', 'picturefield'],
                    ['3', 'a simple text', 'samplefile.png', 'samplefile_1.png', 'picturefile.png'],
                    ['5', 'a supersimple text', 'anotherfile.png', 'someotherfile.png', 'andapicture.png']
                ],
                'expectedcount' => 2
            ]
        ];
    }

    /**
     * Tests adding of files to the exporter to be included in the exported zip archive.
     *
     * @dataProvider add_file_from_string_provider
     * @covers \mod_data\local\exporter\entries_exporter::add_file_from_string
     * @covers \mod_data\local\exporter\entries_exporter::file_exists
     * @param array $files array of filename and filecontent to be tested for exporting
     * @param bool $success if the exporting of files should be successful
     */
    public function test_add_file_from_string(array $files, bool $success): void {
        $exporter = new csv_entries_exporter();
        foreach ($files as $file) {
            if (empty($file['subdir'])) {
                $exporter->add_file_from_string($file['filename'], $file['filecontent']);
                $this->assertEquals($exporter->file_exists($file['filename']), $success);
            } else {
                $exporter->add_file_from_string($file['filename'], $file['filecontent'], $file['subdir']);
                $this->assertEquals($exporter->file_exists($file['filename'], $file['subdir']), $success);
            }
        }
    }

    /**
     * Data provider method for self::test_add_file_from_string.
     *
     * @return array data for testing
     */
    public static function add_file_from_string_provider(): array {
        return [
            'one file' => [
                'files' => [
                    [
                        'filename' => 'testfile.txt',
                        'filecontent' => 'somecontent'
                    ],
                ],
                'success' => true
            ],
            'more files, also with subdirs' => [
                'files' => [
                    [
                        'filename' => 'testfile.txt',
                        'filecontent' => 'somecontent'
                    ],
                    [
                        'filename' => 'testfile2.txt',
                        'filecontent' => 'someothercontent',
                        'subdir' => 'testsubdir'
                    ],
                    [
                        'filename' => 'testfile3.txt',
                        'filecontent' => 'someverydifferentcontent',
                        'subdir' => 'files/foo/bar'
                    ],
                    [
                        'filename' => 'testfile4.txt',
                        'filecontent' => 'someverydifferentcontent',
                        'subdir' => 'files/foo/bar/'
                    ],
                    [
                        'filename' => 'testfile5.txt',
                        'filecontent' => 'someverydifferentcontent',
                        'subdir' => '/files/foo/bar/'
                    ],
                ],
                'success' => true
            ],
            'nocontent' => [
                'files' => [
                    [
                        'filename' => '',
                        'filecontent' => ''
                    ]
                ],
                'success' => false
            ]
        ];
    }

    /**
     * Tests if unique filenames are being created correctly.
     *
     * @covers \mod_data\local\exporter\entries_exporter::create_unique_filename
     * @dataProvider create_unique_filename_provider
     * @param string $inputfilename the name of the file which should be converted into a unique filename
     * @param string $resultfilename the maybe changed $inputfilename, so that it is unique in the exporter
     */
    public function test_create_unique_filename(string $inputfilename, string $resultfilename): void {
        $exporter = new csv_entries_exporter();
        $exporter->add_file_from_string('test.txt', 'somecontent');
        $exporter->add_file_from_string('foo.txt', 'somecontent');
        $exporter->add_file_from_string('foo_1.txt', 'somecontent');
        $exporter->add_file_from_string('foo_2.txt', 'somecontent');
        $exporter->add_file_from_string('foo', 'somecontent');
        $exporter->add_file_from_string('foo_1', 'somecontent');
        $exporter->add_file_from_string('sample_5.txt', 'somecontent');
        $exporter->add_file_from_string('bar_1.txt', 'somecontent');
        $this->assertEquals($resultfilename, $exporter->create_unique_filename($inputfilename));
    }

    /**
     * Data provider method for self::test_create_unique_filename.
     *
     * @return array data for testing
     */
    public static function create_unique_filename_provider(): array {
        return [
            'does not exist yet' => [
                'inputfilename' => 'someuniquename.txt',
                'resultfilename' => 'someuniquename.txt'
            ],
            'already exists' => [
                'inputfilename' => 'test.txt',
                'resultfilename' => 'test_1.txt'
            ],
            'already exists, other numbers as well' => [
                'inputfilename' => 'foo.txt',
                'resultfilename' => 'foo_3.txt'
            ],
            'file with _5 suffix already exists' => [
                'inputfilename' => 'sample_5.txt',
                'resultfilename' => 'sample_5_1.txt'
            ],
            'file with _1 suffix already exists' => [
                'inputfilename' => 'bar_1.txt',
                'resultfilename' => 'bar_1_1.txt'
            ],
            'file without extension unique' => [
                'inputfilename' => 'test',
                'resultfilename' => 'test'
            ],
            'file without extension not unique' => [
                'inputfilename' => 'foo',
                'resultfilename' => 'foo_2'
            ]
        ];
    }
}
