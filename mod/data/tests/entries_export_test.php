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
 * Unit tests for exporting entries.
 *
 * @package    mod_data
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entries_export_test extends \advanced_testcase {

    /**
     * Get the test data.
     *
     * In this instance we are setting up database records to be used in the unit tests.
     *
     * @return array of test instances
     */
    protected function get_test_data(): array {
        $this->resetAfterTest(true);

        /** @var \mod_data_generator $generator */
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
     * Tests the exporting of the content of a mod_data instance by using the csv_entries_exporter.
     *
     * It also includes more general testing of the functionality of the entries_exporter the csv_entries_exporter
     * is inheriting from.
     *
     * @covers \mod_data\local\exporter\entries_exporter
     * @covers \mod_data\local\exporter\entries_exporter::get_records_count()
     * @covers \mod_data\local\exporter\entries_exporter::send_file()
     * @covers \mod_data\local\exporter\csv_entries_exporter
     * @covers \mod_data\local\exporter\utils::data_exportdata
     */
    public function test_export_csv(): void {
        global $DB;
        [
            'data' => $data,
            'cm' => $cm,
        ] = $this->get_test_data();

        $exporter = new csv_entries_exporter();
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
        utils::data_exportdata($data->id, $fields, $selectedfields, $exporter, $currentgroup, $context,
            $exportuser, $exporttime, $exportapproval, $tags, $includefiles);
        $this->assertEquals(file_get_contents(self::get_fixture_path(__NAMESPACE__, 'test_data_export_without_files.csv')),
            $exporter->send_file(false));

        $this->assertEquals(1, $exporter->get_records_count());

        // We now test the export including files. This will generate a zip archive.
        $includefiles = true;
        $exporter = new csv_entries_exporter();
        $exporter->set_export_file_name('testexportfile');
        utils::data_exportdata($data->id, $fields, $selectedfields, $exporter, $currentgroup, $context,
            $exportuser, $exporttime, $exportapproval, $tags, $includefiles);
        // We now write the zip archive temporary to disc to be able to parse it and assert it has the correct structure.
        $tmpdir = make_request_directory();
        file_put_contents($tmpdir . '/testexportarchive.zip', $exporter->send_file(false));
        $ziparchive = new \zip_archive();
        $ziparchive->open($tmpdir . '/testexportarchive.zip');
        $expectedfilecontents = [
            // The test generator for mod_data uses a copy of field/picture/pix/sample.png as sample file content for the
            // file stored in a file and picture field.
            // So we expect that this file has to have the same content as sample.png.
            // Also, the default value for the subdirectory in the zip archive containing the files is 'files/'.
            'files/samplefile.png' => 'mod/data/field/picture/pix/sample.png',
            'files/samplefile_1.png' => 'mod/data/field/picture/pix/sample.png',
            'files/picturefile.png' => 'mod/data/field/picture/pix/sample.png',
            // By checking that the content of the exported csv is identical to the fixture file it is verified
            // that the filenames in the csv file correspond to the names of the exported file.
            // It also verifies that files with identical file names in different fields (or records) will be numbered
            // automatically (samplefile.png, samplefile_1.png, ...).
            'testexportfile.csv' => self::get_fixture_path(__NAMESPACE__, 'test_data_export_with_files.csv'),
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
        unlink($tmpdir . '/testexportarchive.zip');
    }

    /**
     * Tests specific ODS exporting functionality.
     *
     * @covers \mod_data\local\exporter\ods_entries_exporter
     * @covers \mod_data\local\exporter\utils::data_exportdata
     */
    public function test_export_ods(): void {
        global $DB;
        [
            'data' => $data,
            'cm' => $cm,
        ] = $this->get_test_data();

        $exporter = new ods_entries_exporter();
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
        // so we will receive an ods export file.
        $includefiles = false;
        utils::data_exportdata($data->id, $fields, $selectedfields, $exporter, $currentgroup, $context,
            $exportuser, $exporttime, $exportapproval, $tags, $includefiles);
        $odsrows = $this->get_ods_rows_content($exporter->send_file(false));

        // Check, if the headings match with the first row of the ods file.
        $i = 0;
        foreach ($fields as $field) {
            $this->assertEquals($field->field->name, $odsrows[0][$i]);
            $i++;
        }

        // Check, if the values match with the field values.
        $this->assertEquals('3', $odsrows[1][0]);
        $this->assertEquals('a simple text', $odsrows[1][1]);
        $this->assertEquals('samplefile.png', $odsrows[1][2]);
        $this->assertEquals('samplefile.png', $odsrows[1][3]);
        $this->assertEquals('picturefile.png', $odsrows[1][4]);

        // As the logic of renaming the files and building a zip archive is implemented in entries_exporter class, we do
        // not need to test this for the ods_entries_exporter, because entries_export_test::test_export_csv already does this.
    }

    /**
     * Helper function to extract the text data as row arrays from an ODS document.
     *
     * @param string $content the file content
     * @return array two-dimensional row/column array with the text content of the first spreadsheet
     */
    private function get_ods_rows_content(string $content): array {
        $file = tempnam(make_request_directory(), 'ods_');
        $filestream = fopen($file, "w");
        fwrite($filestream, $content);
        $reader = new \OpenSpout\Reader\ODS\Reader();
        $reader->open($file);
        /** @var \OpenSpout\Reader\ODS\Sheet[] $sheets */
        $sheets = $reader->getSheetIterator();
        $rowscellsvalues = [];
        foreach ($sheets as $sheet) {
            /** @var \OpenSpout\Common\Entity\Row[] $rows */
            $rows = $sheet->getRowIterator();
            foreach ($rows as $row) {
                $cellvalues = [];
                foreach ($row->getCells() as $cell) {
                    $cellvalues[] = $cell->getValue();
                }
                $rowscellsvalues[] = $cellvalues;
            }
        }
        return $rowscellsvalues;
    }
}
