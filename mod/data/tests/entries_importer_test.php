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
use mod_data\local\importer\csv_entries_importer;
use zip_archive;

/**
 * Unit tests for entries_importer and csv_entries_importer class.
 *
 * Also {@see entries_import_test} class which provides module tests for importing entries.
 *
 * @package    mod_data
 * @covers     \mod_data\local\importer\entries_importer
 * @covers     \mod_data\local\importer\csv_entries_importer
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entries_importer_test extends \advanced_testcase {

    /**
     * Set up function.
     */
    protected function setUp(): void {
        parent::setUp();

        global $CFG;
        require_once($CFG->dirroot . '/mod/data/lib.php');
        require_once($CFG->dirroot . '/lib/datalib.php');
        require_once($CFG->dirroot . '/lib/csvlib.class.php');
        require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
        require_once($CFG->dirroot . '/mod/data/tests/generator/lib.php');
    }

    /**
     * Get the test data.
     * In this instance we are setting up database records to be used in the unit tests.
     *
     * @return array
     */
    protected function get_test_data(): array {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($teacher);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student', array('username' => 'student'));

        $data = $generator->create_instance(array('course' => $course->id));
        $cm = get_coursemodule_from_instance('data', $data->id);

        // Add fields.
        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'ID'; // Identifier of the records for testing.
        $fieldrecord->type = 'number';
        $generator->create_field($fieldrecord, $data);

        $fieldrecord->name = 'Param2';
        $fieldrecord->type = 'text';
        $generator->create_field($fieldrecord, $data);

        $fieldrecord->name = 'filefield';
        $fieldrecord->type = 'file';
        $generator->create_field($fieldrecord, $data);

        $fieldrecord->name = 'picturefield';
        $fieldrecord->type = 'picture';
        $generator->create_field($fieldrecord, $data);

        return [
            'teacher' => $teacher,
            'student' => $student,
            'data' => $data,
            'cm' => $cm,
        ];
    }

    /**
     * Test importing files from zip archive.
     *
     * @covers \mod_data\local\importer\entries_importer::get_file_content_from_zip
     * @covers \mod_data\local\importer\entries_importer::get_data_file_content
     * @dataProvider get_file_content_from_zip_provider
     * @param array $files array of filenames and filecontents to test
     * @param mixed $datafilecontent the expected result returned by the method which is being tested here
     */
    public function test_get_file_content_from_zip(array $files, mixed $datafilecontent): void {
        // First we need to create the zip file from the provided data.
        $tmpdir = make_request_directory();
        $zipfilepath = $tmpdir . '/entries_importer_test_tmp_' . time() . '.zip';
        $ziparchive = new zip_archive();
        $ziparchive->open($zipfilepath);
        foreach ($files as $file) {
            $localname = empty($file['subdir']) ? $file['filename'] : $file['subdir'] . '/' . $file['filename'];
            $ziparchive->add_file_from_string($localname, $file['filecontent']);
        }
        $ziparchive->close();

        // We now created a zip archive according to the data provider's data. We now can test the importer.
        $importer = new csv_entries_importer($zipfilepath, 'testzip.zip');
        foreach ($files as $file) {
            $subdir = empty($file['subdir']) ? '' : $file['subdir'];
            $this->assertEquals($file['filecontent'], $importer->get_file_content_from_zip($file['filename'], $subdir));
        }

        // Test the method to retrieve the datafile content.
        $this->assertEquals($datafilecontent, $importer->get_data_file_content());
        unlink($zipfilepath);
    }

    /**
     * Data provider method for self::test_get_file_content_from_zip.
     *
     * @return array data for testing
     */
    public static function get_file_content_from_zip_provider(): array {
        return [
            'some files in the zip archive' => [
                'files' => [
                    [
                        'filename' => 'datafile.csv',
                        'filecontent' => 'some,csv,data'
                    ],
                    [
                        'filename' => 'testfile.txt',
                        'filecontent' => 'somecontent',
                        'subdir' => 'files'
                    ],
                    [
                        'filename' => 'testfile2.txt',
                        'filecontent' => 'someothercontent',
                        'subdir' => 'testsubdir'
                    ]
                ],
                // Should be identical with filecontent of 'datafile.csv' above.
                'datafilecontent' => 'some,csv,data'
            ],
            'wrongly placed data file' => [
                'files' => [
                    [
                        'filename' => 'datafile.csv',
                        'filecontent' => 'some,csv,data',
                        'subdir' => 'wrongsubdir'
                    ],
                    [
                        'filename' => 'testfile.txt',
                        'filecontent' => 'somecontent',
                        'subdir' => 'files'
                    ],
                    [
                        'filename' => 'testfile2.txt',
                        'filecontent' => 'someothercontent',
                        'subdir' => 'testsubdir'
                    ]
                ],
                // Data file is not in the root directory, though no content should be retrieved.
                'datafilecontent' => false
            ],
            'two data files where only one is allowed' => [
                'files' => [
                    [
                        'filename' => 'datafile.csv',
                        'filecontent' => 'some,csv,data',
                    ],
                    [
                        'filename' => 'anothercsvfile.csv',
                        'filecontent' => 'some,other,csv,data',
                    ],
                    [
                        'filename' => 'testfile.txt',
                        'filecontent' => 'somecontent',
                        'subdir' => 'files'
                    ],
                    [
                        'filename' => 'testfile2.txt',
                        'filecontent' => 'someothercontent',
                        'subdir' => 'testsubdir'
                    ]
                ],
                // There are two data files in the zip root, so the data cannot be imported.
                'datafilecontent' => false
            ],
        ];
    }
}
