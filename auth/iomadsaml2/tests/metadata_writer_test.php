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
 * Testcase class for metadata_writer class.
 *
 * @package    auth_iomadsaml2
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use auth_iomadsaml2\metadata_writer;

/**
 * Testcase class for metadata_writer class.
 *
 * @package    auth_iomadsaml2
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_metadata_writer_testcase extends basic_testcase {

    public function test_write_default_path() {
        global $CFG;

        $filename = 'idp.xml';
        $content = 'Test data';

        $writer = new metadata_writer();
        $writer->write($filename, $content);

        $this->assertEquals($content, file_get_contents("$CFG->dataroot/iomadsaml2/idp.xml"));
    }

    public function test_write_empty_filename() {
        $filename = '';
        $content = 'Test data';

        $writer = new metadata_writer();
        $this->expectException(\moodle_exception::class);
        $writer->write($filename, $content);
    }

    public function test_write_non_dataroot_path() {
        global $CFG;

        $filename = 'idp.xml';
        $content = 'Test data';

        $nondatarootpath = '/temp/yada/blah/';

        $writer = new metadata_writer($nondatarootpath);
        $writer->write($filename, $content);

        // Backwards compatibility with older PHPUnit - use old assertFile method.
        if (method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist("/temp/yada/blah/idp.xml");
        } else {
            $this->assertFileNotExists("/temp/yada/blah/idp.xml");
        }
        $this->assertEquals($content, file_get_contents("$CFG->dataroot/iomadsaml2/idp.xml"));
    }

    public function test_write_trailing_slash() {
        global $CFG;

        $filename = 'idp.xml';
        $filename2 = 'idp2.xml';
        $content = 'Test data';
        $pathtrailingslash = "$CFG->dataroot/iomadsaml2/";
        $pathnotrailingslash = "$CFG->dataroot/iomadsaml2";

        $writer = new metadata_writer($pathtrailingslash);
        $writer->write($filename, $content);

        $writer2 = new metadata_writer($pathnotrailingslash);
        $writer2->write($filename2, $content);

        $this->assertFileExists($pathtrailingslash . $filename);
        $this->assertFileExists($pathnotrailingslash . '/' . $filename2);
    }
}
