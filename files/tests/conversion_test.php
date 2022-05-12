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

namespace core_files;

/**
 * PHPUnit tests for conversion persistent.
 *
 * @package    core_files
 * @copyright  2017 Andrew nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class conversion_test extends \advanced_testcase {

    /**
     * Helper to create a stored file object with the given supplied content.
     *
     * @param   string $filecontent The content of the mocked file
     * @param   string $filename The file name to use in the stored_file
     * @param   string $filerecord Any overrides to the filerecord
     * @return  stored_file
     */
    protected function create_stored_file($filecontent = 'content', $filename = 'testfile.txt', $filerecord = []) {
        $filerecord = array_merge([
                'contextid' => \context_system::instance()->id,
                'component' => 'core',
                'filearea'  => 'unittest',
                'itemid'    => 0,
                'filepath'  => '/',
                'filename'  => $filename,
            ], $filerecord);

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, $filecontent);

        return $file;
    }

    /**
     * Ensure that get_conversions_for_file returns an existing conversion
     * record with matching sourcefileid and targetformat.
     */
    public function test_get_conversions_for_file_existing_conversion_incomplete() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();

        $existing = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'pdf',
            ]);
        $existing->create();

        $conversions = conversion::get_conversions_for_file($sourcefile, 'pdf');

        $this->assertCount(1, $conversions);

        $conversion = array_shift($conversions);
        $conversionfile = $conversion->get_sourcefile();

        $this->assertEquals($sourcefile->get_id(), $conversionfile->get_id());
        $this->assertFalse($conversion->get_destfile());
    }

    /**
     * Ensure that get_conversions_for_file returns an existing conversion
     * record with matching sourcefileid and targetformat when a second
     * conversion to a different format exists.
     */
    public function test_get_conversions_for_file_existing_conversion_multiple_formats_incomplete() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();

        $existing = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'pdf',
            ]);
        $existing->create();

        $second = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'doc',
            ]);
        $second->create();

        $conversions = conversion::get_conversions_for_file($sourcefile, 'pdf');

        $this->assertCount(1, $conversions);

        $conversion = array_shift($conversions);
        $conversionfile = $conversion->get_sourcefile();

        $this->assertEquals($sourcefile->get_id(), $conversionfile->get_id());
        $this->assertFalse($conversion->get_destfile());
    }

    /**
     * Ensure that get_conversions_for_file returns an existing conversion
     * record with matching sourcefileid and targetformat.
     */
    public function test_get_conversions_for_file_existing_conversion_complete() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();
        $destfile = $this->create_stored_file(
            'example content',
            $sourcefile->get_contenthash(),
            [
                'component' => 'core',
                'filearea' => 'documentconversion',
                'filepath' => '/pdf/',
            ]);

        $existing = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'pdf',
                'destfileid' => $destfile->get_id(),
            ]);
        $existing->create();

        $conversions = conversion::get_conversions_for_file($sourcefile, 'pdf');

        // Only one file should be returned.
        $this->assertCount(1, $conversions);

        $conversion = array_shift($conversions);

        $this->assertEquals($sourcefile->get_id(), $conversion->get_sourcefile()->get_id());
        $this->assertEquals($destfile->get_id(), $conversion->get_destfile()->get_id());
    }

    /**
     * Ensure that get_conversions_for_file returns an existing conversion
     * record with matching sourcefileid and targetformat.
     */
    public function test_get_conversions_for_file_existing_conversion_multiple_formats_complete() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();
        $destfile = $this->create_stored_file(
            'example content',
            $sourcefile->get_contenthash(),
            [
                'component' => 'core',
                'filearea' => 'documentconversion',
                'filepath' => '/pdf/',
            ]);

        $existing = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'pdf',
                'destfileid' => $destfile->get_id(),
            ]);
        $existing->create();

        $second = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'doc',
            ]);
        $second->create();

        $conversions = conversion::get_conversions_for_file($sourcefile, 'pdf');

        // Only one file should be returned.
        $this->assertCount(1, $conversions);

        $conversion = array_shift($conversions);

        $this->assertEquals($sourcefile->get_id(), $conversion->get_sourcefile()->get_id());
        $this->assertEquals($destfile->get_id(), $conversion->get_destfile()->get_id());
    }

    /**
     * Ensure that get_conversions_for_file returns an existing conversion
     * record does not exist, but the file has previously been converted.
     */
    public function test_get_conversions_for_file_existing_target() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();
        $destfile = $this->create_stored_file(
            'example content',
            $sourcefile->get_contenthash(),
            [
                'component' => 'core',
                'filearea' => 'documentconversion',
                'filepath' => '/pdf/',
            ]);

        $conversions = conversion::get_conversions_for_file($sourcefile, 'pdf');

        $this->assertCount(1, $conversions);

        $conversion = array_shift($conversions);
        $conversionsource = $conversion->get_sourcefile();
        $this->assertEquals($sourcefile->get_id(), $conversionsource->get_id());
        $conversiondest = $conversion->get_destfile();
        $this->assertEquals($destfile->get_id(), $conversiondest->get_id());
    }

    /**
     * Ensure that set_sourcefile sets the correct fileid.
     */
    public function test_set_sourcefile() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();
        $conversion = new conversion(0, (object) []);

        $conversion->set_sourcefile($sourcefile);

        $this->assertEquals($sourcefile->get_id(), $conversion->get('sourcefileid'));
        $this->assertNull($conversion->get('destfileid'));
    }

    /**
     * Ensure that store_destfile_from_path stores the file as expected.
     */
    public function test_store_destfile_from_path() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();
        $conversion = new conversion(0, (object) [
            'sourcefileid' => $sourcefile->get_id(),
            'targetformat' => 'pdf',
        ]);

        $fixture = __FILE__;
        $conversion->store_destfile_from_path($fixture);

        $destfile = $conversion->get_destfile();
        $this->assertEquals(file_get_contents($fixture), $destfile->get_content());
    }

    /**
     * Ensure that store_destfile_from_path stores the file as expected.
     */
    public function test_store_destfile_from_path_delete_existing() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();
        $conversion = new conversion(0, (object) [
            'sourcefileid' => $sourcefile->get_id(),
            'targetformat' => 'pdf',
        ]);

        $record = [
            'contextid' => \context_system::instance()->id,
            'component' => 'core',
            'filearea'  => 'documentconversion',
            'itemid'    => 0,
            'filepath'  => '/pdf/',
        ];
        $existingfile = $this->create_stored_file('foo', $sourcefile->get_contenthash(), $record);

        $fixture = __FILE__;
        $conversion->store_destfile_from_path($fixture);

        $destfile = $conversion->get_destfile();
        $this->assertEquals(file_get_contents($fixture), $destfile->get_content());
    }

    /**
     * Ensure that store_destfile_from_path stores the file as expected.
     */
    public function test_store_destfile_from_string() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();
        $conversion = new conversion(0, (object) [
            'sourcefileid' => $sourcefile->get_id(),
            'targetformat' => 'pdf',
        ]);

        $fixture = 'Example content';
        $conversion->store_destfile_from_string($fixture);

        $destfile = $conversion->get_destfile();
        $this->assertEquals($fixture, $destfile->get_content());
    }

    /**
     * Ensure that store_destfile_from_string stores the file as expected when
     * an existing destfile is found.
     */
    public function test_store_destfile_from_string_delete_existing() {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();
        $conversion = new conversion(0, (object) [
            'sourcefileid' => $sourcefile->get_id(),
            'targetformat' => 'pdf',
        ]);

        $record = [
            'contextid' => \context_system::instance()->id,
            'component' => 'core',
            'filearea'  => 'documentconversion',
            'itemid'    => 0,
            'filepath'  => '/pdf/',
        ];
        $existingfile = $this->create_stored_file('foo', $sourcefile->get_contenthash(), $record);

        $fixture = 'Example content';
        $conversion->store_destfile_from_string($fixture);

        $destfile = $conversion->get_destfile();
        $this->assertEquals($fixture, $destfile->get_content());
    }

    /**
     * Ensure that the get_status functions cast the status to integer correctly.
     */
    public function test_get_status() {
        $conversion = new conversion(0, (object) [
            'status' => (string) 1,
        ]);

        $this->assertIsInt($conversion->get('status'));
    }

    /**
     * Ensure that get_converter_instance returns false when no converter is set.
     */
    public function test_get_converter_instance_none_set() {
        $conversion = new conversion(0, (object) []);
        $this->assertFalse($conversion->get_converter_instance());
    }

    /**
     * Ensure that get_converter_instance returns false when no valid converter is set.
     */
    public function test_get_converter_instance_invalid_set() {
        $conversion = new conversion(0, (object) [
            'converter' => '\\fileconverter_not_a_valid_converter\\converter',
        ]);
        $this->assertFalse($conversion->get_converter_instance());
    }

    /**
     * Ensure that get_converter_instance returns an instance when a valid converter is set.
     */
    public function test_get_converter_instance_valid_set() {
        $conversion = new conversion(0, (object) [
            'converter' => \fileconverter_unoconv\converter::class,
        ]);
        $this->assertInstanceOf(\fileconverter_unoconv\converter::class, $conversion->get_converter_instance());
    }

    /**
     * Test that all old conversion records are removed periodically.
     */
    public function test_remove_old_conversion_records_old() {
        $this->resetAfterTest();
        global $DB;

        $sourcefile = $this->create_stored_file();
        $conversion = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'pdf',
            ]);
        $conversion->create();
        $DB->set_field(conversion::TABLE, 'timemodified', time() - YEARSECS);

        conversion::remove_old_conversion_records();

        $this->assertEquals(0, $DB->count_records(conversion::TABLE));
    }

    /**
     * Test that all old conversion records are removed periodically.
     */
    public function test_remove_old_conversion_records_young() {
        $this->resetAfterTest();
        global $DB;

        $sourcefile = $this->create_stored_file();
        $conversion = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'pdf',
            ]);
        $conversion->create();
        $DB->set_field(conversion::TABLE, 'timemodified', time() - DAYSECS);

        conversion::remove_old_conversion_records();

        $this->assertEquals(1, $DB->count_records(conversion::TABLE));
    }

    /**
     * Test orphan records are removed.
     */
    public function test_remove_orphan_records() {
        global $DB;
        $this->resetAfterTest();

        $sf1 = $this->create_stored_file('1', '1');
        $sf2 = $this->create_stored_file('2', '2');
        $sf3 = $this->create_stored_file('3', '3');
        $c1 = new conversion(0, (object) ['sourcefileid' => $sf1->get_id(), 'targetformat' => 'pdf']);
        $c1->create();
        $c2 = new conversion(0, (object) ['sourcefileid' => $sf2->get_id(), 'targetformat' => 'pdf']);
        $c2->create();
        $c3 = new conversion(0, (object) ['sourcefileid' => $sf3->get_id(), 'targetformat' => 'pdf']);
        $c3->create();

        $this->assertTrue(conversion::record_exists($c1->get('id')));
        $this->assertTrue(conversion::record_exists($c2->get('id')));
        $this->assertTrue(conversion::record_exists($c3->get('id')));

        // Nothing should happen here.
        conversion::remove_orphan_records();
        $this->assertTrue(conversion::record_exists($c1->get('id')));
        $this->assertTrue(conversion::record_exists($c2->get('id')));
        $this->assertTrue(conversion::record_exists($c3->get('id')));

        // Delete file #2.
        $sf2->delete();
        conversion::remove_orphan_records();
        $this->assertTrue(conversion::record_exists($c1->get('id')));
        $this->assertFalse(conversion::record_exists($c2->get('id')));
        $this->assertTrue(conversion::record_exists($c3->get('id')));

        // Delete file #1, #3.
        $sf1->delete();
        $sf3->delete();
        conversion::remove_orphan_records();
        $this->assertFalse(conversion::record_exists($c1->get('id')));
        $this->assertFalse(conversion::record_exists($c2->get('id')));
        $this->assertFalse(conversion::record_exists($c3->get('id')));
    }
}
