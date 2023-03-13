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

use coding_exception;
use context_module;
use dml_exception;
use mod_data\local\csv_exporter;
use mod_data\local\exporter_utils;
use mod_data\local\mod_data_csv_importer;

/**
 * Unit tests for import.php.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2019 Tobias Reischmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_test extends \advanced_testcase {

    /**
     * Get the test data.
     *
     * In this instance we are setting up database records to be used in the unit tests.
     *
     * @return array of test instances
     * @throws coding_exception
     */
    protected function get_test_data(): array {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($teacher);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student', ['username' => 'student']);

        $data = $generator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('data', $data->id);

        // Add fields.
        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'numberfield'; // Identifier of the records for testing.
        $fieldrecord->type = 'number';
        $numberfield = $generator->create_field($fieldrecord, $data);

        $fieldrecord->name = 'textfield';
        $fieldrecord->type = 'text';
        $textfield = $generator->create_field($fieldrecord, $data);

        $fieldrecord->name = 'filefield1';
        $fieldrecord->type = 'file';
        $filefield1 = $generator->create_field($fieldrecord, $data);

        $fieldrecord->name = 'filefield2';
        $fieldrecord->type = 'file';
        $filefield2 = $generator->create_field($fieldrecord, $data);

        $fieldrecord->name = 'picturefield';
        $fieldrecord->type = 'picture';
        $picturefield = $generator->create_field($fieldrecord, $data);

        $contents[$numberfield->field->id] = '3';
        $contents[$textfield->field->id] = 'a simple text';
        $contents[$filefield1->field->id] = 'samplefile.png';
        $contents[$filefield2->field->id] = 'samplefile.png';
        $contents[$picturefield->field->id] = ['picturefile.png', 'this picture shows something'];
        $generator->create_entry($data, $contents);

        return [
            'teacher' => $teacher,
            'student' => $student,
            'data' => $data,
            'cm' => $cm,
        ];
    }

    /**
     * Tests the exporting of the content of a mod_data instance.
     *
     * @covers \mod_data\local\exporter
     * @covers \mod_data\local\exporter_utils::data_exportdata
     */
    public function test_export(): void {
        global $DB;
        [
            'data' => $data,
            'cm' => $cm,
        ] = $this->get_test_data();

        $exporter = new csv_exporter();
        $exporter->set_export_file_name('testexportfile');
        $fieldrecords = $DB->get_records('data_fields', ['dataid' => $data->id], 'id');

        $fields = [];
        foreach ($fieldrecords as $fieldrecord) {
            $fields[] = data_get_field($fieldrecord, $data);
        }

        // We select all fields.
        $selectedfields = array_map(fn($field) => $field->field->id, $fields);
        $currentgroup = groups_get_activity_group($cm);
        $context = context_module::instance($cm->id);
        $exportuser = false;
        $exporttime = false;
        $exportapproval = false;
        $tags = false;
        // We first test the export without exporting files.
        // This means file and picture fields will be exported, but only as text (which is the filename),
        // so we will receive a csv export file.
        $includefiles = false;
        exporter_utils::data_exportdata($data->id, $fields, $selectedfields, $exporter, $currentgroup, $context,
            $exportuser, $exporttime, $exportapproval, $tags, $includefiles);
        $this->assertEquals(file_get_contents(__DIR__ . '/fixtures/test_data_export_without_files.csv'),
            $exporter->send_file(false));

        // We now test the export including files. This will generate a zip archive.
        $includefiles = true;
        $exporter = new csv_exporter();
        $exporter->set_export_file_name('testexportfile');
        exporter_utils::data_exportdata($data->id, $fields, $selectedfields, $exporter, $currentgroup, $context,
            $exportuser, $exporttime, $exportapproval, $tags, $includefiles);
        // We now write the zip archive temporary to disc to be able to parse it and assert it has the correct structure.
        $tmpdir = make_request_directory();
        file_put_contents($tmpdir . '/testexportarchive.zip', $exporter->send_file(false));
        $ziparchive = new \zip_archive();
        $ziparchive->open($tmpdir . '/testexportarchive.zip');
        $expectedfilecontents = [
            // The test generator for mod_data uses a copy of pix/monologo.png as sample file content for the file stored in a
            // file and picture field.
            // So we expect that this file has to have the same content as monologo.png.
            // Also, the default value for the subdirectory in the zip archive containing the files is 'files/'.
            'files/samplefile.png' => 'mod/data/pix/monologo.png',
            'files/samplefile_1.png' => 'mod/data/pix/monologo.png',
            'files/picturefile.png' => 'mod/data/pix/monologo.png',
            // By checking that the content of the exported csv is identical to the fixture file it is verified
            // that the filenames in the csv file correspond to the names of the exported file.
            // It also verifies that files with identical file names in different fields (or records) will be numbered
            // automatically (samplefile.png, samplefile_1.png, ...).
            'testexportfile.csv' => __DIR__ . '/fixtures/test_data_export_with_files.csv'
        ];
        for ($i = 0; $i < $ziparchive->count(); $i++) {
            // We here iterate over all files in the zip archive and check if their content is identical to the files
            // in the $expectedfilecontents array.
            $filestream = $ziparchive->get_stream($i);
            $fileinfo = $ziparchive->get_info($i);
            $filecontent = fread($filestream, $fileinfo->size);
            $this->assertEquals(file_get_contents($expectedfilecontents[$fileinfo->pathname]), $filecontent);
            fclose($filestream);
        }
        $ziparchive->close();
    }
}
