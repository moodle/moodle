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
 * Unit tests for core\content\zipwriter.
 *
 * @package     core
 * @category    test
 * @copyright   2020 Simey Lameze <simey@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

declare(strict_types=1);

namespace core\content\export;

use advanced_testcase;
use context_module;
use context_system;
use ZipArchive;

/**
 * Unit tests for core\content\zipwriter.
 *
 * @coversDefaultClass \core\content\export\zipwriter
 */
class zipwriter_test extends advanced_testcase {

    /**
     * Test add_file_from_stored_file().
     */
    public function test_add_file_from_stored_file(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $folder = $this->getDataGenerator()->create_module('folder', ['course' => $course->id]);
        $context = \context_course::instance($course->id);

        // Add a file to the intro.
        $fileintroname = "fileintro.txt";
        $filerecord = [
            'contextid' => context_module::instance($folder->cmid)->id,
            'component' => 'mod_folder',
            'filearea'  => 'intro',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $fileintroname,
        ];
        $fs = get_file_storage();
        $storedfile = $fs->create_file_from_string($filerecord, 'image contents');

        $pathinfolder = $storedfile->get_filepath() . $storedfile->get_filename();

        $zipwriter = zipwriter::get_file_writer('test.zip');
        $zipwriter->add_file_from_stored_file($context, $pathinfolder, $storedfile);
        $zipwriter->finish();

        $zipfilepath = $zipwriter->get_file_path();
        $zip = new ZipArchive();
        $opened = $zip->open($zipfilepath);
        $this->assertTrue($opened);

        $pathinzip = $zipwriter->get_context_path($context, $pathinfolder);
        $this->assertEquals($storedfile->get_content(), $zip->getFromName($pathinzip));
    }

    /**
     * Test add_file_from_string().
     */
    public function test_add_file_from_string(): void {
        $context = context_system::instance();

        $pathinfolder = "/path/to/my/file.txt";
        $mycontent = "Zippidy do dah";

        $zipwriter = zipwriter::get_file_writer('test.zip');
        $zipwriter->add_file_from_string($context, $pathinfolder, $mycontent);
        $zipwriter->finish();

        $zipfilepath = $zipwriter->get_file_path();
        $zip = new ZipArchive();
        $opened = $zip->open($zipfilepath);
        $this->assertTrue($opened);

        $pathinzip = ltrim($zipwriter->get_context_path($context, $pathinfolder), '/');
        $this->assertEquals($mycontent, $zip->getFromName($pathinzip));
    }

    /**
     * Test get_file_writer().
     */
    public function test_get_file_writer(): void {
        $zipwriter = zipwriter::get_file_writer('test.zip');
        $this->assertInstanceOf(zipwriter::class, $zipwriter);
        $this->assertTrue(file_exists($zipwriter->get_file_path()));
    }

    /**
     * Test get_stream_writer().
     */
    public function test_get_stream_writer(): void {
        $zipwriter = zipwriter::get_stream_writer('test.zip');
        $this->assertInstanceOf(zipwriter::class, $zipwriter);
    }
}
