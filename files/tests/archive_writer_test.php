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

use advanced_testcase;
use core_files\local\archive_writer\zip_writer;

/**
 * Unit tests for \core_files\archive_writer.
 *
 * @package core_files
 * @category test
 * @copyright 2020 Mark Nelson <mdjnelson@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @covers \core_files\archive_writer
 */
class archive_writer_test extends advanced_testcase {

    /**
     * Test get_file_writer().
     */
    public function test_get_file_writer(): void {
        $zipwriter = archive_writer::get_file_writer('file.zip', archive_writer::ZIP_WRITER);
        $this->assertInstanceOf(zip_writer::class, $zipwriter);
        $this->assertTrue(file_exists($zipwriter->get_path_to_zip()));
    }

    /**
     * Test get_stream_writer().
     */
    public function test_get_stream_writer(): void {
        $zipwriter = archive_writer::get_stream_writer('path/to/file.txt', archive_writer::ZIP_WRITER);
        $this->assertInstanceOf(zip_writer::class, $zipwriter);
    }
}
