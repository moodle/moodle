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
 * Unit tests for \core_files\local\archive_writer\zip_writer.
 *
 * @package core_files
 * @category test
 * @copyright 2020 Mark Nelson <mdjnelson@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace core_files\local\archive_writer;

use advanced_testcase;
use context_module;
use core_files\archive_writer;
use ZipArchive;

/**
 * Unit tests for \core_files\local\archive_writer\zip_writer.
 *
 * @coversDefaultClass \core_files\local\archive_writer\zip_writer
 */
class zip_writer_testcase extends advanced_testcase {

    /**
     * Test add_file_from_filepath().
     */
    public function test_add_file_from_filepath(): void {
        global $CFG;

        $pathtofileinzip = '/some/made/up/name.txt';
        $filetoadd = $CFG->dirroot . '/files/tests/fixtures/awesome_file.txt';

        $zipwriter = archive_writer::get_file_writer('test.zip', archive_writer::ZIP_WRITER);
        $zipwriter->add_file_from_filepath($pathtofileinzip, $filetoadd);
        $zipwriter->finish();

        $pathtozip = $zipwriter->get_path_to_zip();
        $zip = new ZipArchive();
        $opened = $zip->open($pathtozip);
        $this->assertTrue($opened);

        $pathtofileinzip = $zipwriter->sanitise_filepath($pathtofileinzip);

        $this->assertEquals("Hey, this is an awesome text file. Hello! :)", $zip->getFromName($pathtofileinzip));
    }

    /**
     * Test add_file_from_string().
     */
    public function test_add_file_from_string(): void {
        $pathtofileinzip = "/path/to/my/awesome/file.txt";
        $mycontent = "This is some real awesome content, ya dig?";

        $zipwriter = archive_writer::get_file_writer('test.zip', archive_writer::ZIP_WRITER);
        $zipwriter->add_file_from_string($pathtofileinzip, $mycontent);
        $zipwriter->finish();

        $pathtozip = $zipwriter->get_path_to_zip();
        $zip = new ZipArchive();
        $opened = $zip->open($pathtozip);
        $this->assertTrue($opened);

        $pathtofileinzip = $zipwriter->sanitise_filepath($pathtofileinzip);

        $this->assertEquals($mycontent, $zip->getFromName($pathtofileinzip));
    }

    /**
     * Test add_file_from_stream().
     */
    public function test_add_file_from_stream(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Add a file to the intro.
        $filerecord = [
            'contextid' => context_module::instance($assign->cmid)->id,
            'component' => 'mod_assign',
            'filearea'  => 'intro',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'fileintro.txt',
        ];
        $fs = get_file_storage();
        $storedfile = $fs->create_file_from_string($filerecord, 'Contents for the assignment, yeow!');

        $pathtofileinzip = $storedfile->get_filepath() . $storedfile->get_filename();

        $zipwriter = archive_writer::get_file_writer('test.zip', archive_writer::ZIP_WRITER);
        $zipwriter->add_file_from_stream($pathtofileinzip, $storedfile->get_content_file_handle());
        $zipwriter->finish();

        $pathtozip = $zipwriter->get_path_to_zip();
        $zip = new ZipArchive();
        $opened = $zip->open($pathtozip);
        $this->assertTrue($opened);

        $pathtofileinzip = $zipwriter->sanitise_filepath($pathtofileinzip);

        $this->assertEquals($storedfile->get_content(), $zip->getFromName($pathtofileinzip));
    }

    /**
     * Test add_file_from_stored_file().
     */
    public function test_add_file_from_stored_file(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Add a file to the intro.
        $filerecord = [
            'contextid' => context_module::instance($assign->cmid)->id,
            'component' => 'mod_assign',
            'filearea'  => 'intro',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'fileintro.txt',
        ];
        $fs = get_file_storage();
        $storedfile = $fs->create_file_from_string($filerecord, 'Contents for the assignment, yeow!');

        $pathtofileinzip = $storedfile->get_filepath() . $storedfile->get_filename();

        $zipwriter = archive_writer::get_file_writer('test.zip', archive_writer::ZIP_WRITER);
        $zipwriter->add_file_from_stored_file($pathtofileinzip, $storedfile);
        $zipwriter->finish();

        $pathtozip = $zipwriter->get_path_to_zip();
        $zip = new ZipArchive();
        $opened = $zip->open($pathtozip);
        $this->assertTrue($opened);

        $pathtofileinzip = $zipwriter->sanitise_filepath($pathtofileinzip);

        $this->assertEquals($storedfile->get_content(), $zip->getFromName($pathtofileinzip));
    }

    /**
     * Test sanitise_filepath().
     *
     * @param string $providedfilepath The provided file path.
     * @param string $expectedfilepath The expected file path.
     * @dataProvider sanitise_filepath_provider
     */
    public function test_sanitise_filepath(string $providedfilepath, string $expectedfilepath): void {
        $zipwriter = archive_writer::get_stream_writer('path/to/file.txt', archive_writer::ZIP_WRITER);
        $this->assertEquals($expectedfilepath, $zipwriter->sanitise_filepath($providedfilepath));
    }

    /**
     * Data provider for test_sanitise_filepath.
     *
     * @return array
     */
    public function sanitise_filepath_provider(): array {
        return [
            ['a../../file/path', 'a../file/path'],
            ['a./file/path', 'a./file/path'],
            ['../file/path', 'file/path'],
            ['foo/bar/', 'foo/bar/'],
            ['\\\\\\a\\\\\\file\\\\\\path', 'a/file/path'],
            ['//a//file/////path////', 'a/file/path/']
        ];
    }
}
